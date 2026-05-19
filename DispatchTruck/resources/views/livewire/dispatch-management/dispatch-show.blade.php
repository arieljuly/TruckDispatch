<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dispatch Session #{{ $session->id }}</h1>
                    <p class="mt-1 text-sm text-gray-600">View dispatch details and AI prediction information</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.dispatch.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to History
                    </a>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $session->status === 'executed' ? 'bg-green-100 text-green-800' : ($session->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($session->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Dispatch Details Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Dispatch Details</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Date Created</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $session->created_at->format('Y-m-d H:i:s') }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Algorithm Used</span>
                                <span class="text-sm font-medium text-gray-900">{{ $session->algorithm_used }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Executed By</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $session->executor?->full_name ?? 'Not executed' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Notes</span>
                                <span class="text-sm text-gray-600">{{ $session->notes ?? 'None' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Prediction Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">AI Prediction</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-indigo-50 rounded-lg p-4 mb-4 text-center">
                            <div class="text-3xl font-bold text-indigo-900">
                                {{ number_format($session->predicted_fuel_liters, 2) }} <span
                                    class="text-base">Liters</span></div>
                            @if($session->actual_fuel_used)
                                <div class="text-sm text-gray-600 mt-1">Actual:
                                    {{ number_format($session->actual_fuel_used, 2) }} L</div>
                                @if($predictionError)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mt-2
                                                {{ $predictionError < 10 ? 'bg-green-100 text-green-800' : ($predictionError < 20 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $predictionError }}% error
                                    </span>
                                @endif
                            @endif
                        </div>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Model Version</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $session->prediction_model_version ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Optimization Method</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ $session->optimization_method ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-sm text-gray-500">Confidence</span>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ isset($session->prediction_confidence) ? round($session->prediction_confidence * 100) . '%' : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trip Parameters Card -->
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Trip Parameters</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-500">Distance</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ number_format($session->distance_km, 2) }} km</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-500">Duration</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ number_format($session->actual_duration_hours, 2) }} hrs</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-500">Avg MPG</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ number_format($session->average_mpg, 2) }} km/L</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-500">Idle Time</div>
                            <div class="text-lg font-semibold text-gray-900">
                                {{ number_format($session->idle_time_hours, 2) }} hrs</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-500">Detention</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $session->detention_minutes ?? 0 }} mins
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-500">Delay</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $session->delay_minutes ?? 0 }} mins
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <div class="text-xs text-gray-500">On Time</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $session->on_time_flag ? 'Yes' : 'No' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trucks Information -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-yellow-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Recommended Truck</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($session->recommendedTruck)
                            <div class="text-center">
                                <div class="text-xl font-bold text-gray-900">{{ $session->recommendedTruck->truck_name }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $session->recommendedTruck->plate_number }}</div>
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <div class="text-sm text-gray-600">Available Fuel: <span
                                            class="font-semibold">{{ number_format($session->recommendedTruck->available_ltrs, 2) }}
                                            L</span></div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-gray-500">No recommendation available</div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 18l4-4m0 0l4-4m-4 4V3m0 12H3m15 0h4M3 3h18M3 3v18m18-18v18"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Assigned Truck</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($session->assignedTruck)
                            <div class="text-center">
                                <div class="text-xl font-bold text-gray-900">{{ $session->assignedTruck->truck_name }}</div>
                                <div class="text-sm text-gray-500">{{ $session->assignedTruck->plate_number }}</div>
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <div class="text-sm text-gray-600">Available Fuel: <span
                                            class="font-semibold">{{ number_format($session->assignedTruck->available_ltrs, 2) }}
                                            L</span></div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-gray-500">No truck assigned yet</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Allocations Table -->
            @if($allocations->count() > 0)
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Allocations</h3>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Truck</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Area</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Allocated Liters</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Distance</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allocations as $allocation)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $allocation->truck?->truck_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $allocation->area?->area_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                            {{ number_format($allocation->liters_allocated, 2) }} L</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ number_format($allocation->distance_used, 2) }} km</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $allocation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($allocation->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Feedback Form -->
            @if($session->status === 'executed' && !$session->actual_fuel_used)
                <div class="mt-6">
                    @if(!$showFeedbackForm)
                        <button wire:click="$set('showFeedbackForm', true)"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            Record Actual Fuel Usage
                        </button>
                    @else
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-900">Record Actual Fuel Usage</h3>
                                <p class="text-sm text-gray-500">Help improve AI predictions by providing feedback</p>
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Actual Fuel Used
                                        (Liters)</label>
                                    <input type="number" wire:model="actualFuelUsed" step="0.1"
                                        class="w-full md:w-1/3 rounded-lg border-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('actualFuelUsed') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex space-x-3">
                                    <button wire:click="recordActualFuel"
                                        class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 transition-colors duration-150">Save</button>
                                    <button wire:click="$set('showFeedbackForm', false)"
                                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150">Cancel</button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>