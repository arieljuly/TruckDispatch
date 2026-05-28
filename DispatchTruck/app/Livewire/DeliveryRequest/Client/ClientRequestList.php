<?php
namespace App\Livewire\DeliveryRequest\Client;

use App\Models\DeliveryRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ClientRequestList extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $priorityFilter = '';
    public $search = '';

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->priorityFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = DeliveryRequest::with(['area', 'station', 'purchaseOrderItem.fuelType'])
            ->where('requested_by', Auth::id());

        // Get all records for stats (without pagination)
        $allRequests = clone $query;
        $stats = [
            'pending' => (clone $allRequests)->where('status', 'pending')->count(),
            'partially_fulfilled' => (clone $allRequests)->where('status', 'partially_fulfilled')->count(),
            'fulfilled' => (clone $allRequests)->where('status', 'fulfilled')->count(),
            'cancelled' => (clone $allRequests)->where('status', 'cancelled')->count(),
        ];

        // Apply filters for paginated results
        $deliveryRequests = $query
            ->when($this->statusFilter, function ($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->when($this->priorityFilter, function ($query) {
                return $query->where('priority', $this->priorityFilter);
            })
            ->when($this->search, function ($query) {
                return $query->where(function ($q) {
                    $q->whereHas('area', function ($areaQuery) {
                        $areaQuery->where('area_name', 'like', '%' . $this->search . '%');
                    })->orWhereHas('station', function ($stationQuery) {
                        $stationQuery->where('station_name', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.delivery-request.client.client-request-list', [
            'deliveryRequests' => $deliveryRequests,
            'stats' => $stats,
        ]);
    }
}