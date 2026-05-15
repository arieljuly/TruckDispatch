<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Truck</h1>
                <p class="mt-1 text-sm text-gray-600">Add a new truck to your fleet</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.trucks.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
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

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Truck Information</h3>
                <p class="mt-1 text-sm text-gray-500">Fill in the details for the new truck</p>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="createTruck">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                        <div>
                            <label for="truck_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Truck Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('truck_name') border-red-300 @enderror"
                                id="truck_name" 
                                wire:model.live="truck_name" 
                                placeholder="e.g., Truck A, Delivery Truck 1">
                            @error('truck_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="plate_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Plate Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('plate_number') border-red-300 @enderror"
                                id="plate_number" 
                                wire:model.live="plate_number" 
                                placeholder="e.g., ABC-1234">
                            @error('plate_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="capacity_ltrs" class="block text-sm font-medium text-gray-700 mb-2">
                                Capacity (Liters) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                    step="0.01"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('capacity_ltrs') border-red-300 @enderror"
                                    id="capacity_ltrs" 
                                    wire:model.live="capacity_ltrs" 
                                    placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">L</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maximum fuel capacity of the truck in liters</p>
                            @error('capacity_ltrs')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="available_ltrs" class="block text-sm font-medium text-gray-700 mb-2">
                                Available Liters <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                    step="0.01"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('available_ltrs') border-red-300 @enderror"
                                    id="available_ltrs" 
                                    wire:model.live="available_ltrs" 
                                    placeholder="0.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">L</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Current fuel available (cannot exceed capacity)</p>
                            @error('available_ltrs')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="current_area_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Area
                            </label>
                            <select
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('current_area_id') border-red-300 @enderror"
                                id="current_area_id" 
                                wire:model="current_area_id">
                                <option value="">Select Area</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                @endforeach
                            </select>
                            @error('current_area_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('status') border-red-300 @enderror"
                                id="status" 
                                wire:model="status">
                                <option value="available">Available</option>
                                <option value="in-transit">In Transit</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Live Capacity vs Available Indicator -->
                    @if(!empty($capacity_ltrs) && !empty($available_ltrs))
                        <div class="mb-6">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Fuel Level</span>
                                <span class="text-sm font-medium text-gray-700">
                                    {{ number_format(($available_ltrs / $capacity_ltrs) * 100, 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                @php
                                    $percentage = min(100, max(0, ($available_ltrs / $capacity_ltrs) * 100));
                                    $color = $percentage > 50 ? 'bg-green-600' : ($percentage > 20 ? 'bg-yellow-500' : 'bg-red-600');
                                @endphp
                                <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ number_format($available_ltrs, 2) }} L / {{ number_format($capacity_ltrs, 2) }} L
                            </p>
                        </div>
                    @endif

                    <!-- Validation Warning -->
                    @if(!empty($capacity_ltrs) && !empty($available_ltrs) && floatval($available_ltrs) > floatval($capacity_ltrs))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        <strong>Error:</strong> Available liters ({{ number_format($available_ltrs, 2) }} L) cannot exceed capacity ({{ number_format($capacity_ltrs, 2) }} L).
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.trucks.index') }}"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                            Cancel
                        </a>
                        <button type="submit"
                            @if(!empty($capacity_ltrs) && !empty($available_ltrs) && floatval($available_ltrs) > floatval($capacity_ltrs))
                                disabled
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed"
                            @else
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150"
                            @endif>
                            Create Truck
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>