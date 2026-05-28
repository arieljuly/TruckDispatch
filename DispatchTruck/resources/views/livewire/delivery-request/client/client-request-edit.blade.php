<div>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Delivery Request</h1>
                    <p class="mt-1 text-sm text-gray-600">Update your delivery request #{{ $deliveryRequest->id }}</p>
                </div>
                <button type="button" wire:click="cancel"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cancel
                </button>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8">
            <form wire:submit.prevent="update">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
                        <h3 class="text-lg font-medium text-gray-900">Edit Request Details</h3>
                        <p class="mt-1 text-sm text-gray-500">Update the information below to modify your delivery request.</p>
                    </div>
                    
                    <div class="px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <!-- Area Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Area <span class="text-red-500">*</span></label>
                                <select wire:model="area_id" wire:change="areaChanged"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Select Area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                    @endforeach
                                </select>
                                @error('area_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Station Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fuel Station <span class="text-red-500">*</span></label>
                                <select wire:model="station_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">Select Station</option>
                                    @foreach($stations as $station)
                                        <option value="{{ $station->id }}">{{ $station->station_name }}</option>
                                    @endforeach
                                </select>
                                @error('station_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority <span class="text-red-500">*</span></label>
                                <select wire:model="priority"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                @error('priority') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Deadline -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Delivery Deadline <span class="text-red-500">*</span></label>
                                <input type="datetime-local" wire:model="deadline"
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('deadline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Fuel Items Section -->
                        <div class="mt-8">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-md font-medium text-gray-900">Fuel Items <span class="text-red-500">*</span></h4>
                                @if($purchase_order_id)
                                    <span class="text-xs text-gray-500">PO #: {{ $po_number }}</span>
                                @endif
                            </div>
                            
                            <!-- Available Fuel Types -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Add More Fuel Items</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    @foreach($fuelTypes as $fuelType)
                                        @php
                                            $isSelected = collect($selected_fuels)->contains('fuel_type_id', $fuelType->id);
                                        @endphp
                                        @if(!$isSelected)
                                            <button type="button" 
                                                    wire:click="addFuelItem({{ $fuelType->id }})"
                                                    class="text-left border rounded-lg p-3 hover:bg-gray-50 transition">
                                                <p class="font-medium text-gray-900">{{ $fuelType->fuel_name }}</p>
                                                <p class="text-sm text-gray-500">{{ $fuelType->fuel_code }}</p>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Selected Fuel Items -->
                            @if(count($selected_fuels) > 0)
                                <div class="border-t pt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Selected Items ({{ count($selected_fuels) }})</label>
                                    <div class="space-y-3">
                                        @foreach($selected_fuels as $index => $fuel)
                                            <div class="border rounded-lg p-4 bg-gray-50">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <p class="font-medium text-gray-900">{{ $fuel['fuel_name'] }}</p>
                                                                <p class="text-sm text-gray-500">{{ $fuel['fuel_code'] }}</p>
                                                            </div>
                                                            <button type="button" 
                                                                    wire:click="removeFuelItem({{ $index }})"
                                                                    class="text-red-600 hover:text-red-800">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div class="mt-3">
                                                            <label class="block text-sm font-medium text-gray-700">Quantity (Liters) *</label>
                                                            <input type="number" 
                                                                   step="0.001"
                                                                   wire:model="selected_fuels.{{ $index }}.quantity"
                                                                   wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                                   placeholder="Quantity in liters"
                                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                            @error("selected_fuels.{$index}.quantity") 
                                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p> 
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        <div class="bg-indigo-50 p-4 rounded-lg mt-3">
                                            <div class="flex justify-between items-center">
                                                <p class="text-sm font-medium text-gray-700">Total Quantity:</p>
                                                <p class="text-lg font-bold text-indigo-600">{{ number_format($this->total_quantity, 2) }} L</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 text-center">
                                    <p class="text-sm text-yellow-800">No fuel items added. Please add at least one fuel item.</p>
                                </div>
                            @endif
                            @error('selected_fuels') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <textarea wire:model="notes" rows="3"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                placeholder="Any special instructions for the delivery team..."></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 border-t border-gray-200">
                        <div class="flex justify-end space-x-3">
                            <button type="button" wire:click="cancel"
                                class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </button>
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update Request
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Only <strong>pending</strong> requests can be edited. Once a request is in progress or completed, you cannot modify it.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>