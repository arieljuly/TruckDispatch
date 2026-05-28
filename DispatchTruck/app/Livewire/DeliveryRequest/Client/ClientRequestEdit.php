<?php
namespace App\Livewire\DeliveryRequest\Client;

use App\Models\DeliveryRequest;
use App\Models\Area;
use App\Models\Station;
use App\Models\FuelType;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClientRequestEdit extends Component
{
    public $requestId;
    public $deliveryRequest;

    // Form fields
    public $area_id;
    public $station_id;
    public $priority;
    public $deadline;
    public $notes;

    // Fuel items
    public $selected_fuels = [];
    public $fuelTypes = [];
    public $purchase_order_id = null;
    public $po_number = null;

    // Data collections
    public $areas = [];
    public $stations = [];

    protected $rules = [
        'area_id' => 'required|exists:areas,id',
        'station_id' => 'required|exists:stations,id',
        'priority' => 'required|in:low,medium,high,urgent',
        'deadline' => 'required|date|after:now',
        'selected_fuels' => 'required|array|min:1',
        'selected_fuels.*.fuel_type_id' => 'required',
        'selected_fuels.*.quantity' => 'required|numeric|min:0.001',
    ];

    protected $messages = [
        'area_id.required' => 'Please select an area.',
        'station_id.required' => 'Please select a fuel station.',
        'priority.required' => 'Please select a priority level.',
        'deadline.required' => 'Please select a deadline.',
        'deadline.after' => 'Deadline must be in the future.',
        'selected_fuels.required' => 'Please add at least one fuel item.',
        'selected_fuels.*.quantity.min' => 'Quantity must be greater than 0.',
    ];

    public function mount($id)
    {
        $this->requestId = $id;
        $this->deliveryRequest = DeliveryRequest::findOrFail($id);

        // Ensure user owns this request
        if ($this->deliveryRequest->requested_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Ensure request is still pending
        if ($this->deliveryRequest->status !== 'pending') {
            session()->flash('message', 'Only pending requests can be edited.');
            session()->flash('alert-type', 'error');
            return redirect()->route('client.delivery.index');
        }

        // Load data
        $this->loadAreas();
        $this->loadFuelTypes();

        // Populate form with existing data
        $this->area_id = $this->deliveryRequest->area_id;
        $this->station_id = $this->deliveryRequest->station_id;
        $this->priority = $this->deliveryRequest->priority;
        $this->deadline = $this->deliveryRequest->deadline ? $this->deliveryRequest->deadline->format('Y-m-d\TH:i') : null;
        $this->notes = $this->deliveryRequest->notes;

        // Load stations for the selected area
        if ($this->area_id) {
            $this->loadStationsForArea();
        }

        // Load existing fuel items
        $this->loadExistingFuelItems();
    }

    public function loadAreas()
    {
        $this->areas = Area::where('status', 'active')->orderBy('area_name')->get();
    }

    public function loadStationsForArea()
    {
        if ($this->area_id) {
            $this->stations = Station::where('area_id', $this->area_id)
                ->where('user_id', Auth::id())
                ->where('status', 'active')
                ->get();
        }
    }

    public function loadFuelTypes()
    {
        $this->fuelTypes = FuelType::where('status', 'active')->orderBy('fuel_name')->get();
    }

    public function loadExistingFuelItems()
    {
        $this->selected_fuels = [];

        // Get additional_items - it might be a string or array
        $additionalData = $this->deliveryRequest->additional_items;

        // If it's a string, decode it
        if (is_string($additionalData)) {
            $additionalData = json_decode($additionalData, true);
        }

        // If we have a purchase_order_id, load items from purchase_order_items table
        if (isset($additionalData['purchase_order_id'])) {
            $this->purchase_order_id = $additionalData['purchase_order_id'];
            $this->po_number = $additionalData['po_number'] ?? null;

            // Load items from purchase_order_items table
            $poItems = PurchaseOrderItem::with('fuelType')
                ->where('purchase_order_id', $this->purchase_order_id)
                ->get();

            foreach ($poItems as $poItem) {
                $this->selected_fuels[] = [
                    'fuel_type_id' => $poItem->fuel_type_id,
                    'fuel_name' => $poItem->fuelType->fuel_name ?? 'Unknown',
                    'fuel_code' => $poItem->fuelType->fuel_code ?? 'N/A',
                    'quantity' => (float) $poItem->qty_liters,
                ];
            }
        }
        // Fallback: try to get items from the items array
        elseif (isset($additionalData['items']) && is_array($additionalData['items'])) {
            foreach ($additionalData['items'] as $item) {
                $this->selected_fuels[] = [
                    'fuel_type_id' => $item['fuel_type_id'],
                    'fuel_name' => $item['fuel_name'],
                    'fuel_code' => $item['fuel_code'],
                    'quantity' => (float) $item['quantity'],
                ];
            }
        }
    }

    public function areaChanged()
    {
        $this->loadStationsForArea();
        $this->station_id = null;
    }

    public function addFuelItem($fuelTypeId)
    {
        $fuelType = $this->fuelTypes->firstWhere('id', $fuelTypeId);

        if (!$fuelType) {
            return;
        }

        // Check if already added
        $exists = collect($this->selected_fuels)->contains('fuel_type_id', $fuelTypeId);
        if ($exists) {
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

    public function removeFuelItem($index)
    {
        unset($this->selected_fuels[$index]);
        $this->selected_fuels = array_values($this->selected_fuels);
    }

    public function updateQuantity($index, $quantity)
    {
        if (isset($this->selected_fuels[$index])) {
            $this->selected_fuels[$index]['quantity'] = $quantity;
        }
    }

    public function getTotalQuantityProperty()
    {
        return array_sum(array_column($this->selected_fuels, 'quantity'));
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            // Update purchase order items if we have a purchase order
            if ($this->purchase_order_id) {
                // Delete existing items
                PurchaseOrderItem::where('purchase_order_id', $this->purchase_order_id)->delete();

                // Create new items
                foreach ($this->selected_fuels as $fuel) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $this->purchase_order_id,
                        'fuel_type_id' => $fuel['fuel_type_id'],
                        'qty_liters' => $fuel['quantity'],
                        'delivered_ltrs' => 0,
                        'unit_price' => 0,
                        'status' => 'pending',
                    ]);
                }
            }

            // Prepare additional_items JSON (as an array, will be cast to JSON by model)
            $additionalItems = [
                'purchase_order_id' => $this->purchase_order_id,
                'po_number' => $this->po_number,
                'items' => $this->selected_fuels,
                'notes' => $this->notes,
            ];

            // Update the delivery request
            $this->deliveryRequest->update([
                'area_id' => $this->area_id,
                'station_id' => $this->station_id,
                'requested_liters' => $this->total_quantity,
                'priority' => $this->priority,
                'deadline' => $this->deadline,
                'notes' => $this->notes,
                'additional_items' => $additionalItems,
                'purchase_order_item_id' => null,
            ]);

            DB::commit();

            session()->flash('message', 'Delivery request updated successfully!');
            session()->flash('alert-type', 'success');

            return redirect()->route('client.delivery.show', $this->deliveryRequest->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('message', 'Error updating request: ' . $e->getMessage());
            session()->flash('alert-type', 'error');
        }
    }

    public function cancel()
    {
        return redirect()->route('client.delivery.show', $this->deliveryRequest->id);
    }

    public function render()
    {
        return view('livewire.delivery-request.client.client-request-edit');
    }
}