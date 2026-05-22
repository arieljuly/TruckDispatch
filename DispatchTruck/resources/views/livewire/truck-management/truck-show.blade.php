<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Truck Details</h1>
                <p class="mt-1 text-sm text-gray-600">View truck information, compartments, and assignment history</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-3">
                <a href="{{ route('admin.trucks.index') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
                <a href="{{ route('admin.trucks.edit', $truck->id) }}" 
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Truck
                </a>
            </div>
        </div>

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

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Truck Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Truck Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border-b pb-3">
                            <label class="text-sm font-medium text-gray-500">Truck Name</label>
                            <p class="mt-1 text-gray-900 font-semibold">{{ $truck->truck_name }}</p>
                        </div>
                        <div class="border-b pb-3">
                            <label class="text-sm font-medium text-gray-500">Plate Number</label>
                            <p class="mt-1 text-gray-900">{{ $truck->plate_number }}</p>
                        </div>
                        <div class="border-b pb-3">
                            <label class="text-sm font-medium text-gray-500">Truck Max Capacity</label>
                            <p class="mt-1 text-gray-900">{{ number_format($truck->max_capacity_ltrs, 2) }} Liters</p>
                        </div>
                        <div class="border-b pb-3">
                            <label class="text-sm font-medium text-gray-500">Total Compartment Capacity</label>
                            <p class="mt-1 text-gray-900">{{ number_format($totalCapacity, 2) }} Liters</p>
                            @php
$capacityPercentage = ($truck->max_capacity_ltrs > 0) ? ($totalCapacity / $truck->max_capacity_ltrs) * 100 : 0;
$capacityColor = $capacityPercentage > 100 ? 'bg-red-600' : ($capacityPercentage > 80 ? 'bg-yellow-500' : 'bg-green-600');
                            @endphp
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $capacityColor }} h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $capacityPercentage) }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Remaining available capacity: {{ number_format(max(0, $truck->max_capacity_ltrs - $totalCapacity), 2) }} L
                            </p>
                        </div>
                        <div class="border-b pb-3">
                            <label class="text-sm font-medium text-gray-500">Total Current Fuel</label>
                            <p class="mt-1 text-gray-900">{{ number_format($totalLoaded, 2) }} Liters</p>
                            @php
