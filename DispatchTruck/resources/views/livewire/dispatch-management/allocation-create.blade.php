<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Allocation</h1>
                    <p class="mt-1 text-sm text-gray-600">Assign fuel allocation to a truck for dispatch</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.dispatch.allocations') }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Allocations
                    </a>
                </div>
            </div>

            <!-- Success/Error Messages -->
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

            <!-- Selected Truck Info Card -->
            @if($truckId)
                @php $selectedTruck = $trucks->firstWhere('id', $truckId); @endphp
                @if($selectedTruck)
                    <div class="mb-6 bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-indigo-800">Selected Truck: {{ $selectedTruck->truck_name }}</h4>
                                    <p class="text-xs text-indigo-600 mt-1">
                                        Plate: {{ $selectedTruck->plate_number }} | 
                                        Capacity: {{ number_format($selectedTruck->capacity_ltrs, 2) }} L | 
                                        Available: {{ number_format($selectedTruck->available_ltrs, 2) }} L
                                    </p>
                                </div>
                            </div>
                            @if($litersAllocated && $litersAllocated > $selectedTruck->available_ltrs)
                                <div class="text-red-600 text-sm font-medium">
                                    ⚠️ Exceeds available fuel by {{ number_format($litersAllocated - $selectedTruck->available_ltrs, 2) }} L
                                </div>
                            @elseif($litersAllocated)
                                <div class="text-green-600 text-sm font-medium">
                                    ✓ Within available fuel
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            <!-- Form Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-white">
                    <h3 class="text-lg font-medium text-gray-900">Allocation Details</h3>
                    <p class="mt-1 text-sm text-gray-500">Fill in the allocation information below</p>
                </div>
                
                <div class="p-6">
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Dispatch Session -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Dispatch Session <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="dispatchSessionId" 
                                    class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Dispatch Session</option>
                                    @foreach($dispatchSessions as $session)
                                        <option value="{{ $session->id }}">
                                            #{{ $session->id }} - {{ $session->created_at->format('Y-m-d H:i') }} 
                                            (Predicted: {{ number_format($session->predicted_fuel_liters, 2) }} L)
                                        </option>
                                    @endforeach
                                </select>
                                @error('dispatchSessionId') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Truck -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Truck <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="truckId" 
                                    class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Truck</option>
                                    @foreach($trucks as $truck)
                                        <option value="{{ $truck->id }}" 
                                            {{ $litersAllocated && $litersAllocated > $truck->available_ltrs ? 'disabled' : '' }}>
                                            {{ $truck->truck_name }} ({{ $truck->plate_number }}) - 
                                            Available: {{ number_format($truck->available_ltrs, 2) }} L
                                            @if($litersAllocated && $litersAllocated > $truck->available_ltrs)
                                                - Insufficient Fuel
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('truckId') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Area -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Delivery Area <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="areaId" 
                                    class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">
                                            {{ $area->area_name }} (Required: {{ number_format($area->required_liters, 2) }} L)
                                        </option>
                                    @endforeach
                                </select>
                                @error('areaId') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Allocated Liters -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Allocated Liters <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                        wire:model.live="litersAllocated"
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Enter allocated fuel in liters">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">L</span>
                                    </div>
                                </div>
                                @error('litersAllocated') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @else
                                    <p class="mt-1 text-xs text-gray-500">Recommended: Based on distance and truck efficiency</p>
                                @enderror
                            </div>

                            <!-- Distance Used -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Distance (km) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" 
                                        wire:model="distanceUsed" 
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Enter distance in kilometers">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">km</span>
                                    </div>
                                </div>
                                @error('distanceUsed') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="status" 
                                    class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                </select>
                                @error('status') 
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Primary Area Toggle -->
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" 
                                        wire:model="isPrimaryArea" 
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div class="ml-3">
                                    <label class="text-sm font-medium text-gray-700">Mark as Primary Area</label>
                                    <p class="text-xs text-gray-500">Primary areas get priority for fuel allocation</p>
                                </div>
                            </div>
                        </div>

                        <!-- Notes (Optional) -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                            <textarea 
                                wire:model="notes" 
                                rows="3"
                                class="w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Add any additional notes about this allocation..."></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.dispatch.allocations') }}" 
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                Cancel
                            </a>
                            <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                <div class="flex items-center">
                                    <svg wire:loading.remove class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    <svg wire:loading class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove>Create Allocation</span>
                                    <span wire:loading>Creating...</span>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">Fuel Calculation</h4>
                            <p class="text-xs text-blue-600 mt-1">Fuel = Distance × Consumption Rate + Buffer</p>
                            <p class="text-xs text-blue-500 mt-1">Standard rate: ~2.5 L/100km</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-4 border border-green-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-green-800">AI Prediction</h4>
                            <p class="text-xs text-green-600 mt-1">Select a dispatch session to auto-fill predicted fuel from AI model</p>
                            <p class="text-xs text-green-500 mt-1">Based on distance, duration, and traffic patterns</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800">Truck Availability</h4>
                            <p class="text-xs text-yellow-600 mt-1">Only trucks with 'available' status are shown</p>
                            <p class="text-xs text-yellow-500 mt-1">Trucks with insufficient fuel are disabled</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>