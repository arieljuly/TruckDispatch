<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserCreate extends Component
{
    public $showCreateModal = false;

    public $first_name = '';
    public $middle_name = '';
    public $last_name = '';
    public $email = '';
    public $phone_number = '';
    public $password = '';
    public $password_confirmation = '';
    public $role_id = '';
    public $status = 'active';
    public $company_name = '';
    public $address = '';
    public $preferred_contact_method = 'email';

    public $roles;
    public $showClientFields = false;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone_number' => 'nullable|string|max:20',
        'password' => 'required|min:8|confirmed',
        'role_id' => 'required|exists:roles,id',
        'status' => 'required|in:active,inactive,suspended',
        'company_name' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:500',
        'preferred_contact_method' => 'required|in:email,phone,both',
    ];

    protected $messages = [
        'role_id.required' => 'Please select a role for the user.',
        'email.unique' => 'This email address is already registered.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function resetCreateForm()
    {
        $this->reset([
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'phone_number',
            'password',
            'password_confirmation',
            'role_id',
            'status',
            'company_name',
            'address',
            'preferred_contact_method',
            'showClientFields'
        ]);
        $this->status = 'active';
        $this->preferred_contact_method = 'email';
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedRoleId($value)
    {
        $selectedRole = Role::find($value);
        $this->showClientFields = $selectedRole && $selectedRole->role_name === 'client';
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->dispatch('closeCreateModal');
    }

    public function createUser()
    {
        $this->validate();

        $user = User::create([
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'password' => Hash::make($this->password),
            'role_id' => $this->role_id,
            'status' => $this->status,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'preferred_contact_method' => $this->preferred_contact_method,
        ]);

        $this->resetCreateForm();

        // Dispatch SweetAlert event
        $this->dispatch('showSuccessAlert', 'User created successfully!');
        $this->dispatch('userCreated');
        $this->dispatch('refreshUsers');
        $this->dispatch('closeCreateModal');

        session()->flash('message', 'User created successfully!');
    }

    public function render()
    {
        return view('livewire.admin.user-management.user-create');
    }
}