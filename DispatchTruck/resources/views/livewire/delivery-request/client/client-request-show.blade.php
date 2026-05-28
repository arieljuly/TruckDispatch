<div>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header with Back Button -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Delivery Request Details</h1>
                    <p class="mt-1 text-sm text-gray-600">View and manage your delivery request #{{ $request->id }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('client.delivery.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </a>
                    @if($request->status == 'pending')
                        <a href="{{ route('client.delivery.edit', $request->id) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Edit Request
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6">
            <div class="rounded-lg p-4 
                @if($request->status == 'pending') bg-yellow-50 border border-yellow-200
                @elseif($request->status == 'partially_fulfilled') bg-blue-50 border border-blue-200
                @elseif($request->status == 'fulfilled') bg-green-50 border border-green-200
                @elseif($request->status == 'cancelled') bg-red-50 border border-red-200
                @endif">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($request->status == 'pending')
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($request->status == 'partially_fulfilled')
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            @elseif($request->status == 'fulfilled')
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            @else
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium 
                                @if($request->status == 'pending') text-yellow-800
                                @elseif($request->status == 'partially_fulfilled') text-blue-800
                                @elseif($request->status == 'fulfilled') text-green-800
                                @else text-red-800
                                @endif">
                                Request Status: {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                            </h3>
                            <div class="mt-1 text-sm 
                                @if($request->status == 'pending') text-yellow-700
                                @elseif($request->status == 'partially_fulfilled') text-blue-700
                                @elseif($request->status == 'fulfilled') text-green-700
                                @else text-red-700
                                @endif">
                                @if($request->status == 'pending')
                                    Your request is awaiting approval from the dispatch team.
                                @elseif($request->status == 'partially_fulfilled')
                                    Your delivery is in progress. Some fuel has been delivered.
                                @elseif($request->status == 'fulfilled')
                                    Your delivery request has been completed successfully.
                                @else
                                    This request has been cancelled.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Request Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Request Information Card -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Request Information</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Request ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">#{{ $request->id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Priority Level</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $request->priority == 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $request->priority == 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $request->priority == 'medium' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $request->priority == 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ ucfirst($request->priority) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Requested Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($request->created_at)->format('F d, Y h:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                                    <dd
                                        class="mt-1 text-sm {{ \Carbon\Carbon::parse($request->deadline)->isPast() && $request->status != 'fulfilled' ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                        {{ \Carbon\Carbon::parse($request->deadline)->format('F d, Y h:i A') }}
                                        @if(\Carbon\Carbon::parse($request->deadline)->isPast() && $request->status != 'fulfilled')
                                            <span class="ml-2 text-xs text-red-600">(Overdue)</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Area</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $request->area->area_name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fuel Station</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $request->station->station_name ?? 'N/A' }}</dd>
                                </div>
                                @if($request->station && $request->station->address)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Station Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $request->station->address }}</dd>
                                    </div>
                                @endif
                                @if($request->notes)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Special Instructions</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $request->notes }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Fuel Items Card -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Fuel Items</h3>
                            <p class="mt-1 text-sm text-gray-500">Fuel products from Purchase Order</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuel Type
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requested (L)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fulfilled (L)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Remaining (L)</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($fuelItems as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->fuel_type_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->fuel_type_code }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="text-sm text-gray-900">{{ number_format($item->requested_liters, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="text-sm text-green-600">{{ number_format($item->fulfilled_liters, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="text-sm {{ $item->remaining_liters > 0 ? 'text-orange-600' : 'text-gray-900' }}">
                                                    {{ number_format($item->remaining_liters, 2) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($item->remaining_liters <= 0)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Completed
                                                    </span>
                                                @elseif($item->fulfilled_liters > 0)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        In Progress
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="text-gray-500">No fuel items found</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(count($fuelItems) > 0)
                                    <tfoot class="bg-gray-50">
                                        <tr class="font-semibold">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Total</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                                {{ number_format($totalRequested, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-green-600">
                                                {{ number_format($totalFulfilled, 2) }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-right text-sm {{ $totalRemaining > 0 ? 'text-orange-600' : 'text-gray-900' }}">
                                                {{ number_format($totalRemaining, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap"></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    
                        <!-- Overall Progress Bar -->
                        @if($totalRequested > 0)
                            <div class="px-6 py-4 border-t border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Overall Delivery Progress</span>
                                    <span
                                        class="text-sm font-semibold text-indigo-600">{{ number_format(($totalFulfilled / $totalRequested) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-indigo-600 rounded-full h-3 transition-all duration-500"
                                        style="width: {{ ($totalFulfilled / $totalRequested) * 100 }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column - Additional Info -->
                <div class="space-y-6">
                    <!-- Requester Information Card -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Requester Information</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @php
$requester = $request->requester;
$name = 'N/A';
if ($requester) {
    if (isset($requester->first_name) && isset($requester->last_name)) {
        $name = $requester->first_name . ' ' . $requester->last_name;
    } elseif (isset($requester->name)) {
        $name = $requester->name;
    } elseif (isset($requester->full_name)) {
        $name = $requester->full_name;
    }
}
                                        @endphp
                                        {{ $name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $request->requester->email ?? 'N/A' }}</dd>
                                </div>
                                @php
$phone = $request->requester->phone ?? $request->requester->mobile_number ?? $request->requester->contact_number ?? null;
                                @endphp
                                @if($phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $phone }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Purchase Order Information Card -->
                    @if($request->purchaseOrderItem && $request->purchaseOrderItem->purchaseOrder)
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
                                <h3 class="text-lg font-medium text-gray-900">Purchase Order Details</h3>
                            </div>
                            <div class="px-4 py-5 sm:p-6">
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">PO Number</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $request->purchaseOrderItem->purchaseOrder->po_number ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Order Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ isset($request->purchaseOrderItem->purchaseOrder->order_date) ? \Carbon\Carbon::parse($request->purchaseOrderItem->purchaseOrder->order_date)->format('F d, Y') : 'N/A' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Requested Delivery Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ isset($request->purchaseOrderItem->purchaseOrder->request_delivery_date) ? \Carbon\Carbon::parse($request->purchaseOrderItem->purchaseOrder->request_delivery_date)->format('F d, Y') : 'N/A' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions Card -->
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
                            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-3">
                                @if($request->status == 'pending')
                                    <button wire:click="confirmCancel"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                                            </path>
                                        </svg>
                                        Cancel Request
                                    </button>
                                @endif
                                <a href="{{ route('client.delivery.index') }}"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                    View All Requests
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('swal:confirm', (event) => {
                    Swal.fire({
                        title: event.title || 'Are you sure?',
                        text: event.text || 'This action cannot be undone!',
                        icon: event.icon || 'warning',
                        showCancelButton: true,
                        confirmButtonColor: event.confirmButtonColor || '#d33',
                        cancelButtonColor: event.cancelButtonColor || '#3085d6',
                        confirmButtonText: event.confirmButtonText || 'Yes, cancel it!',
                        cancelButtonText: event.cancelButtonText || 'No, keep it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('confirmed');
                        }
                    });
                });
            });
        </script>
    @endpush
</div>