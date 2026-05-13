<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Truck Details</h1>
                <p class="mt-1 text-sm text-gray-600">View truck information and assignment history</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-3">
                <a href="{{ route('admin.trucks.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
                <a href="{{ route('admin.trucks.edit', $truck->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Truck
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
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
                            <label class="text-sm font-medium text-gray-500">Capacity</label>
                            <p class="mt-1 text-gray-900">{{ number_format($truck->capacity_ltrs, 2) }} Liters</p>
                        </div>
                        <div class="border-b pb-3">
                            <label class="text-sm font-medium text-gray-500">Available Liters</label>
                            <p class="mt-1 text-gray-900">{{ number_format($truck->available_ltrs, 2) }} Liters</p>
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
                                        'in-transit' => 'bg-blue-100 text-blue-800',
                                        'maintenance' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$truck->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($truck->status) }}
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
                    @if($truck->currentAssignment)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Driver:</span>
                                <span class="text-gray-900">{{ $truck->currentAssignment->driver->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">License:</span>
                                <span class="text-gray-900">{{ $truck->currentAssignment->driver->license_number ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Started:</span>
                                <span class="text-gray-900">{{ $truck->currentAssignment->start_time->format('M d, Y H:i') }}</span>
                            </div>
                            <div class="pt-3">
                                <button onclick="endAssignment({{ $truck->currentAssignment->id }})" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    End Assignment
                                </button>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No active driver assignment</p>
                    @endif
                </div>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Driver</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Start Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">End Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($assignments as $assignment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $assignment->driver->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $assignment->start_time->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $assignment->end_time ? $assignment->end_time->format('M d, Y H:i') : 'Active' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $assignment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No assignment history</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function endAssignment(assignmentId) {
        Swal.fire({
            title: 'End Assignment?',
            text: "This will mark the assignment as completed",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, end it!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('endAssignment', assignmentId);
            }
        });
    }
</script>