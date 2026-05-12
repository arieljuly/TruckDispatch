<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class UserShow extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $showCreateModal = false;

    protected $paginationTheme = 'tailwind';

    protected $listeners = [
        'userUpdated' => 'refreshUsers',
        'userCreated' => 'refreshUsers',
        'closeCreateModal' => 'closeCreateModal'
    ];

    public function refreshUsers()
    {
        $this->resetPage();
        $this->dispatch('refresh');
    }

    public function openCreateModal()
    {
        Log::info('Opening create modal');
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        Log::info('Closing create modal');
        $this->showCreateModal = false;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function getUsersProperty()
    {
        return User::query()
            ->with('role')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($query) {
                $query->whereHas('role', function ($q) {
                    $q->where('role_name', $this->roleFilter);
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $user->delete();
        session()->flash('message', 'User deleted successfully.');
        $this->dispatch('userUpdated');
    }

    public function render()
    {
        Log::info('Render the show');
        return view('livewire.admin.user-management.user-show', [
            'users' => $this->users,
        ]);
    }
}