<div>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Assignment Details #{{ $assignment->id }}</h1>
                    <p class="mt-1 text-sm text-gray-600">View driver and truck assignment information</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.dispatch.assignments') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Assignments
                    </a>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $assignment->status === 'active' ? 'bg-green-100 text-green-800' : ($assignment->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($assignment->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Assignment Details Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Assignment Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Start Time</span>
                                <span class="text-sm font-medium text-gray-900">{{ $assignment->start_time->format('Y-m-d H:i:s') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">End Time</span>
                                <span class="text-sm font-medium text-gray-900">{{ $assignment->end_time?->format('Y-m-d H:i:s') ?? 'In Progress' }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-sm text-gray-500">Duration</span>
                                <span class="text-sm font-medium text-gray-900">
                                    @if($assignment->end_time)
                                        {{ $assignment->start_time->diffInHours($assignment->end_time) }} hours
                                    @else
                                        {{ $assignment->start_time->diffInHours(now()) }} hours (ongoing)
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Truck Information Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 18l4-4m0 0l4-4m-4 4V3m0 12H3m15 0h4M3 3h18M3 3v18m18-18v18"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Truck Information</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-900">{{ $assignment->truck->truck_name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $assignment->truck->plate_number ?? 'N/A' }}</div>
                            <div class="mt-3 pt-3 border-t border-gray-100 grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-xs text-gray-500">Capacity</div>
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format($assignment->truck->capacity_ltrs, 2) }} L</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500">Available Fuel</div>
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format($assignment->truck->available_ltrs, 2) }} L</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver Information Card -->
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Driver Information</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div class="text-xl font-bold text-gray-900">{{ $assignment->driver->user->full_name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">License: {{ $assignment->driver->licensed_number ?? 'N/A' }}</div>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Status: {{ ucfirst($assignment->driver->status ?? 'unknown') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            @if($assignment->status === 'active')
            <div class="mt-6">
                <button wire:click="completeAssignment" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Mark as Completed
                </button>
            </div>
            @endif
        </div>
    </div>
</div>