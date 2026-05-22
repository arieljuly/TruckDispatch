<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($viewingFuel && $selectedFuel)
                <!-- Detailed View Mode -->
                <div>
                    <!-- Header -->
                    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Fuel Type Details</h1>
                            <p class="mt-1 text-sm text-gray-600">View fuel type information</p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex gap-3">
                            <button wire:click="backToList" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to List
                            </button>
                            @if(!$selectedFuel->trashed())
                                <a href="{{ route('admin.fuel.edit', $selectedFuel->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Fuel Type
                                </a>
                            @endif
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

                    <!-- Fuel Type Information -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Fuel Type Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="border-b pb-3">
                                    <label class="text-sm font-medium text-gray-500">Fuel Code</label>
                                    <p class="mt-1 text-gray-900 font-semibold">{{ $selectedFuel->fuel_code }}</p>
                                </div>
                                <div class="border-b pb-3">
                                    <label class="text-sm font-medium text-gray-500">Fuel Name</label>
                                    <p class="mt-1 text-gray-900">{{ $selectedFuel->fuel_name }}</p>
                                </div>
                                <div class="border-b pb-3">
                                    <label class="text-sm font-medium text-gray-500">Status</label>
                                    <div class="mt-1">
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $selectedFuel->status_color }}">
                                            {{ ucfirst($selectedFuel->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="border-b pb-3">
                                    <label class="text-sm font-medium text-gray-500">Created At</label>
                                    <p class="mt-1 text-gray-900">{{ $selectedFuel->created_at->format('F d, Y H:i:s') }}</p>
                                </div>
                                <div class="border-b pb-3">
                                    <label class="text-sm font-medium text-gray-500">Last Updated</label>
                                    <p class="mt-1 text-gray-900">{{ $selectedFuel->updated_at->format('F d, Y H:i:s') }}</p>
                                </div>
                                @if($selectedFuel->deleted_at)
                                    <div class="border-b pb-3">
                                        <label class="text-sm font-medium text-gray-500">Deleted At</label>
                                        <p class="mt-1 text-red-600">{{ $selectedFuel->deleted_at->format('F d, Y H:i:s') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- List Mode -->
                <div>
                    <!-- Header Section -->
                    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Fuel Types</h1>
                            <p class="mt-1 text-sm text-gray-600">Manage your fuel type inventory</p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex gap-3">
                            <a href="{{ route('admin.fuel.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Fuel Type
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

                    <!-- Filters -->
                    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    placeholder="Search by code or name..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select wire:model.live="statusFilter"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label for="perPage" class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                                <select wire:model.live="perPage"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Results Count -->
                    <div class="mb-4 flex justify-between items-center">
                        <p class="text-sm text-gray-600">
                            Showing <span class="font-medium">{{ $fuels->firstItem() ?? 0 }}</span> to 
                            <span class="font-medium">{{ $fuels->lastItem() ?? 0 }}</span> of 
                            <span class="font-medium">{{ $fuels->total() }}</span> results
                        </p>
                    </div>

                    <!-- Table Card -->
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
                                                @if($fuel->trashed())
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Deleted
                                                    </span>
                                                @endif
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
                                                {{ $fuel->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex gap-2">
                                                    <button wire:click="viewFuel({{ $fuel->id }})"
                                                        class="text-blue-600 hover:text-blue-900 transition-colors duration-150"
                                                        title="View Details">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </button>
                                                    <a href="{{ route('admin.fuel.edit', $fuel->id) }}"
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    @if(!$fuel->trashed())
                                                        <button onclick="confirmDelete({{ $fuel->id }})"
                                                            class="text-red-600 hover:text-red-900">
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
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $fuels->links() }}
                        </div>
                    </div>
                </div>
            @endif
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
                }
            });
        }
    </script>
</div>