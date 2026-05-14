<div class="py-8 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Driver Management</h1>
                <p class="mt-2 text-sm text-gray-600">Manage and organize your fleet drivers</p>
            </div>
            <div class="mt-6 sm:mt-0">
                <button type="button" wire:click="openCreateModal"
                    class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-md text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:shadow-lg">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Driver
                </button>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-5 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 p-5 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6v-2m0-4v-2"></path>
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search by name, email, or license..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select wire:model.live="statusFilter"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="on-duty">On Duty</option>
                            <option value="off-duty">Off Duty</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                        <select wire:model.live="perPage"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white">
                            <option value="10">10 entries</option>
                            <option value="25">25 entries</option>
                            <option value="50">50 entries</option>
                            <option value="100">100 entries</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drivers Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Truck</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($drivers as $driver)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $driver->id + 999 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <span class="text-indigo-600 font-medium">{{ $driver->user->initials() ?? substr($driver->user->first_name ?? 'N', 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $driver->user->full_name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $driver->user->email ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $driver->licensed_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($driver->currentAssignment && $driver->currentAssignment->truck)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $driver->currentAssignment->truck->truck_name ?? 'N/A' }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">No truck assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'available' => 'bg-green-100 text-green-800',
                                            'on-duty' => 'bg-blue-100 text-blue-800',
                                            'off-duty' => 'bg-gray-100 text-gray-800',
                                            'inactive' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$driver->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('-', ' ', $driver->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.drivers.show', $driver->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150" title="View Driver">
                                            <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.drivers.edit', $driver->id) }}"
                                            class="text-blue-600 hover:text-blue-900 transition-colors duration-150" title="Edit Driver">
                                            <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        @if($driver->status == 'inactive')
                                            <button type="button" onclick="confirmReactivate({{ $driver->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                                title="Reactivate Driver">
                                                <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button" onclick="confirmDeactivate({{ $driver->id }})" 
                                                @if($driver->currentAssignment) disabled @endif
                                                class="text-red-600 hover:text-red-900 transition-colors duration-150 {{ $driver->currentAssignment ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                title="Deactivate Driver">
                                                <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-gray-100 rounded-full p-3 mb-3">
                                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-1">No drivers found</h3>
                                        <p class="text-sm text-gray-500">Get started by creating a new driver.</p>
                                        <button type="button" wire:click="openCreateModal"
                                            class="mt-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            Add New Driver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($drivers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $drivers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Driver Modal -->
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">New Driver</h3>
                <p class="mt-1 text-sm text-gray-500">Assign a user as a driver</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select User <span class="text-red-500">*</span></label>
                        <select wire:model="user_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white">
                            <option value="">Select a user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">License Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="licensed_number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white" placeholder="e.g., D123-4567-8901">
                        @error('licensed_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white">
                            <option value="available">Available</option>
                            <option value="on-duty">On Duty</option>
                            <option value="off-duty">Off Duty</option>
                        </select>
                        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button wire:click="closeCreateModal" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150">Cancel</button>
                <button wire:click="createDriver" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-150">Create Driver</button>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function confirmDeactivate(driverId) {
    showLoadingAlert('Checking driver status...');
    
    @this.call('canDeactivateDriver', driverId).then((canDeactivate) => {
        Swal.close(); // Close loading alert
        
        if (!canDeactivate) {
            Swal.fire({
                title: 'Cannot Deactivate',
                text: 'Driver has active assignments. Remove them first.',
                icon: 'error',
                width: 320,
                padding: '1.5rem',
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'OK',
                timer: 3500,
                timerProgressBar: true,
                showConfirmButton: true,
                customClass: {
                    popup: 'text-sm',
                    title: 'text-base'
                }
            });
            return;
        }
        
        // Show confirmation dialog
        Swal.fire({
            title: 'Deactivate Driver?',
            html: `
                <div class="text-left text-xs">
                    <p class="mb-1">This driver will be <strong class="text-red-600">deactivated</strong>.</p>
                    <ul class="list-disc list-inside text-xs text-gray-600 mb-2 space-y-0.5">
                        <li>Cannot be assigned to trucks</li>
                        <li>Will be removed from duty</li>
                        <li>Can be reactivated later</li>
                    </ul>
                </div>
            `,
            icon: 'warning',
            width: 320,
            padding: '1.5rem',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, deactivate',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                popup: 'text-sm',
                title: 'text-lg',
                htmlContainer: 'p-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deactivating...',
                    html: 'Updating status',
                    width: 280,
                    padding: '1.5rem',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'text-sm',
                        title: 'text-base'
                    },
                    didOpen: () => {
                        Swal.showLoading();
                        @this.call('deleteDriver', driverId);
                    }
                });
            }
        });
    }).catch((error) => {
        Swal.close();
        Swal.fire({
            title: 'Error',
            text: 'Unable to check driver status. Please try again.',
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
    });
}

function confirmReactivate(driverId) {
    Swal.fire({
        title: 'Reactivate Driver?',
        html: `
            <div class="text-left text-xs">
                <p class="mb-1">This driver will be <strong class="text-emerald-600">reactivated</strong>.</p>
                <ul class="list-disc list-inside text-xs text-gray-600 space-y-0.5">
                    <li>Available for assignments</li>
                    <li>Can be assigned to duty</li>
                    <li>Status set to Available</li>
                </ul>
            </div>
        `,
        icon: 'question',
        width: 320,
        padding: '1.5rem',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, reactivate',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'text-sm',
            title: 'text-lg',
            htmlContainer: 'p-0'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Reactivating...',
                html: 'Updating status',
                width: 280,
                padding: '1.5rem',
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: {
                    popup: 'text-sm',
                    title: 'text-base'
                },
                didOpen: () => {
                    Swal.showLoading();
                    @this.call('reactivateDriver', driverId);
                }
            });
        }
    });
}

// Helper function for showing loading alerts
function showLoadingAlert(message = 'Processing...') {
    Swal.fire({
        title: message,
        width: 280,
        padding: '1.5rem',
        allowOutsideClick: false,
        showConfirmButton: false,
        customClass: {
            popup: 'text-sm',
            title: 'text-base'
        },
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Success alert helper
function showSuccessAlert(message, title = 'Success!') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        width: 320,
        padding: '1.5rem',
        confirmButtonColor: '#10b981',
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: true,
        customClass: {
            popup: 'text-sm',
            title: 'text-base'
        }
    });
}

// Error alert helper
function showErrorAlert(message, title = 'Error!') {
    Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        width: 320,
        padding: '1.5rem',
        confirmButtonColor: '#dc2626',
        customClass: {
            popup: 'text-sm',
            title: 'text-base'
        }
    });
}

// Listen for Livewire events
document.addEventListener('livewire:initialized', () => {
    // Close any open alerts when refreshing
    Livewire.on('refreshDrivers', () => {
        if (Swal.isVisible()) {
            Swal.close();
        }
        showSuccessAlert('Operation completed successfully!', 'Updated');
    });
    
    // Handle errors from Livewire
    Livewire.on('operationError', (errorData) => {
        if (Swal.isVisible()) {
            Swal.close();
        }
        showErrorAlert(errorData.message || 'An error occurred');
    });
});
</script>