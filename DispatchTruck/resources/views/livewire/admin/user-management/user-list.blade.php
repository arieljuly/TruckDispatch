<div>
    <!-- View User Modal -->
    @if($showViewModal)
        <div
            style="position: fixed; inset: 0; z-index: 99999; background: rgba(0,0,0,0.75); display: flex; align-items: center; justify-content: center; overflow-y: auto;">
            <div
                style="background: white; border-radius: 12px; max-width: 500px; width: 90%; margin: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                @if($selectedUser)
                    <div style="padding: 20px;">
                        <div style="display: flex; align-items: flex-start;">
                            <div
                                style="flex-shrink: 0; display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 9999px; background-color: #e0e7ff;">
                                <span
                                    style="color: #4f46e5; font-size: 18px; font-weight: bold;">{{ $selectedUser->initials() }}</span>
                            </div>
                            <div style="margin-left: 16px; width: 100%;">
                                <h3 style="font-size: 18px; font-weight: 500; color: #111827; margin: 0 0 16px 0;">User Details
                                </h3>

                                <div style="margin-top: 16px;">
                                    <!-- Personal Information -->
                                    <div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 12px;">
                                        <h4
                                            style="font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; margin: 0 0 8px 0;">
                                            Personal Information</h4>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Full Name:</span>
                                            <span style="font-size: 14px; color: #111827;">{{ $selectedUser->full_name }}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Email:</span>
                                            <span style="font-size: 14px; color: #111827;">{{ $selectedUser->email }}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Phone:</span>
                                            <span
                                                style="font-size: 14px; color: #111827;">{{ $selectedUser->phone_number ?? 'Not provided' }}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Role:</span>
                                            <span
                                                style="font-size: 14px; color: #111827;">{{ ucfirst($selectedUser->role->role_name ?? 'N/A') }}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Status:</span>
                                            <span
                                                style="padding: 2px 8px; font-size: 12px; border-radius: 9999px; 
                                                        {{ $selectedUser->status === 'active' ? 'background-color: #d1fae5; color: #065f46;' :
            ($selectedUser->status === 'inactive' ? 'background-color: #fef3c7; color: #92400e;' : 'background-color: #fee2e2; color: #991b1b;') }}">
                                                {{ ucfirst($selectedUser->status ?? 'Inactive') }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($selectedUser->company_name || $selectedUser->address)
                                        <div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 12px;">
                                            <h4
                                                style="font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; margin: 0 0 8px 0;">
                                                Company Information</h4>
                                            @if($selectedUser->company_name)
                                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                                    <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Company:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827;">{{ $selectedUser->company_name }}</span>
                                                </div>
                                            @endif
                                            @if($selectedUser->address)
                                                <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                                    <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Address:</span>
                                                    <span style="font-size: 14px; color: #111827;">{{ $selectedUser->address }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div>
                                        <h4
                                            style="font-size: 12px; font-weight: 500; color: #6b7280; text-transform: uppercase; margin: 0 0 8px 0;">
                                            Account Information</h4>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Email
                                                Verified:</span>
                                            <span
                                                style="font-size: 14px; color: {{ $selectedUser->email_verified_at ? '#059669' : '#dc2626' }};">{{ $selectedUser->email_verified_at ? 'Yes' : 'No' }}</span>
                                        </div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                            <span style="font-size: 14px; font-weight: 500; color: #6b7280;">Member
                                                Since:</span>
                                            <span
                                                style="font-size: 14px; color: #111827;">{{ $selectedUser->created_at->format('F d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        style="background-color: #f9fafb; padding: 12px 20px; display: flex; justify-content: flex-end; gap: 12px; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                        <button type="button" wire:click="$dispatch('editUser', { userId: {{ $userId }} }); closeViewModal()"
                            style="padding: 8px 16px; border: none; border-radius: 8px; background-color: #4f46e5; color: white; font-weight: 500; cursor: pointer;">
                            Edit User
                        </button>
                        <button type="button" wire:click="closeViewModal"
                            style="padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 8px; background-color: white; color: #374151; font-weight: 500; cursor: pointer;">
                            Close
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

   <!-- Edit User Modal -->
    @if($showEditModal)
        <div style="position: fixed; inset: 0; z-index: 99999; background: rgba(0,0,0,0.75); display: flex; align-items: center; justify-content: center; overflow-y: auto;">
            <div style="background: white; border-radius: 12px; max-width: 90%; width: 800px; max-height: 90vh; overflow-y: auto; margin: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);">
                <div style="background: linear-gradient(to right, #2563eb, #4f46e5); padding: 16px 24px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 8px;">
                                <svg style="width: 32px; height: 32px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 style="font-size: 20px; font-weight: bold; color: white; margin: 0;">Edit User</h3>
                                <p style="font-size: 14px; color: #bfdbfe; margin-top: 4px;">Update user information for User ID: {{ $userId }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="closeEditModal" style="color: rgba(255,255,255,0.8); background: none; border: none; cursor: pointer; padding: 8px;">
                            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="updateUser">
                    <div style="background: white; padding: 24px; max-height: calc(90vh - 200px); overflow-y: auto;">
                        <!-- Personal Information -->
                        <div style="margin-bottom: 24px;">
                            <h4
                                style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; display: flex; align-items: center;">
                                <span style="background: #e0e7ff; border-radius: 8px; padding: 6px; margin-right: 8px; display: inline-flex;">
                                    <svg style="width: 20px; height: 20px; color: #4f46e5;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </span>
                                Personal Information
                            </h4>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">First
                                        Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" wire:model="edit_first_name"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                    @error('edit_first_name') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Middle
                                        Name</label>
                                    <input type="text" wire:model="edit_middle_name"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                    @error('edit_middle_name') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Last
                                        Name <span style="color: #ef4444;">*</span></label>
                                    <input type="text" wire:model="edit_last_name"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                    @error('edit_last_name') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div style="margin-bottom: 24px;">
                            <h4
                                style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; display: flex; align-items: center;">
                                <span style="background: #e0e7ff; border-radius: 8px; padding: 6px; margin-right: 8px; display: inline-flex;">
                                    <svg style="width: 20px; height: 20px; color: #4f46e5;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </span>
                                Contact Information
                            </h4>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Email
                                        Address <span style="color: #ef4444;">*</span></label>
                                    <input type="email" wire:model="edit_email"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                    @error('edit_email') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Phone
                                        Number</label>
                                    <input type="text" wire:model="edit_phone_number" maxlength="11"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="11-digit number"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                    @error('edit_phone_number') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div style="margin-bottom: 24px;">
                            <h4
                                style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; display: flex; align-items: center;">
                                <span style="background: #e0e7ff; border-radius: 8px; padding: 6px; margin-right: 8px; display: inline-flex;">
                                    <svg style="width: 20px; height: 20px; color: #4f46e5;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                </span>
                                Account Information
                            </h4>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Role
                                        <span style="color: #ef4444;">*</span></label>
                                    <select wire:model="edit_role_id"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" style="color: #111827;">{{ ucfirst($role->role_name) }}</option>
                                        @endforeach
                                    </select>
                                    @error('edit_role_id') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Status</label>
                                    <select wire:model="edit_status"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                        <option value="active" style="color: #111827;">Active</option>
                                        <option value="inactive" style="color: #111827;">Inactive</option>
                                        <option value="suspended" style="color: #111827;">Suspended</option>
                                    </select>
                                    @error('edit_status') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">New
                                        Password</label>
                                    <input type="password" wire:model="edit_password"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                    @error('edit_password') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Confirm
                                        Password</label>
                                    <input type="password" wire:model="edit_password_confirmation"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                    @error('edit_password_confirmation') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">
                                    {{ $message }}</p> @enderror
                                </div>
                                <div style="grid-column: span 2;">
                                    <label
                                        style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Preferred
                                        Contact Method</label>
                                    <select wire:model="edit_preferred_contact_method"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                        <option value="email" style="color: #111827;">Email</option>
                                        <option value="phone" style="color: #111827;">Phone</option>
                                        <option value="both" style="color: #111827;">Both</option>
                                    </select>
                                    @error('edit_preferred_contact_method') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">
                                    {{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Company Information -->
                        <div style="margin-bottom: 24px;">
                            <h4
                                style="font-size: 16px; font-weight: 600; color: #111827; margin: 0 0 16px 0; display: flex; align-items: center;">
                                <span style="background: #e0e7ff; border-radius: 8px; padding: 6px; margin-right: 8px; display: inline-flex;">
                                    <svg style="width: 20px; height: 20px; color: #4f46e5;" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </span>
                                Company Information
                            </h4>
                            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                                <div>
                                    <label
                                        style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Company
                                        Name</label>
                                    <input type="text" wire:model="edit_company_name"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827;">
                                    @error('edit_company_name') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">Address</label>
                                    <textarea wire:model="edit_address" rows="3"
                                        style="width: 100%; padding: 10px 16px; border-radius: 8px; border: 1px solid #d1d5db; outline: none; background-color: white; color: #111827; resize: none;"></textarea>
                                    @error('edit_address') <p style="color: #ef4444; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="background-color: #f9fafb; padding: 16px 24px; display: flex; justify-content: flex-end; gap: 12px;">
                        <button type="button" wire:click="closeEditModal" style="padding: 10px 20px; border: 1px solid #d1d5db; border-radius: 8px; background-color: white; color: #374151; font-weight: 500; cursor: pointer;">
                            Cancel
                        </button>
                        <button type="submit" style="padding: 10px 20px; border: none; border-radius: 8px; background: linear-gradient(to right, #4f46e5, #7c3aed); color: white; font-weight: 500; cursor: pointer;">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>