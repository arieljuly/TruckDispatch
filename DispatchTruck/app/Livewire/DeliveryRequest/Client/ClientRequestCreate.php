<?php

namespace App\Livewire\DeliveryRequest\Client;

use App\Models\Area;
use App\Models\DeliveryRequest;
use App\Models\FuelType;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Station;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClientRequestCreate extends Component
{
    public $areas = [];
    public $stations = [];
    public $fuelTypes = [];

    // Form fields
    public $selected_area_id = null;
    public $selected_station_id = null;
    public $selected_fuels = [];
    public $request_delivery_date = null;
    public $notes = null;

    // UI state
    public $step = 1;
    public $debug_message = '';
    public $showSuccessAlert = false;
    public $successMessage = '';

    protected $rules = [
        'selected_area_id' => 'required|exists:areas,id',
        'selected_station_id' => 'required|exists:stations,id',
        'selected_fuels' => 'required|array|min:1',
        'selected_fuels.*.fuel_type_id' => 'required|exists:fuel_types,id',
        'selected_fuels.*.quantity' => 'required|numeric|min:0.001',
        'request_delivery_date' => 'required|date|after:today',
    ];

    protected $messages = [
        'selected_area_id.required' => 'Please select an area.',
        'selected_station_id.required' => 'Please select a station.',
        'selected_fuels.required' => 'Please add at least one fuel item.',
        'selected_fuels.min' => 'Please add at least one fuel item.',
        'selected_fuels.*.quantity.required' => 'Please enter quantity.',
        'selected_fuels.*.quantity.min' => 'Quantity must be greater than 0.',
        'request_delivery_date.required' => 'Please select requested delivery date.',
        'request_delivery_date.after' => 'Delivery date must be in the future.',
    ];

    public function mount()
    {
        $clientStations = Station::where('user_id', Auth::id())
            ->where('status', 'active')
            ->get();

        $areaIds = $clientStations->pluck('area_id')->filter()->unique()->values();

        if ($areaIds->isEmpty()) {
            $this->debug_message = "You have " . $clientStations->count() . " stations but none have area_id assigned.";
            $this->areas = collect();
        } else {
            $this->areas = Area::whereIn('id', $areaIds)
                ->where('status', 'active')
                ->get();
        }

        $this->fuelTypes = FuelType::where('status', 'active')->get();
        $this->request_delivery_date = now()->addDays(3)->format('Y-m-d');

        if ($this->areas->isNotEmpty() && !$this->selected_area_id) {
            $this->selected_area_id = $this->areas->first()->id;
            $this->loadStationsForArea();
        }
    }

    public function loadStationsForArea()
    {
        if ($this->selected_area_id) {
            $this->stations = Station::where('area_id', $this->selected_area_id)
                ->where('user_id', Auth::id())
                ->where('status', 'active')
                ->get();

            $this->selected_station_id = null;
            $this->selected_fuels = [];
        }
    }

    public function areaChanged()
    {
        $this->loadStationsForArea();
    }

    public function stationChanged()
    {
        $this->selected_fuels = [];
    }

    public function addFuelToRequest($fuelTypeId)
    {
        $fuelType = $this->fuelTypes->firstWhere('id', $fuelTypeId);

        if (!$fuelType) {
            return;
        }

        $existingIndex = collect($this->selected_fuels)->search(function ($selected) use ($fuelTypeId) {
            return $selected['fuel_type_id'] == $fuelTypeId;
        });

        if ($existingIndex !== false) {
            session()->flash('message', 'This fuel type is already added.');
            session()->flash('alert-type', 'warning');
            return;
        }

        $this->selected_fuels[] = [
            'fuel_type_id' => $fuelTypeId,
            'fuel_name' => $fuelType->fuel_name,
            'fuel_code' => $fuelType->fuel_code,
            'quantity' => null,
        ];
    }

    public function updateFuelQuantity($index, $quantity)
    {
        if (isset($this->selected_fuels[$index])) {
            if ($quantity <= 0) {
                $this->addError("selected_fuels.{$index}.quantity", "Quantity must be greater than 0");
            } else {
                $this->resetErrorBag("selected_fuels.{$index}.quantity");
                $this->selected_fuels[$index]['quantity'] = $quantity;
            }
        }
    }

    public function removeSelectedFuel($index)
    {
        unset($this->selected_fuels[$index]);
        $this->selected_fuels = array_values($this->selected_fuels);
    }

    public function getTotalQuantityProperty()
    {
        return array_sum(array_column($this->selected_fuels, 'quantity'));
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'selected_area_id' => 'required',
                'selected_station_id' => 'required',
            ]);

            $station = Station::where('id', $this->selected_station_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$station) {
                $this->addError('selected_station_id', 'You are not authorized for this station.');
                return;
            }

            $this->step = 2;
        } elseif ($this->step === 2) {
            $this->validate([
                'selected_fuels' => 'required|array|min:1',
            ]);

            foreach ($this->selected_fuels as $index => $fuel) {
                if (empty($fuel['quantity']) || $fuel['quantity'] <= 0) {
                    $this->addError("selected_fuels.{$index}.quantity", "Please enter valid quantity");
                    return;
                }
            }

            $this->step = 3;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit()
    {
        $this->validate();

        foreach ($this->selected_fuels as $index => $fuel) {
            if (empty($fuel['quantity']) || $fuel['quantity'] <= 0) {
                $this->addError("selected_fuels.{$index}.quantity", "Required");
                return;
            }
        }

        $station = Station::where('id', $this->selected_station_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$station) {
            session()->flash('message', 'Unauthorized: You do not own this station.');
            session()->flash('alert-type', 'error');
            return;
        }

        DB::beginTransaction();

        try {
            $poNumber = 'PO-' . date('Ymd') . '-' . strtoupper(uniqid());

            $purchaseOrder = PurchaseOrder::create([
                'user_id' => Auth::id(),
                'station_id' => $this->selected_station_id,
                'po_number' => $poNumber,
                'order_date' => now(),
                'request_delivery_date' => $this->request_delivery_date,
                'status' => 'pending',
            ]);

            foreach ($this->selected_fuels as $fuel) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'fuel_type_id' => $fuel['fuel_type_id'],
                    'qty_liters' => $fuel['quantity'],
                    'delivered_ltrs' => 0,
                    'unit_price' => 0,
                    'status' => 'pending',
                ]);
            }

            $totalLiters = $this->total_quantity;

            $deliveryRequest = DeliveryRequest::create([
                'area_id' => $this->selected_area_id,
                'station_id' => $this->selected_station_id,
                'purchase_order_item_id' => null,
                'requested_liters' => $totalLiters,
                'priority' => 'medium',
                'status' => 'pending',
                'requested_by' => Auth::id(),
                'fulfilled_liters' => 0,
                'deadline' => $this->request_delivery_date . ' 23:59:59',
                'additional_items' => json_encode([
                    'purchase_order_id' => $purchaseOrder->id,
                    'po_number' => $poNumber,
                    'items' => $this->selected_fuels,
                    'notes' => $this->notes,
                ]),
                'notes' => $this->notes,
            ]);

            DB::commit();

            // Set success message and flag for SweetAlert
            $this->successMessage = 'Purchase Order #' . $poNumber . ' has been created successfully!';
            $this->showSuccessAlert = true;

            // Dispatch browser event for SweetAlert
            $this->dispatch('order-submitted', ['poNumber' => $poNumber]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message', 'Error: ' . $e->getMessage());
            session()->flash('alert-type', 'error');
        }
    }

    public function render()
    {
        return view('livewire.delivery-request.client.client-request-create');
    }
}