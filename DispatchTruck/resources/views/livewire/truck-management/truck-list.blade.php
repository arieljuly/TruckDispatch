<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Truck Management</h1>
                <p class="mt-1 text-sm text-gray-600">Manage fleet trucks and driver assignments</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.trucks.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Truck
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filters</h3>
                <p class="mt-1 text-sm text-gray-500">Search and filter trucks</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" 
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by name or plate number..."
                            class="w-full rounded-md border-gray-300 bg-white text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select wire:model.live="statusFilter"
                            class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="in-transit">In Transit</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                        <select wire:model.live="perPage"
                            class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="10">10 entries</option>
                            <option value="25">25 entries</option>
                            <option value="50">50 entries</option>
                            <option value="100">100 entries</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trucks Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Truck Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plate Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Driver</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($trucks as $truck)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-gray-900">#{{ $truck->id + 999 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $truck->truck_name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $truck->plate_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($truck->capacity_ltrs, 2) }}&nbsp;L</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($truck->available_ltrs, 2) }}&nbsp;L</td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($truck->currentAssignment && $truck->currentAssignment->driver)
                                        {{ trim($truck->currentAssignment->driver->user->first_name ?? '') . ' ' . trim($truck->currentAssignment->driver->user->last_name ?? '') }}
                                    @else
                                        <span class="text-sm text-gray-500">No driver assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'available' => 'bg-green-100 text-green-800',
                                            'in-transit' => 'bg-blue-100 text-blue-800',
                                            'maintenance' => 'bg-red-100 text-red-800',
                                            'inactive' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$truck->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($truck->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
    <div class="flex items-center justify-end space-x-3">
        <!-- View Button - Blue -->
        <a href="{{ route('admin.trucks.show', $truck->id) }}" 
            class="text-blue-600 hover:text-blue-800 transition-colors duration-150" 
            title="View Truck Details">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        </a>
        
        <!-- Edit Button - Orange -->
        <a href="{{ route('admin.trucks.edit', $truck->id) }}" 
            class="text-orange-600 hover:text-orange-800 transition-colors duration-150" 
            title="Edit Truck">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
        </a>
        
        <!-- Assign Button / Check Indicator -->
        @if(!$truck->currentAssignment)
            <button type="button" 
                onclick="openAssignModal({{ $truck->id }})"
                class="text-green-600 hover:text-green-800 transition-colors duration-150" 
                title="Assign Driver to Truck">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </button>
        @else
            <!-- Simple checkmark icon only, no text -->
            <span class="text-green-600" title="Driver Assigned">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </span>
        @endif
        
        <!-- Delete Button - Red -->
        @if(!$truck->currentAssignment)
            <button onclick="confirmDelete({{ $truck->id }})" 
                class="text-red-600 hover:text-red-800 transition-colors duration-150" 
                title="Delete Truck">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        @else
            <button type="button" 
                class="text-gray-400 cursor-not-allowed" 
                title="Cannot delete truck with active driver assignment. Please end the assignment first." 
                disabled
                onclick="showAssignmentAlert()">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        @endif
    </div>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">No trucks found. Click "New Truck" to get started.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($trucks->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $trucks->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Assign Driver Modal -->
    @if($showAssignModal)
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click.self="closeAssignModal">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto transform transition-all">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white rounded-t-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-indigo-100 rounded-full p-2">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Assign Driver to Truck</h3>
                                <p class="text-sm text-gray-500 mt-1">Select a driver and set assignment time</p>
                            </div>
                        </div>
                        <button wire:click="closeAssignModal" 
                            class="text-gray-400 hover:text-gray-500 transition-colors duration-150">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-6">
                    <div class="space-y-5">
                        <!-- Driver Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Select Driver <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="driver_id" 
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white">
                                <option value="">Choose a driver...</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">
                                        {{ trim($driver->user->first_name ?? '') }} {{ trim($driver->user->last_name ?? '') }} 
                                        - License #: {{ $driver->licensed_number ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('driver_id') 
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Start Time -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="datetime-local" 
                                    wire:model="start_time" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('start_time') 
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- End Time (Optional) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                End Time <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                            </label>
                            <div class="relative">
                                <input type="datetime-local" 
                                    wire:model="end_time" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('end_time') 
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Leave empty for open-ended assignment</p>
                        </div>

                        <!-- Info Alert -->
                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Driver will be marked as "on-duty" during this assignment period.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                    <button wire:click="closeAssignModal" 
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150">
                        Cancel
                    </button>
                    <button wire:click="assignDriver" 
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 shadow-sm">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Assign Driver
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openAssignModal(truckId) {
        @this.call('openAssignModal', truckId);
    }
    
    function confirmDelete(truckId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-lg',
                title: 'text-lg font-semibold',
                confirmButton: 'px-4 py-2 text-sm font-medium',
                cancelButton: 'px-4 py-2 text-sm font-medium'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deleteTruck', truckId);
            }
        });
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && @this.showAssignModal) {
            @this.call('closeAssignModal');
        }
    });
</script>