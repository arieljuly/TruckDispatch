<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Fuel Management</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage fuel types and configurations</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.fuel.create') }}" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Fuel Type
                    </a>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mb-4 rounded-md p-4 {{ session('message_type') == 'success' ? 'bg-green-50 border-l-4 border-green-400' : 'bg-red-50 border-l-4 border-red-400' }}">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            @if(session('message_type') == 'success')
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3">
                            <p class="text-sm {{ session('message_type') == 'success' ? 'text-green-700' : 'text-red-700' }}">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Search and Filter Section -->
            <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Search by fuel code or name..."
                                class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Status Filter</label>
                        <select wire:model.live="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="deleted">Deleted</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="perPage" class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                        <select wire:model.live="perPage" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="10">10 entries</option>
                            <option value="25">25 entries</option>
                            <option value="50">50 entries</option>
                            <option value="100">100 entries</option>
                        </select>
                    </div>
                </div>
                
                <!-- Active Filters Display -->
                @if($search || $statusFilter)
                    <div class="mt-4 flex items-center gap-2 flex-wrap">
                        <span class="text-sm text-gray-600">Active Filters:</span>
                        @if($search)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800">
                                Search: {{ $search }}
                                <button wire:click="$set('search', '')" class="ml-1 hover:text-blue-600">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        @if($statusFilter)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-800">
                                Status: {{ ucfirst($statusFilter) }}
                                <button wire:click="$set('statusFilter', '')" class="ml-1 hover:text-green-600">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        @endif
                        <button wire:click="clearFilters" class="text-sm text-red-600 hover:text-red-800">
                            Clear All Filters
                        </button>
                    </div>
                @endif
            </div>

            <!-- Results Count -->
            <div class="mb-4 flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    Showing <span class="font-medium">{{ $fuels->firstItem() ?? 0 }}</span> to 
                    <span class="font-medium">{{ $fuels->lastItem() ?? 0 }}</span> of 
                    <span class="font-medium">{{ $fuels->total() }}</span> results
                </p>
                <div class="text-sm text-gray-500">
                    Total Fuel Types: <span class="font-medium">{{ $totalFuels }}</span>
                </div>
            </div>

            <!-- Fuel Types Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($fuels as $fuel)
                                <tr class="hover:bg-gray-50 {{ $fuel->trashed() ? 'bg-gray-50 opacity-75' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium {{ $fuel->trashed() ? 'text-gray-500' : 'text-gray-900' }}">
                                            {{ $fuel->fuel_code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm {{ $fuel->trashed() ? 'text-gray-500' : 'text-gray-900' }}">
                                            {{ $fuel->fuel_name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $fuel->status_color }}">
                                            {{ ucfirst($fuel->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $fuel->created_at->format('M d, Y') }}
                                        @if($fuel->deleted_at)
                                            <div class="text-xs text-red-500">
                                                Deleted: {{ $fuel->deleted_at->format('M d, Y H:i') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.fuel.list', ['view' => $fuel->id]) }}"
                                                class="text-blue-600 hover:text-blue-900 transition-colors duration-150" title="View Details">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <!-- Edit Button (only for non-deleted) -->
                                            @if(!$fuel->trashed())
                                                <a href="{{ route('admin.fuel.edit', $fuel->id) }}" 
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150"
                                                    title="Edit Fuel">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                            @endif

                                            <!-- Restore Button (only for deleted) -->
                                            @if($fuel->trashed())
                                                <button onclick="confirmRestore({{ $fuel->id }})" 
                                                    class="text-green-600 hover:text-green-900 transition-colors duration-150"
                                                    title="Restore Fuel">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- Delete/Permanent Delete Button -->
                                            @if(!$fuel->trashed())
                                                <button onclick="confirmDelete({{ $fuel->id }})" 
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-150"
                                                    title="Soft Delete">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @else
                                                <button onclick="confirmForceDelete({{ $fuel->id }})" 
                                                    class="text-red-800 hover:text-red-900 transition-colors duration-150"
                                                    title="Permanently Delete">
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
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="text-gray-500">No fuel types found.</div>
                                     </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $fuels->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(fuelId) {
            Swal.fire({
                title: 'Delete Fuel Type?',
                html: "This will soft delete the fuel type and set status to <strong>inactive</strong>.<br>You can restore it later.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteFuel', fuelId);
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            });
        }

        function confirmRestore(fuelId) {
            Swal.fire({
                title: 'Restore Fuel Type?',
                text: "This will restore the fuel type and set status to active.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, restore it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('restoreFuel', fuelId);
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            });
        }

        function confirmForceDelete(fuelId) {
            Swal.fire({
                title: 'Permanently Delete?',
                text: "This action cannot be undone! The fuel type will be permanently removed.",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, permanently delete!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('forceDeleteFuel', fuelId);
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            });
        }
    </script>
</div>