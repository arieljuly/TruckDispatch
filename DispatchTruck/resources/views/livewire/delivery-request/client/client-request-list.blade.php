<div>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header with Stats -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Delivery Requests</h1>
                    <p class="mt-1 text-sm text-gray-600">Track and manage your fuel delivery requests</p>
                </div>
                <a href="{{ route('client.delivery.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Request
                </a>
            </div>
        </div>

        <!-- Stats Cards - All in one row (4 cards) -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <!-- Pending Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition duration-150 {{ $statusFilter == 'pending' ? 'ring-2 ring-yellow-500' : '' }}"
                    wire:click="$set('statusFilter', 'pending')">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 truncate">Pending</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-full bg-yellow-100 p-2">
                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- In Progress Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition duration-150 {{ $statusFilter == 'partially_fulfilled' ? 'ring-2 ring-blue-500' : '' }}"
                    wire:click="$set('statusFilter', 'partially_fulfilled')">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 truncate">In Progress</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['partially_fulfilled'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-full bg-blue-100 p-2">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Completed Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition duration-150 {{ $statusFilter == 'fulfilled' ? 'ring-2 ring-green-500' : '' }}"
                    wire:click="$set('statusFilter', 'fulfilled')">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 truncate">Completed</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['fulfilled'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-full bg-green-100 p-2">
                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
        
                <!-- Cancelled Card -->
                <div class="bg-white overflow-hidden shadow rounded-lg cursor-pointer hover:shadow-md transition duration-150 {{ $statusFilter == 'cancelled' ? 'ring-2 ring-red-500' : '' }}"
                    wire:click="$set('statusFilter', 'cancelled')">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500 truncate">Cancelled</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['cancelled'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-full bg-red-100 p-2">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Filter Requests</h3>
                        <button wire:click="resetFilters" type="button"
                            class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Filters
                        </button>
                    </div>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model="statusFilter" id="status"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="partially_fulfilled">In Progress</option>
                                <option value="fulfilled">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select wire:model="priorityFilter" id="priority"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" wire:model="search" id="search" 
                                    placeholder="Search by area or station..."
                                    class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if($statusFilter || $priorityFilter || $search)
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
                <div class="flex flex-wrap gap-2">
                    @if($statusFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            Status: {{ ucfirst(str_replace('_', ' ', $statusFilter)) }}
                            <button wire:click="$set('statusFilter', '')" class="ml-2 text-indigo-600 hover:text-indigo-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($priorityFilter)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            Priority: {{ ucfirst($priorityFilter) }}
                            <button wire:click="$set('priorityFilter', '')" class="ml-2 text-indigo-600 hover:text-indigo-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if($search)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            Search: {{ $search }}
                            <button wire:click="$set('search', '')" class="ml-2 text-indigo-600 hover:text-indigo-900">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Requests List -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Recent Delivery Requests</h3>
                            <p class="mt-1 text-sm text-gray-500">Showing {{ $deliveryRequests->firstItem() ?? 0 }} to {{ $deliveryRequests->lastItem() ?? 0 }} of {{ $deliveryRequests->total() }} requests</p>
                        </div>
                        @if($statusFilter || $priorityFilter || $search)
                            <span class="text-sm text-gray-500">
                                Found {{ $deliveryRequests->total() }} result(s)
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Table View -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID & Details</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status & Priority</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($deliveryRequests as $request)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#{{ $request->id }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->station->station_name ?? 'N/A' }}</div>
                                        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-2">
                                            <!-- Status Badge -->
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full w-28 justify-center
                                                {{ $request->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $request->status == 'partially_fulfilled' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $request->status == 'fulfilled' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $request->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                @if($request->status == 'pending') @endif
                                                @if($request->status == 'partially_fulfilled') @endif
                                                @if($request->status == 'fulfilled') @endif
                                                @if($request->status == 'cancelled') @endif
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                            <!-- Priority Badge -->
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full w-28 justify-center
                                                {{ $request->priority == 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $request->priority == 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $request->priority == 'medium' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $request->priority == 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                @if($request->priority == 'urgent') @endif
                                                @if($request->priority == 'high') @endif
                                                @if($request->priority == 'medium') @endif
                                                @if($request->priority == 'low') @endif
                                                {{ ucfirst($request->priority) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ number_format($request->requested_liters, 2) }} L</div>
                                        <div class="text-xs text-gray-500">Requested</div>
                                        <div class="text-sm text-green-600 mt-1">{{ number_format($request->fulfilled_liters, 2) }} L</div>
                                        <div class="text-xs text-gray-500">Fulfilled</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-32">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-xs font-medium text-gray-700">{{ number_format($request->progress_percentage, 1) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-indigo-600 rounded-full h-2 transition-all duration-500" style="width: {{ $request->progress_percentage }}%"></div>
                                            </div>
                                            @if($request->deadline)
                                                <div class="mt-2 text-xs {{ \Carbon\Carbon::parse($request->deadline)->isPast() && $request->status != 'fulfilled' ? 'text-red-600' : 'text-gray-500' }}">
                                                    📅 {{ \Carbon\Carbon::parse($request->deadline)->format('M d') }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex space-x-2 justify-end">
                                            <a href="{{ route('client.delivery.show', $request->id) }}" 
                                               class="inline-flex items-center px-3 py-1 border border-indigo-300 rounded-md text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition duration-150">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </a>
                                            @if($request->status == 'pending')
                                                <a href="{{ route('client.delivery.edit', $request->id) }}" 
                                                   class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="mx-auto h-24 w-24 text-gray-400 mb-4">
                                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No delivery requests found</h3>
                                        <p class="mt-1 text-sm text-gray-500">
                                            @if($statusFilter || $priorityFilter || $search)
                                                No requests match your filters. Try adjusting your search criteria.
                                            @else
                                                Get started by creating your first delivery request.
                                            @endif
                                        </p>
                                        <div class="mt-6">
                                            <a href="{{ route('client.delivery.create') }}" 
                                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                Create New Request
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($deliveryRequests->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200 sm:px-6 bg-gray-50">
                        {{ $deliveryRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>