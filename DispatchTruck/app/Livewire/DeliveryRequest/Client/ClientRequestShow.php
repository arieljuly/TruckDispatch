<?php
namespace App\Livewire\DeliveryRequest\Client;

use App\Models\DeliveryRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClientRequestShow extends Component
{
    public $deliveryRequest;
    public $requestId;
    public $fuelItems = [];
    public $purchaseOrder = null;
    public $debugInfo = [];

    public function mount($id)
    {
        $this->requestId = $id;
        $this->deliveryRequest = DeliveryRequest::with([
            'area',
            'station',
            'requester',
        ])->findOrFail($id);

        // Ensure user owns this request
        if ($this->deliveryRequest->requested_by !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Load fuel items
        $this->loadFuelItems();
    }

    public function loadFuelItems()
    {
        $this->fuelItems = [];

        // Get additional_items - it might be a string or array
        $additionalData = $this->deliveryRequest->additional_items;

        // If it's a string, decode it
        if (is_string($additionalData)) {
            $additionalData = json_decode($additionalData, true);
        }

        // If we have a purchase_order_id, load items from purchase_order_items table
        if (isset($additionalData['purchase_order_id'])) {
            $purchaseOrderId = $additionalData['purchase_order_id'];

            // Load all items from purchase_order_items table
            $poItems = PurchaseOrderItem::with('fuelType')
                ->where('purchase_order_id', $purchaseOrderId)
                ->get();

            foreach ($poItems as $poItem) {
                $this->fuelItems[] = (object) [
                    'fuel_type_id' => $poItem->fuel_type_id,
                    'fuel_type_name' => $poItem->fuelType->fuel_name ?? 'Unknown',
                    'fuel_type_code' => $poItem->fuelType->fuel_code ?? 'N/A',
                    'requested_liters' => (float) $poItem->qty_liters,
                    'fulfilled_liters' => (float) $poItem->delivered_ltrs,
                    'remaining_liters' => (float) $poItem->remaining_liters,
                ];
            }

            // Also load purchase order details
            $this->purchaseOrder = PurchaseOrder::find($purchaseOrderId);
        }
        // Fallback: try to get items from the items array if it exists
        elseif (isset($additionalData['items']) && is_array($additionalData['items'])) {
            foreach ($additionalData['items'] as $item) {
                $this->fuelItems[] = (object) [
                    'fuel_type_id' => $item['fuel_type_id'] ?? null,
                    'fuel_type_name' => $item['fuel_name'] ?? $item['fuel_type_name'] ?? 'Unknown',
                    'fuel_type_code' => $item['fuel_code'] ?? $item['fuel_type_code'] ?? 'N/A',
                    'requested_liters' => (float) ($item['quantity'] ?? $item['requested_liters'] ?? 0),
                    'fulfilled_liters' => 0,
                    'remaining_liters' => (float) ($item['quantity'] ?? $item['requested_liters'] ?? 0),
                ];
            }
        }

        // If still no items but we have requested_liters, create a fallback
        if (empty($this->fuelItems) && $this->deliveryRequest->requested_liters > 0) {
            $this->fuelItems[] = (object) [
                'fuel_type_id' => null,
                'fuel_type_name' => 'Fuel Product',
                'fuel_type_code' => 'N/A',
                'requested_liters' => (float) $this->deliveryRequest->requested_liters,
                'fulfilled_liters' => (float) $this->deliveryRequest->fulfilled_liters,
                'remaining_liters' => (float) $this->deliveryRequest->remaining_liters,
            ];
        }

        // Store debug info
        $this->debugInfo = [
            'additional_items_raw' => $this->deliveryRequest->additional_items,
            'additional_items_decoded' => $additionalData,
            'fuel_items_count' => count($this->fuelItems),
            'purchase_order_id' => $additionalData['purchase_order_id'] ?? null,
        ];
    }

    public function confirmCancel()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Cancel Delivery Request?',
            'text' => 'Are you sure you want to cancel this delivery request? This action cannot be undone.',
            'icon' => 'warning',
            'confirmButtonText' => 'Yes, cancel it!',
            'cancelButtonText' => 'No, keep it'
        ]);
    }

    public function confirmed()
    {
        if ($this->deliveryRequest->status === 'pending') {
            $this->deliveryRequest->status = 'cancelled';
            $this->deliveryRequest->save();

            session()->flash('message', 'Request cancelled successfully.');
            session()->flash('alert-type', 'success');

            return redirect()->route('client.delivery.index');
        }
    }

    public function getTotalRequestedLiters()
    {
        return array_sum(array_column($this->fuelItems, 'requested_liters'));
    }

    public function getTotalFulfilledLiters()
    {
        return array_sum(array_column($this->fuelItems, 'fulfilled_liters'));
    }

    public function getTotalRemainingLiters()
    {
        return array_sum(array_column($this->fuelItems, 'remaining_liters'));
    }

    public function render()
    {
        return view('livewire.delivery-request.client.client-request-show', [
            'request' => $this->deliveryRequest,
            'fuelItems' => $this->fuelItems,
            'purchaseOrder' => $this->purchaseOrder,
            'debugInfo' => $this->debugInfo,
            'totalRequested' => $this->getTotalRequestedLiters(),
            'totalFulfilled' => $this->getTotalFulfilledLiters(),
            'totalRemaining' => $this->getTotalRemainingLiters(),
        ]);
    }
}