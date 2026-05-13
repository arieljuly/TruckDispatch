<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class TruckShow extends Component
{
    use WithPagination;

    // Modal flags
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showViewModal = false;

    // Form properties
    public $truck_id;
    public $truck_name;
    public $plate_number;
    public $capacity_ltrs;
    public $available_ltrs;
    public $current_area_id;
    public $status;

    // View/Delete properties
    public $viewingTruck = null; // Initialize as null
    public $deletingTruckId;
    public $deletingTruckName;

    // Filters
    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $rules = [
        'truck_name' => 'required|string|max:255',
        'plate_number' => 'required|string|max:50|unique:trucks,plate_number',
        'capacity_ltrs' => 'required|numeric|min:0',
        'available_ltrs' => 'required|numeric|min:0',
        'current_area_id' => 'nullable|exists:areas,id',
        'status' => 'required|in:available,in-transit,maintenance',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($id)
    {
        $truck = Truck::findOrFail($id);
        $this->truck_id = $truck->id;
        $this->truck_name = $truck->truck_name;
        $this->plate_number = $truck->plate_number;
        $this->capacity_ltrs = $truck->capacity_ltrs;
        $this->available_ltrs = $truck->available_ltrs;
        $this->current_area_id = $truck->current_area_id;
        $this->status = $truck->status;
        $this->showEditModal = true;
    }

    public function openViewModal($id)
    {
        // Load the truck data first, then show modal
        $this->viewingTruck = Truck::with('currentArea')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function openDeleteModal($id)
    {
        $truck = Truck::findOrFail($id);
        $this->deletingTruckId = $truck->id;
        $this->deletingTruckName = $truck->truck_name;
        $this->showDeleteModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingTruck = null;
    }

    public function createTruck()
    {
        $this->validate();

        if ($this->available_ltrs > $this->capacity_ltrs) {
            $this->addError('available_ltrs', 'Available liters cannot exceed capacity');
            return;
        }

        Truck::create([
            'truck_name' => $this->truck_name,
            'plate_number' => $this->plate_number,
            'capacity_ltrs' => $this->capacity_ltrs,
            'available_ltrs' => $this->available_ltrs,
            'current_area_id' => $this->current_area_id,
            'status' => $this->status,
        ]);

        $this->showCreateModal = false;
        $this->resetForm();
        session()->flash('message', 'Truck created successfully!');
    }

    public function updateTruck()
    {
        $truck = Truck::findOrFail($this->truck_id);

        $rules = $this->rules;
        $rules['plate_number'] = 'required|string|max:50|unique:trucks,plate_number,' . $this->truck_id;

        $this->validate($rules);

        if ($this->available_ltrs > $this->capacity_ltrs) {
            $this->addError('available_ltrs', 'Available liters cannot exceed capacity');
            return;
        }

        $truck->update([
            'truck_name' => $this->truck_name,
            'plate_number' => $this->plate_number,
            'capacity_ltrs' => $this->capacity_ltrs,
            'available_ltrs' => $this->available_ltrs,
            'current_area_id' => $this->current_area_id,
            'status' => $this->status,
        ]);

        $this->showEditModal = false;
        $this->resetForm();
        session()->flash('message', 'Truck updated successfully!');
    }

    public function deleteTruck()
    {
        $truck = Truck::findOrFail($this->deletingTruckId);

        // Check if truck has any active assignments
        $hasActiveAssignments = \App\Models\TruckAssignment::where('truck_id', $truck->id)
            ->where('status', 'active')
            ->exists();

        if ($hasActiveAssignments) {
            session()->flash('error', 'Cannot delete truck with active assignments!');
            $this->showDeleteModal = false;
            return;
        }

        $truck->delete();
        $this->showDeleteModal = false;
        session()->flash('message', 'Truck deleted successfully!');
    }

    public function resetForm()
    {
        $this->reset([
            'truck_id',
            'truck_name',
            'plate_number',
            'capacity_ltrs',
            'available_ltrs',
            'current_area_id',
            'status'
        ]);
        $this->resetErrorBag();
    }

    public function render()
    {
        Log::info('Rendering TruckShow component with search: ' . $this->search . ' and statusFilter: ' . $this->statusFilter);
        $trucks = Truck::with('currentArea')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('truck_name', 'like', '%' . $this->search . '%')
                        ->orWhere('plate_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $areas = Area::orderBy('area_name')->get();

        return view('livewire.truck-management.truck-show', [
            'trucks' => $trucks,
            'areas' => $areas,
        ])->layout('layouts.app');
    }
}