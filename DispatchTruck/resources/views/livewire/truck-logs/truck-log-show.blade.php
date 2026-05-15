<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Truck Activity Logs</h1>
                <p class="mt-1 text-sm text-gray-600">Track all truck operations and activities</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button wire:click="resetFilters"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Reset Filters
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500">Total Activities</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($actionStats['total']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500">Deliveries</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($actionStats['delivered']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500">Assignments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($actionStats['assigned']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-xs text-gray-500">Maintenance</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($actionStats['maintenance']) }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filters</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Truck, location, remarks..."
                            class="w-full rounded-md border-gray-300 bg-white text-gray-900 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Action Type</label>
                        <select wire:model.live="actionFilter"
                            class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Actions</option>
                            <option value="created">Created</option>
                            <option value="assigned">Assigned</option>
                            <option value="loaded">Loaded</option>
                            <option value="departed">Departed</option>
                            <option value="arrived">Arrived</option>
                            <option value="delivered">Delivered</option>
                            <option value="returned">Returned</option>
                            <option value="refueled">Refueled</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="status_change">Status Change</option>
                            <option value="driver_assigned">Driver Assigned</option>
                            <option value="driver_unassigned">Driver Unassigned</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" wire:model.live="dateFrom"
                            class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" wire:model.live="dateTo"
                            class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Truck</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->truck->truck_name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $log->truck->plate_number ?? '' }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $actionColors = [
                                            'created' => 'bg-gray-100 text-gray-800',
                                            'assigned' => 'bg-purple-100 text-purple-800',
                                            'loaded' => 'bg-blue-100 text-blue-800',
                                            'departed' => 'bg-indigo-100 text-indigo-800',
                                            'arrived' => 'bg-green-100 text-green-800',
                                            'delivered' => 'bg-emerald-100 text-emerald-800',
                                            'returned' => 'bg-cyan-100 text-cyan-800',
                                            'refueled' => 'bg-yellow-100 text-yellow-800',
                                            'maintenance' => 'bg-red-100 text-red-800',
                                            'status_change' => 'bg-orange-100 text-orange-800',
                                            'driver_assigned' => 'bg-teal-100 text-teal-800',
                                            'driver_unassigned' => 'bg-pink-100 text-pink-800',
                                        ];

                                        $actionIcons = [
                                            'created' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                                            'assigned' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                            'loaded' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4',
                                            'departed' => 'M9 11l3-3m0 0l3 3m-3-3v8m9-4a9 9 0 11-18 0 9 9 0 0118 0z',
                                            'arrived' => 'M9 13l3 3m0 0l3-3m-3 3V8m9 4a9 9 0 11-18 0 9 9 0 0118 0z',
                                            'delivered' => 'M5 13l4 4L19 7',
                                            'returned' => 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6',
                                            'refueled' => 'M4 8h16M4 16h16M8 4v4m8-4v4',
                                            'maintenance' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                            'status_change' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                                            'driver_assigned' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3M4 4h16v16H4z',
                                            'driver_unassigned' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3M4 4h16v16H4z',
                                        ];
                                    @endphp
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 mr-2 {{ explode(' ', $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800')[1] }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $actionIcons[$log->action] ?? 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z' }}">
                                            </path>
                                        </svg>
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $actionColors[$log->action] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if($log->liters)
                                            <span class="font-medium">{{ number_format($log->liters, 2) }} L</span>
                                            @if($log->action === 'loaded') loaded @endif
                                            @if($log->action === 'refueled') refueled @endif
                                            @if($log->action === 'delivered') delivered @endif
                                            <br>
                                        @endif
                                        @if($log->remarks)
                                            <span class="text-gray-600">{{ $log->remarks }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($log->location)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $log->location }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No activity logs found</p>
                                    <p class="text-xs text-gray-400">Logs will appear here as trucks perform actions</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Auto-refresh logs every 30 seconds
        setInterval(function () {
            @this.refreshLogs();
        }, 30000);
    </script>
@endpush