<div>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Create Purchase Order & Delivery Request</h1>
                    <p class="mt-1 text-sm text-gray-600">Create a purchase order and request fuel delivery</p>
                </div>
                <a href="{{ route('client.delivery.index') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </a>
            </div>
            
            <!-- Progress Steps -->
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg font-medium text-gray-900">Order Progress</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 text-center">
                            <div class="rounded-full {{ $step >= 1 ? 'bg-indigo-600' : 'bg-gray-300' }} w-8 h-8 flex items-center justify-center text-white mx-auto">
                                1
                            </div>
                            <p class="text-xs mt-2">Location</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="rounded-full {{ $step >= 2 ? 'bg-indigo-600' : 'bg-gray-300' }} w-8 h-8 flex items-center justify-center text-white mx-auto">
                                2
                            </div>
                            <p class="text-xs mt-2">Order Items</p>
                        </div>
                        <div class="flex-1 text-center">
                            <div class="rounded-full {{ $step >= 3 ? 'bg-indigo-600' : 'bg-gray-300' }} w-8 h-8 flex items-center justify-center text-white mx-auto">
                                3
                            </div>
                            <p class="text-xs mt-2">Review & Submit</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="submit">
                <!-- Step 1: Location -->
                <div class="{{ $step != 1 ? 'hidden' : '' }}">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg font-medium text-gray-900">Select Delivery Location</h3>
                            <p class="mt-1 text-sm text-gray-500">Choose the area and station for fuel delivery</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Area *</label>
                                    <select wire:model="selected_area_id" wire:change="areaChanged" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="">Select Area</option>
                                        @foreach($areas as $area)
                                            <option value="{{ $area->id }}">{{ $area->area_name }} ({{ $area->area_code }})</option>
                                        @endforeach
                                    </select>
                                    @error('selected_area_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Station *</label>
                                    <select wire:model="selected_station_id" wire:change="stationChanged" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="">Select Station</option>
                                        @foreach($stations as $station)
                                            <option value="{{ $station->id }}">{{ $station->station_name }} ({{ $station->station_code }})</option>
                                        @endforeach
                                    </select>
                                    @error('selected_station_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            
                            @if($debug_message)
                                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                    <p class="text-sm text-yellow-800">{{ $debug_message }}</p>
                                </div>
                            @endif
                            
                            <div class="mt-6 flex justify-end">
                                <button type="button" wire:click="nextStep" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Next: Add Order Items
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2: Order Items -->
                <div class="{{ $step != 2 ? 'hidden' : '' }}">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                            <p class="mt-1 text-sm text-gray-500">Add fuel types and quantities for your purchase order</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            
                            <!-- Selected Station Info -->
                            @if($selected_station_id)
                                <div class="mb-4 bg-blue-50 border border-blue-200 rounded-md p-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>Station:</strong> 
                                        @php
                                            $station = $stations->firstWhere('id', $selected_station_id);
                                        @endphp
                                        {{ $station->station_name ?? 'Loading...' }}
                                    </p>
                                </div>
                            @endif
                            
                            <!-- Available Fuel Types -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Add Fuel Items</label>
                                
                                @if(count($fuelTypes) > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                                        @foreach($fuelTypes as $fuelType)
                                            @php
                                                $isSelected = collect($selected_fuels)->contains('fuel_type_id', $fuelType->id);
                                            @endphp
                                            @if(!$isSelected)
                                                <div class="border rounded-lg p-3 flex justify-between items-center hover:bg-gray-50">
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $fuelType->fuel_name }}</p>
                                                        <p class="text-sm text-gray-500">{{ $fuelType->fuel_code }}</p>
                                                    </div>
                                                    <button type="button" 
                                                            wire:click="addFuelToRequest({{ $fuelType->id }})"
                                                            class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                                        Add Item
                                                    </button>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                        <p class="text-sm text-yellow-800">No fuel types found in the system.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Selected Order Items -->
                            @if(count($selected_fuels) > 0)
                                <div class="border-t pt-4 mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Order Items ({{ count($selected_fuels) }})
                                    </label>
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
                                                                    wire:click="removeSelectedFuel({{ $index }})"
                                                                    class="text-red-600 hover:text-red-800 ml-2">
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
                                                                   wire:change="updateFuelQuantity({{ $index }}, $event.target.value)"
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
                                <div class="border-t pt-4 mt-4">
                                    <div class="bg-gray-50 border border-gray-200 rounded-md p-4 text-center">
                                        <p class="text-sm text-gray-600">Click "Add Item" above to add fuel items to your purchase order.</p>
                                    </div>
                                </div>
                            @endif
                            
                            @error('selected_fuels') 
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                            
                            <div class="mt-6 flex justify-between">
                                <button type="button" wire:click="previousStep" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </button>
                                <button type="button" wire:click="nextStep" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Next: Review Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 3: Review & Submit -->
                <div class="{{ $step != 3 ? 'hidden' : '' }}">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                            <h3 class="text-lg font-medium text-gray-900">Review Order & Delivery Request</h3>
                            <p class="mt-1 text-sm text-gray-500">Review and submit your purchase order</p>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <div class="space-y-6">
                                <!-- Delivery Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Requested Delivery Date *</label>
                                    <input type="date" wire:model="request_delivery_date" class="mt-1 block w-full rounded-md border-gray-300">
                                    <p class="mt-1 text-xs text-gray-500">When do you need the fuel to be delivered?</p>
                                    @error('request_delivery_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                
                                <!-- Notes -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                                    <textarea wire:model="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300" placeholder="Any special instructions or notes for this order..."></textarea>
                                </div>
                                
                                <!-- Summary Card -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900 mb-3">Order Summary</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm py-2 border-b border-gray-200">
                                            <span class="text-gray-600">Area:</span>
                                            <span class="font-medium text-gray-900">
                                                @php
                                                    $area = collect($areas)->firstWhere('id', $selected_area_id);
                                                @endphp
                                                {{ $area['area_name'] ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between text-sm py-2 border-b border-gray-200">
                                            <span class="text-gray-600">Station:</span>
                                            <span class="font-medium text-gray-900">
                                                @php
                                                    $station = collect($stations)->firstWhere('id', $selected_station_id);
                                                @endphp
                                                {{ $station['station_name'] ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between text-sm py-2 border-b border-gray-200">
                                            <span class="text-gray-600">Delivery Date:</span>
                                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($request_delivery_date)->format('F d, Y') }}</span>
                                        </div>
                                        
                                        <div class="border-t pt-2 mt-2">
                                            <p class="font-medium text-gray-900 mb-2">Order Items:</p>
                                            @foreach($selected_fuels as $fuel)
                                                <div class="flex justify-between text-sm py-1 pl-4">
                                                    <span class="text-gray-600">{{ $fuel['fuel_name'] }}</span>
                                                    <span class="font-medium text-gray-900">{{ number_format($fuel['quantity'], 2) }} L</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="flex justify-between text-sm py-2 border-t border-gray-200 mt-2 pt-2">
                                            <span class="font-bold">Total Quantity</span>
                                            <span class="font-bold">{{ number_format($this->total_quantity, 2) }} L</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-between">
                                <button type="button" wire:click="previousStep" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </button>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Submit Purchase Order & Delivery Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('order-submitted', (event) => {
            Swal.fire({
                title: 'Success!',
                text: 'Purchase Order #' + event.poNumber + ' has been created successfully!',
                icon: 'success',
                confirmButtonText: 'View My Orders',
                confirmButtonColor: '#3085d6',
                showCancelButton: true,
                cancelButtonText: 'Stay Here',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("client.delivery.index") }}';
                } else {
                    location.reload();
                }
            });
        });
    });
</script>
@endpush