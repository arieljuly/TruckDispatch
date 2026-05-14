<?php

namespace App\Livewire\DriverManagement;

use App\Models\Driver;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class DriverList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $showCreateModal = false;

    // Form fields
    public $user_id = '';
    public $licensed_number = '';
    public $status = 'available';
    public $users = [];

    protected $rules = [
        'user_id' => 'required|exists:users,id|unique:drivers,user_id',
        'licensed_number' => 'required|string|max:50|unique:drivers,licensed_number',
        'status' => 'required|in:available,on-duty,off-duty,inactive',
    ];

    protected $listeners = ['refreshDrivers' => '$refresh'];

    public function mount()
    {
        $this->loadAvailableUsers();
    }

    public function loadAvailableUsers()
    {
        // Get users with role 'driver' that are not yet assigned as drivers
        $this->users = User::whereHas('role', function ($query) {
            $query->where('role_name', 'driver');
        })->whereDoesntHave('driver')->get();
    }

    public function openCreateModal()
    {
        $this->reset(['user_id', 'licensed_number', 'status']);
        $this->loadAvailableUsers();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->reset(['user_id', 'licensed_number', 'status']);
    }

    public function createDriver()
    {
        $this->validate();

        Driver::create([
            'user_id' => $this->user_id,
            'licensed_number' => $this->licensed_number,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Driver created successfully!');
        $this->closeCreateModal();
        $this->dispatch('refreshDrivers');
        $this->loadAvailableUsers();
    }

    /**
     * Check if driver can be deactivated
     * This is called by the frontend before showing the confirmation dialog
     */
    public function canDeactivateDriver($id)
    {
        try {
            $driver = Driver::findOrFail($id);

            // Check if driver has active assignment
            if ($driver->currentAssignment) {
                return false;
            }

            // Check if driver is already inactive
            if ($driver->status === 'inactive') {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Deactivate a driver (change status to inactive and set deactivated_at)
     */
    public function deleteDriver($id)
    {
        try {
            $driver = Driver::findOrFail($id);

            // Double-check before deactivating
            if ($driver->currentAssignment) {
                $this->dispatch('operationError', message: 'Cannot deactivate driver with active truck assignment.');
                return;
            }

            if ($driver->status === 'inactive') {
                $this->dispatch('operationError', message: 'Driver is already deactivated.');
                return;
            }

            // Change status to inactive and set deactivated_at
            $driver->update([
                'status' => 'inactive',
                'deactivated_at' => now(),
            ]);

            $this->dispatch('refreshDrivers');
            session()->flash('message', 'Driver deactivated successfully!');

        } catch (\Exception $e) {
            $this->dispatch('operationError', message: 'Failed to deactivate driver. Please try again.');
            \Log::error('Driver deactivation error: ' . $e->getMessage());
        }
    }

    /**
     * Reactivate a deactivated driver (change status to available and set deactivated_at to null)
     */
    public function reactivateDriver($id)
    {
        try {
            $driver = Driver::findOrFail($id);

            if ($driver->status !== 'inactive') {
                $this->dispatch('operationError', message: 'Driver is not deactivated.');
                return;
            }

            // Change status to available and set deactivated_at to null
            $driver->update([
                'status' => 'available',
                'deactivated_at' => null,
            ]);

            $this->dispatch('refreshDrivers');
            session()->flash('message', 'Driver reactivated successfully!');

        } catch (\Exception $e) {
            $this->dispatch('operationError', message: 'Failed to reactivate driver. Please try again.');
            \Log::error('Driver reactivation error: ' . $e->getMessage());
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $drivers = Driver::query()
            ->with(['user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('user', function ($userQuery) {
                        $userQuery->where('first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    })->orWhere('licensed_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.driver-management.driver-list', [
            'drivers' => $drivers
        ]);
    }
}