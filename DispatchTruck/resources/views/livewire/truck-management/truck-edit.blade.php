<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Truck</h1>
                <p class="mt-1 text-sm text-gray-600">Update truck information and compartments</p>
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

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Truck Information</h3>
                <p class="mt-1 text-sm text-gray-500">Update the details for this truck</p>
            </div>

            <div class="p-6">
                <form wire:submit.prevent="updateTruck">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                        <div>
                            <label for="truck_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Truck Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('truck_name') border-red-300 @enderror"
                                id="truck_name" wire:model="truck_name" placeholder="e.g., Truck A, Delivery Truck 1">
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
                                id="plate_number" wire:model="plate_number" placeholder="e.g., ABC-1234">
                            @error('plate_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="max_capacity_ltrs" class="block text-sm font-medium text-gray-700 mb-2">
                                Truck Max Capacity (L) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" step="0.001"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('max_capacity_ltrs') border-red-300 @enderror"
                                    id="max_capacity_ltrs" wire:model.live="max_capacity_ltrs" placeholder="0.000">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">L</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maximum total fuel capacity of the truck across all compartments</p>
                            @error('max_capacity_ltrs')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="current_area_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Area
                            </label>
                            <select
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('current_area_id') border-red-300 @enderror"
                                id="current_area_id" wire:model="current_area_id">
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
                                id="status" wire:model="status">
                                <option value="available">Available</option>
                                <option value="in_transit">In Transit</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Compartments Section -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h4 class="text-md font-medium text-gray-900">Fuel Compartments</h4>
                                <p class="text-sm text-gray-500">Manage fuel compartments for this truck</p>
                            </div>
                            <button type="button" wire:click="addCompartment"
                                class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-1 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Compartment
                            </button>
                        </div>

                        @foreach($compartments as $index => $compartment)
                            <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                                <div class="flex justify-between items-start mb-3">
                                    <h5 class="text-sm font-medium text-gray-700">Compartment {{ $index + 1 }}</h5>
                                    @if(count($compartments) > 1)
                                        <button type="button" wire:click="removeCompartment({{ $index }})"
                                            class="text-red-600 hover:text-red-800">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Compartment No. <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                            wire:model="compartments.{{ $index }}.compartment_no"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            placeholder="e.g., Tank 1">
                                        @error("compartments.{$index}.compartment_no")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Fuel Type <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="compartments.{{ $index }}.current_fuel_type_id"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">Select Fuel Type</option>
                                            @foreach($fuelTypes as $fuelType)
                                                <option value="{{ $fuelType->id }}">{{ $fuelType->fuel_name }} ({{ $fuelType->fuel_code }})</option>
                                            @endforeach
                                        </select>
                                        @error("compartments.{$index}.current_fuel_type_id")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Max Capacity (L) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" step="0.001" 
                                            wire:model.live="compartments.{{ $index }}.capacity_ltrs"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            placeholder="0.000">
                                        @error("compartments.{$index}.capacity_ltrs")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                            Current Fuel (L) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" step="0.001" 
                                            wire:model.live="compartments.{{ $index }}.loaded_ltrs"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                            placeholder="0.000">
                                        @error("compartments.{$index}.loaded_ltrs")
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Fuel Level Indicator -->
                                @if(!empty($compartment['capacity_ltrs']) && $compartment['capacity_ltrs'] > 0)
                                    @php
                                        $fuelPercentage = min(100, max(0, ($compartment['loaded_ltrs'] / $compartment['capacity_ltrs']) * 100));
                                        $fuelColor = $fuelPercentage > 50 ? 'bg-green-600' : ($fuelPercentage > 20 ? 'bg-yellow-500' : 'bg-red-600');
                                    @endphp
                                    <div class="mt-3">
                                        <div class="flex justify-between mb-1">
                                            <span class="text-xs text-gray-500">Fuel Level</span>
                                            <span class="text-xs text-gray-500">{{ number_format($fuelPercentage, 1) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="{{ $fuelColor }} h-1.5 rounded-full" style="width: {{ $fuelPercentage }}%"></div>
                                        </div>
                                        <div class="flex justify-between mt-1 text-xs text-gray-500">
                                            <span>Current Fuel: {{ number_format($compartment['loaded_ltrs'], 3) }} L</span>
                                            <span>Remaining Capacity: {{ number_format($compartment['capacity_ltrs'] - $compartment['loaded_ltrs'], 3) }} L</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @error('compartments')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Capacity Summary -->
                    @if(!empty($max_capacity_ltrs) && count($compartments) > 0)
                        @php
                            $totalCompartmentCapacity = 0;
                            foreach($compartments as $comp) {
                                $totalCompartmentCapacity += floatval($comp['capacity_ltrs'] ?? 0);
                            }
                            $remainingCapacityValue = max(0, floatval($max_capacity_ltrs) - $totalCompartmentCapacity);
                            $percentageUsed = $max_capacity_ltrs > 0 ? ($totalCompartmentCapacity / $max_capacity_ltrs) * 100 : 0;
                            $capacityColor = $percentageUsed > 100 ? 'bg-red-600' : ($percentageUsed > 80 ? 'bg-yellow-500' : 'bg-green-600');
                        @endphp
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-medium text-blue-900">Capacity Usage</span>
                                <span class="text-sm font-medium text-blue-900">{{ number_format($percentageUsed, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="{{ $capacityColor }} h-2.5 rounded-full transition-all duration-300" style="width: {{ min(100, $percentageUsed) }}%"></div>
                            </div>
                            <div class="flex justify-between mt-2 text-sm">
                                <span class="text-blue-800">Total Compartment Capacity: <strong>{{ number_format($totalCompartmentCapacity, 2) }}&nbspL</strong></span>
                                <span class="text-blue-800">Remaining Available: <strong>{{ number_format($remainingCapacityValue, 2) }}&nbspL</strong></span>
                            </div>
                            @if($totalCompartmentCapacity > $max_capacity_ltrs)
                                <p class="text-xs text-red-600 mt-2">
                                     Error: Total compartment capacity exceeds truck's maximum capacity!
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.trucks.index') }}"
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                            Cancel
                        </a>
                        <button type="submit"
                            @if(!empty($max_capacity_ltrs) && $totalCompartmentCapacity > $max_capacity_ltrs) disabled
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-400 cursor-not-allowed"
                            @else
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150"
                            @endif>
                            Update Truck
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>