$fuelPercentage = ($totalCapacity > 0) ? ($totalLoaded / $totalCapacity) * 100 : 0;
$fuelColor = $fuelPercentage > 50 ? 'bg-green-600' : ($fuelPercentage > 20 ? 'bg-yellow-500' : 'bg-red-600');
                            @endphp
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $fuelColor }} h-2 rounded-full transition-all duration-300" style="width: {{ min(100, $fuelPercentage) }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                Remaining capacity in compartments: {{ number_format($totalAvailable, 2) }} L
                            </p>
                        </div>
                        <div class="border-b pb-3">
                            <label class="text-sm font-medium text-gray-500">Number of Compartments</label>
                            <p class="mt-1 text-gray-900">{{ $truck->compartments->count() }}</p>
                        </div>
                        <div class="border-b pb-3">
                            <label class="text-sm font-medium text-gray-500">Current Location</label>
                            <p class="mt-1 text-gray-900">{{ $truck->currentArea->area_name ?? 'Not assigned' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <div class="mt-1">
                                @php
$statusColors = [
    'available' => 'bg-green-100 text-green-800',
    'in_transit' => 'bg-blue-100 text-blue-800',
    'maintenance' => 'bg-red-100 text-red-800',
];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$truck->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst(str_replace('_', ' ', $truck->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Assignment -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Current Assignment</h3>
                </div>
                <div class="p-6">
                    @if($truck->currentAssignment && $truck->currentAssignment->driver)
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b">
                                <span class="text-sm font-medium text-gray-500">Driver:</span>
                                <span class="text-gray-900 font-medium">
                                    {{ trim($truck->currentAssignment->driver->user->first_name ?? '') }} 
                                    {{ trim($truck->currentAssignment->driver->user->last_name ?? '') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b">
                                <span class="text-sm font-medium text-gray-500">License:</span>
                                <span class="text-gray-900">{{ $truck->currentAssignment->driver->licensed_number ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between pb-2 border-b">
                                <span class="text-sm font-medium text-gray-500">Started:</span>
                                <span class="text-gray-900">{{ $truck->currentAssignment->start_time->format('M d, Y H:i') }}</span>
                            </div>
                            @if($truck->currentAssignment->end_time)
                                <div class="flex items-center justify-between pb-2 border-b">
                                    <span class="text-sm font-medium text-gray-500">Expected End:</span>
                                    <span class="text-gray-900">{{ $truck->currentAssignment->end_time->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                            <div class="pt-3">
                                <button onclick="endAssignment({{ $truck->currentAssignment->id }})" 
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                    End Assignment
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <p class="mt-2 text-gray-500">No active driver assignment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Compartments Section -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Fuel Compartments</h3>
                        <p class="text-sm text-gray-500">View all compartments and their fuel details</p>
                    </div>
                    <div class="text-sm text-gray-600">
                        Total Capacity: <span class="font-semibold">{{ number_format($totalCapacity, 2) }} L</span> | 
                        Total Current Fuel: <span class="font-semibold">{{ number_format($totalLoaded, 2) }} L</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @forelse($truck->compartments as $index => $compartment)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500">Compartment Number</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $compartment->compartment_no }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500">Fuel Type</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                        {{ $compartment->fuelType->fuel_name ?? 'Unknown' }}
                                    </span>
                                    <span class="ml-1 text-xs text-gray-500">({{ $compartment->fuelType->fuel_code ?? 'N/A' }})</span>
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500">Max Capacity</label>
                                <p class="mt-1 text-sm text-gray-900">{{ number_format($compartment->capacity_ltrs, 3) }} L</p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500">Current Fuel</label>
                                <p class="mt-1 text-sm text-gray-900">{{ number_format($compartment->loaded_ltrs, 3) }} L</p>
                            </div>
                        </div>

                        <!-- Fuel Level Indicator -->
                        @if($compartment->capacity_ltrs > 0)
                            @php
        $fuelPercentage = min(100, max(0, ($compartment->loaded_ltrs / $compartment->capacity_ltrs) * 100));
        $fuelColor = $fuelPercentage > 50 ? 'bg-green-600' : ($fuelPercentage > 20 ? 'bg-yellow-500' : 'bg-red-600');
                            @endphp
                            <div class="mt-3">
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs text-gray-500">Fuel Level</span>
                                    <span class="text-xs text-gray-500">{{ number_format($fuelPercentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $fuelColor }} h-2 rounded-full transition-all duration-300" style="width: {{ $fuelPercentage }}%"></div>
                                </div>
                                <div class="flex justify-between mt-1 text-xs text-gray-500">
                                    <span>Current Fuel: {{ number_format($compartment->loaded_ltrs, 3) }} L</span>
                                    <span>Remaining Capacity: {{ number_format($compartment->capacity_ltrs - $compartment->loaded_ltrs, 3) }} L</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="mt-2 text-gray-500">No compartments found for this truck.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Assignment History -->
        <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Assignment History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($assignments as $assignment)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ trim($assignment->driver->user->first_name ?? '') }} 
                                        {{ trim($assignment->driver->user->last_name ?? '') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        License #: {{ $assignment->driver->licensed_number ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $assignment->start_time->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    @if($assignment->end_time)
                                        {{ $assignment->end_time->format('M d, Y H:i') }}
                                    @else
                                        <span class="text-green-600 font-medium">Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
    $assignmentStatusColors = [
        'active' => 'bg-green-100 text-green-800',
        'completed' => 'bg-gray-100 text-gray-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $assignmentStatusColors[$assignment->status] ?? 'bg-gray-100' }}">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">No assignment history found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($assignments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>
    <script>
        function endAssignment(assignmentId) {
            Swal.fire({
                title: 'End Assignment?',
                text: "This will mark the assignment as completed and make the truck available",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, end it!',
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
                    @this.call('endAssignment', assignmentId);
                }
            });
        }
    </script>
</div>