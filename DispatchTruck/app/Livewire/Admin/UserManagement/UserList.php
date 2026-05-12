<?php

namespace App\Livewire\Admin\UserManagement;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserList extends Component
{
    public $showViewModal = false;
    public $showEditModal = false;
    public $selectedUser = null;
    public $userId = null;

    // Edit form properties
    public $edit_first_name = '';
    public $edit_middle_name = '';
    public $edit_last_name = '';
    public $edit_email = '';
    public $edit_phone_number = '';
    public $edit_password = '';
    public $edit_password_confirmation = '';
    public $edit_role_id = '';
    public $edit_status = '';
    public $edit_company_name = '';
    public $edit_address = '';
    public $edit_preferred_contact_method = '';

    public $roles;
    public $showEditClientFields = false;

    protected $listeners = [
        'viewUser' => 'loadUser',
        'editUser' => 'loadEditUser'
    ];

    public function mount()
    {
        Log::info('UserList component mounted');
        $this->roles = Role::all();
        Log::info('Roles loaded', ['roles_count' => $this->roles->count()]);
    }

    public function loadUser($userId)
    {
        Log::info('loadUser called', ['userId' => $userId]);

        $this->userId = $userId;
        $this->selectedUser = User::with('role')->find($userId);

        if ($this->selectedUser) {
            Log::info('User found', [
                'user_id' => $this->selectedUser->id,
                'user_name' => $this->selectedUser->full_name,
                'user_email' => $this->selectedUser->email
            ]);
        } else {
            Log::warning('User not found', ['userId' => $userId]);
        }

        $this->showViewModal = true;
        Log::info('View modal opened', ['showViewModal' => $this->showViewModal]);
    }

    public function loadEditUser($userId)
    {
        Log::info('loadEditUser called', ['userId' => $userId]);

        $this->resetEditForm();
        $this->userId = $userId;

        try {
            $user = User::findOrFail($userId);
            Log::info('User found for edit', [
                'user_id' => $user->id,
                'user_name' => $user->full_name,
                'user_email' => $user->email,
                'user_role' => $user->role_id
            ]);

            // Populate edit form with user data
            $this->edit_first_name = $user->first_name;
            $this->edit_middle_name = $user->middle_name;
            $this->edit_last_name = $user->last_name;
            $this->edit_email = $user->email;
            $this->edit_phone_number = $user->phone_number;
            $this->edit_role_id = $user->role_id;
            $this->edit_status = $user->status;
            $this->edit_company_name = $user->company_name;
            $this->edit_address = $user->address;
            $this->edit_preferred_contact_method = $user->preferred_contact_method ?? 'email';

            Log::info('Edit form populated', [
                'first_name' => $this->edit_first_name,
                'last_name' => $this->edit_last_name,
                'email' => $this->edit_email,
                'role_id' => $this->edit_role_id,
                'status' => $this->edit_status
            ]);

            $this->updatedEditRoleId($this->edit_role_id);
            $this->showEditModal = true;
            Log::info('Edit modal opened', ['showEditModal' => $this->showEditModal]);

        } catch (\Exception $e) {
            Log::error('Error loading user for edit', [
                'userId' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function updatedEditRoleId($value)
    {
        Log::info('updatedEditRoleId called', ['role_id' => $value]);

        $selectedRole = Role::find($value);
        $this->showEditClientFields = $selectedRole && $selectedRole->role_name === 'client';

        Log::info('Client fields updated', [
            'showEditClientFields' => $this->showEditClientFields,
            'role_name' => $selectedRole ? $selectedRole->role_name : null
        ]);
    }

    public function resetEditForm()
    {
        Log::info('resetEditForm called');

        $this->reset([
            'edit_first_name',
            'edit_middle_name',
            'edit_last_name',
            'edit_email',
            'edit_phone_number',
            'edit_password',
            'edit_password_confirmation',
            'edit_role_id',
            'edit_status',
            'edit_company_name',
            'edit_address',
            'edit_preferred_contact_method',
            'showEditClientFields'
        ]);
        $this->edit_status = 'active';
        $this->edit_preferred_contact_method = 'email';
        $this->resetErrorBag();
        $this->resetValidation();

        Log::info('Edit form reset complete');
    }

    public function closeViewModal()
    {
        Log::info('closeViewModal called');

        $this->showViewModal = false;
        $this->selectedUser = null;
        $this->userId = null;

        Log::info('View modal closed');
    }

    public function closeEditModal()
    {
        Log::info('closeEditModal called');

        $this->showEditModal = false;
        $this->resetEditForm();

        Log::info('Edit modal closed');
    }

    public function updateUser()
    {
        Log::info('updateUser called', ['userId' => $this->userId]);

        $rules = [
            'edit_first_name' => 'required|string|max:255',
            'edit_last_name' => 'required|string|max:255',
            'edit_middle_name' => 'nullable|string|max:255',
            'edit_email' => 'required|email|unique:users,email,' . $this->userId,
            'edit_phone_number' => 'nullable|string|max:11|regex:/^[0-9]+$/',
            'edit_password' => 'nullable|min:8|confirmed',
            'edit_role_id' => 'required|exists:roles,id',
            'edit_status' => 'required|in:active,inactive,suspended',
            'edit_company_name' => 'nullable|string|max:255',
            'edit_address' => 'nullable|string|max:500',
            'edit_preferred_contact_method' => 'required|in:email,phone,both',
        ];

        $messages = [
            'edit_role_id.required' => 'Please select a role for the user.',
            'edit_email.unique' => 'This email address is already registered.',
            'edit_password.min' => 'Password must be at least 8 characters.',
            'edit_password.confirmed' => 'Password confirmation does not match.',
            'edit_phone_number.regex' => 'Phone number must contain only digits.',
            'edit_phone_number.max' => 'Phone number cannot exceed 11 digits.',
        ];

        Log::info('Validating user data', [
            'data' => [
                'first_name' => $this->edit_first_name,
                'last_name' => $this->edit_last_name,
                'email' => $this->edit_email,
                'role_id' => $this->edit_role_id,
                'status' => $this->edit_status
            ]
        ]);

        $this->validate($rules, $messages);
        Log::info('Validation passed');

        $updateData = [
            'first_name' => $this->edit_first_name,
            'middle_name' => $this->edit_middle_name,
            'last_name' => $this->edit_last_name,
            'email' => $this->edit_email,
            'phone_number' => $this->edit_phone_number,
            'role_id' => $this->edit_role_id,
            'status' => $this->edit_status,
            'company_name' => $this->edit_company_name,
            'address' => $this->edit_address,
            'preferred_contact_method' => $this->edit_preferred_contact_method,
        ];

        if (!empty($this->edit_password)) {
            $updateData['password'] = Hash::make($this->edit_password);
            Log::info('Password updated for user');
        }

        try {
            // Update the user
            $user = User::findOrFail($this->userId);
            $user->update($updateData);

            Log::info('User updated successfully', ['userId' => $this->userId]);

            // Update the selectedUser for view modal if it's open
            if ($this->selectedUser && $this->selectedUser->id == $this->userId) {
                $this->selectedUser = User::with('role')->find($this->userId);
            }

            session()->flash('message', 'User updated successfully!');

            $this->closeEditModal();
            $this->dispatch('userUpdated');
            $this->dispatch('refreshUsers');

        } catch (\Exception $e) {
            Log::error('Error updating user', [
                'userId' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    public function render()
    {
        Log::info('UserList render called', [
            'showViewModal' => $this->showViewModal,
            'showEditModal' => $this->showEditModal,
            'userId' => $this->userId
        ]);

        return view('livewire.admin.user-management.user-list');
    }
}