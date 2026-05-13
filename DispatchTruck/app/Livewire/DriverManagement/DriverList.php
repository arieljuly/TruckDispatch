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
        'status' => 'required|in:available,on-duty,off-duty',
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

    public function deleteDriver($id)
    {
        $driver = Driver::findOrFail($id);

        // Check if driver has active assignment
        if ($driver->currentAssignment) {
            session()->flash('error', 'Cannot delete driver with active truck assignment.');
            return;
        }

        $driver->delete();
        session()->flash('message', 'Driver deleted successfully!');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $drivers = Driver::query()
            ->with(['user'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhere('licensed_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->paginate($this->perPage);

        return view('livewire.driver-management.driver-list', [
            'drivers' => $drivers
        ]);
    }
}