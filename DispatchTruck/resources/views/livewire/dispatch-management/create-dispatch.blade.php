<div>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-gray-900">Create New Dispatch</h1>
                <p class="mt-1 text-sm text-gray-500">Use AI to predict fuel requirements and find the best truck</p>
            </div>

            <!-- Modern Progress Steps -->
            <div class="mb-12">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex flex-col items-center">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold transition-all duration-300 {{ $step >= 1 ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg' : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                                1
                            </div>
                            <div class="absolute -bottom-7 left-1/2 transform -translate-x-1/2 text-xs font-medium whitespace-nowrap {{ $step >= 1 ? 'text-indigo-600' : 'text-gray-400' }}">
                                Trip Details
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 h-0.5 {{ $step >= 2 ? 'bg-gradient-to-r from-indigo-600 to-indigo-400' : 'bg-gray-200' }}"></div>

                    <div class="flex-1 flex flex-col items-center">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold transition-all duration-300 {{ $step >= 2 ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg' : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                                2
                            </div>
                            <div class="absolute -bottom-7 left-1/2 transform -translate-x-1/2 text-xs font-medium whitespace-nowrap {{ $step >= 2 ? 'text-indigo-600' : 'text-gray-400' }}">
                                AI Prediction
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 h-0.5 {{ $step >= 3 ? 'bg-gradient-to-r from-indigo-600 to-indigo-400' : 'bg-gray-200' }}"></div>

                    <div class="flex-1 flex flex-col items-center">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold transition-all duration-300 {{ $step >= 3 ? 'bg-gradient-to-r from-indigo-600 to-indigo-500 text-white shadow-lg' : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                                3
                            </div>
                            <div class="absolute -bottom-7 left-1/2 transform -translate-x-1/2 text-xs font-medium whitespace-nowrap {{ $step >= 3 ? 'text-indigo-600' : 'text-gray-400' }}">
                                Select Truck
                            </div>
                        </div>
                    </div>
                    <div class="flex-1 h-0.5 {{ $step >= 4 ? 'bg-gradient-to-r from-indigo-600 to-indigo-400' : 'bg-gray-200' }}"></div>

                    <div class="flex-1 flex flex-col items-center">
                        <div class="relative">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold transition-all duration-300 {{ $step >= 4 ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg' : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                                4
                            </div>
                            <div class="absolute -bottom-7 left-1/2 transform -translate-x-1/2 text-xs font-medium whitespace-nowrap {{ $step >= 4 ? 'text-green-600' : 'text-gray-400' }}">
                                Confirm
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alert Messages -->
            @if(session()->has('error'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-red-700">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session()->has('prediction_success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-green-700">{{ session('prediction_success') }}</span>
                    </div>
                </div>
            @endif

            <!-- STEP 1: Trip Information -->
            @if($step == 1)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Dispatch Information</h2>
                                <p class="text-sm text-gray-500">Enter trip details for AI-powered fuel prediction</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Delivery Area <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="areaId" class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200">
                                    <option value="">Select Area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->area_name }}</option>
                                    @endforeach
                                </select>
                                @error('areaId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Distance (km) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" wire:model="distance_km" step="0.1" 
                                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200 pl-4 pr-12" 
                                            placeholder="e.g., 50">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">km</span>
                                    </div>
                                    @error('distance_km') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Duration (hours) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" wire:model="duration_hours" step="0.5" 
                                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200 pl-4 pr-12" 
                                            placeholder="e.g., 2.5">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">hrs</span>
                                    </div>
                                    @error('duration_hours') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Average Fuel Efficiency (km/L)
                                </label>
                                <input type="number" wire:model="average_mpg" step="0.1" 
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200" 
                                    placeholder="e.g., 6">
                                <p class="mt-1 text-xs text-gray-400">Typical truck: 5-7 km/L</p>
                                @error('average_mpg') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Idle Time</label>
                                    <div class="relative">
                                        <input type="number" wire:model="idle_time_hours" step="0.5" 
                                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200 pl-4 pr-12" 
                                            placeholder="0">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">hrs</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Detention</label>
                                    <div class="relative">
                                        <input type="number" wire:model="detention_minutes" 
                                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200 pl-4 pr-12" 
                                            placeholder="0">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">min</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Delay</label>
                                    <div class="relative">
                                        <input type="number" wire:model="delay_minutes" 
                                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200 pl-4 pr-12" 
                                            placeholder="0">
                                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">min</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="on_time_flag" 
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                                    <label class="ml-3 text-sm font-medium text-gray-700">On-Time Delivery Expected</label>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Affects fuel prediction accuracy
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Notes (Optional)</label>
                                <textarea wire:model="notes" rows="3" 
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-all duration-200" 
                                    placeholder="Any special instructions or remarks..."></textarea>
                            </div>
                        </div>

                        <div class="mt-8">
                            <button wire:click="predictFuel" 
                                class="w-full bg-gradient-to-r from-indigo-600 to-indigo-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-indigo-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    <span>Predict Fuel Requirement with AI</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- STEP 2: AI Prediction Results -->
            @if($step == 2)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">AI Prediction Results</h2>
                                <p class="text-sm text-gray-500">Machine learning model analysis</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-8 mb-8 border border-indigo-200">
                            <div class="text-center">
                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-200 text-indigo-800 text-xs font-semibold mb-4">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    AI PREDICTION
                                </div>
                                <div class="text-6xl font-black text-indigo-900 mb-2">{{ number_format($predicted_fuel_liters, 1) }} <span class="text-2xl font-medium text-indigo-700">Liters</span></div>
                                <div class="text-sm text-indigo-700 mb-4">Predicted Fuel Requirement</div>

                                <div class="max-w-md mx-auto">
                                    <div class="flex justify-between text-xs text-indigo-700 mb-1">
                                        <span>Confidence Score</span>
                                        <span>{{ round($confidence_score * 100) }}%</span>
                                    </div>
                                    <div class="w-full bg-indigo-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $confidence_score * 100 }}%"></div>
                                    </div>
                                </div>

                                @if($is_fallback)
                                    <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs">
                                        Using fallback calculation (AI service unavailable)
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                                <div class="text-xs text-gray-500 mb-1">Distance</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $distance_km }} <span class="text-sm font-normal">km</span></div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                                <div class="text-xs text-gray-500 mb-1">Duration</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $duration_hours }} <span class="text-sm font-normal">hrs</span></div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                                <div class="text-xs text-gray-500 mb-1">Efficiency</div>
                                <div class="text-lg font-semibold text-gray-900">{{ $average_mpg }} <span class="text-sm font-normal">km/L</span></div>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4 text-center border border-gray-100">
                                <div class="text-xs text-gray-500 mb-1">Model</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $model_version }}</div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button wire:click="previousStep" 
                                class="flex-1 px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                                ← Back
                            </button>
                            <button wire:click="getRecommendations" 
                                class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-indigo-600 transition-all duration-200 shadow-lg">
                                Find Best Truck →
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- STEP 3: Select Truck -->
            @if($step == 3)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 18l4-4m0 0l4-4m-4 4V3m0 12H3m15 0h4M3 3h18M3 3v18m18-18v18"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Select Truck</h2>
                                <p class="text-sm text-gray-500">Choose the best truck for this dispatch</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-xl p-4 mb-8 border border-yellow-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <div>
                                        <div class="text-sm text-yellow-800">Required Fuel</div>
                                        <div class="text-2xl font-bold text-yellow-900">{{ number_format($predicted_fuel_liters, 1) }} Liters</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-yellow-700">Smallest sufficient truck recommended</div>
                                </div>
                            </div>
                        </div>

                        @forelse($truck_recommendations as $index => $rec)
                            <div class="border {{ $index == 0 ? 'border-2 border-indigo-200' : 'border-gray-200' }} rounded-2xl {{ $index == 0 ? 'p-6 mb-6 bg-gradient-to-r from-indigo-50 to-white shadow-lg' : 'p-5 mb-3 hover:shadow-md' }} transition-all duration-200 {{ $selected_truck_id == $rec->truck->id ? 'bg-green-50 border-green-300' : '' }}">
                                @if($index == 0)
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-2">
                                            <span class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-xs px-3 py-1 rounded-full font-semibold">BEST MATCH</span>
                                            <span class="text-xs text-indigo-600">Recommended by AI</span>
                                        </div>
                                        <div class="text-indigo-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-center">
                                    <div class="lg:col-span-1">
                                        <div class="text-xs text-gray-500 mb-1">Truck</div>
                                        <div class="font-bold text-lg text-gray-900">{{ $rec->truck->truck_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $rec->truck->plate_number }}</div>
                                    </div>
                                    
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Available Fuel</div>
                                        <div class="font-bold text-xl text-green-600">{{ number_format($rec->truck->available_ltrs, 1) }} <span class="text-sm">L</span></div>
                                    </div>
                                    
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Distance to Area</div>
                                        <div class="font-medium text-gray-900">{{ number_format($rec->distance_km ?? 0, 1) }} <span class="text-sm text-gray-500">km</span></div>
                                    </div>
                                    
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Travel Fuel Cost</div>
                                        <div class="font-medium text-orange-600">{{ number_format($rec->fuel_to_reach ?? 0, 1) }} <span class="text-sm">L</span></div>
                                    </div>
                                    
                                    <div>
                                        <div class="text-xs text-gray-500 mb-1">Net Available</div>
                                        <div class="font-bold text-lg {{ ($rec->net_available ?? 0) >= $predicted_fuel_liters ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($rec->net_available ?? 0, 1) }} <span class="text-sm">L</span>
                                        </div>
                                        @if(($rec->net_available ?? 0) >= $predicted_fuel_liters)
                                            <div class="text-xs text-green-600">+{{ number_format(($rec->net_available - $predicted_fuel_liters), 1) }} L excess</div>
                                        @else
                                            <div class="text-xs text-red-600">Short by {{ number_format(($predicted_fuel_liters - $rec->net_available), 1) }} L</div>
                                        @endif
                                    </div>
                                    
                                    <div class="lg:col-span-1">
                                        @if($selected_truck_id == $rec->truck->id)
                                            <span class="inline-flex items-center px-3 py-2 rounded-lg bg-green-100 text-green-700 text-sm font-semibold w-full justify-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Selected
                                            </span>
                                        @else
                                            <button wire:click="selectTruck({{ $rec->truck->id }})" 
                                                class="w-full bg-indigo-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-indigo-700 transition-all duration-200">
                                                Select Truck
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Travel Cost Details for Selected Truck -->
                                @if($selected_truck_id == $rec->truck->id && isset($selected_truck_travel_cost))
                                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            <div>
                                                <div class="text-blue-800 font-medium">📍 Travel Distance</div>
                                                <div class="text-blue-900 font-bold">{{ number_format($selected_truck_distance ?? 0, 1) }} km</div>
                                            </div>
                                            <div>
                                                <div class="text-blue-800 font-medium">⛽ Fuel for Travel</div>
                                                <div class="text-blue-900 font-bold">{{ number_format($selected_truck_travel_cost ?? 0, 1) }} L</div>
                                                <div class="text-xs text-blue-600">({{ number_format($selected_truck_distance / max($average_mpg, 0.1), 1) }} L at {{ $average_mpg }} km/L)</div>
                                            </div>
                                            <div>
                                                <div class="text-blue-800 font-medium">📦 Net Available at Destination</div>
                                                <div class="text-green-700 font-bold text-lg">{{ number_format($selected_truck_net_available ?? 0, 1) }} L</div>
                                                <div class="text-xs text-blue-600 mt-1">
                                                    Truck has {{ number_format($rec->truck->available_ltrs, 1) }}L total
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div class="text-red-600 font-semibold mb-2">No trucks available with sufficient fuel</div>
                                <p class="text-sm text-gray-500 mb-4">Required: {{ number_format($predicted_fuel_liters, 1) }} Liters</p>
                                <button wire:click="previousStep" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-200">
                                    ← Go Back
                                </button>
                            </div>
                        @endforelse

                        @if($truck_recommendations->isNotEmpty())
                            <div class="flex gap-4 mt-8 pt-4 border-t border-gray-100">
                                <button wire:click="previousStep" 
                                    class="flex-1 px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-200">
                                    ← Back
                                </button>
                                <button wire:click="createDispatch" 
                                    class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ !$selected_truck_id ? 'disabled' : '' }}>
                                    Create Dispatch →
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- STEP 4: Confirmation -->
            @if($step == 4)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Dispatch Created Successfully!</h2>
                                <p class="text-sm text-gray-500">The dispatch has been recorded and truck assigned</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 text-center">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Dispatch Session #{{ $dispatch_session_id }}</h3>
                        <p class="text-gray-500 mb-8">has been created and assigned successfully</p>

                        <div class="max-w-md mx-auto bg-gray-50 rounded-xl p-6 mb-8 text-left border border-gray-100">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="space-y-1">
                                    <div class="text-xs text-gray-500">Area</div>
                                    <div class="font-semibold text-gray-900">{{ $areas->firstWhere('id', $areaId)->area_name ?? 'N/A' }}</div>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs text-gray-500">Distance</div>
                                    <div class="font-semibold text-gray-900">{{ $distance_km }} km</div>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs text-gray-500">Predicted Fuel</div>
                                    <div class="font-bold text-indigo-600 text-lg">{{ number_format($predicted_fuel_liters, 1) }} L</div>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-xs text-gray-500">Assigned Truck</div>
                                    <div class="font-semibold text-gray-900">{{ \App\Models\Truck::find($selected_truck_id)?->truck_name ?? 'N/A' }}</div>
                                </div>
                                @if(isset($selected_truck_travel_cost) && $selected_truck_travel_cost > 0)
                                    <div class="col-span-2 pt-2 border-t border-gray-200 mt-2">
                                        <div class="text-xs text-gray-500">Travel Details</div>
                                        <div class="grid grid-cols-2 gap-2 mt-1">
                                            <div class="text-sm">Distance to area:</div>
                                            <div class="text-sm font-semibold">{{ number_format($selected_truck_distance ?? 0, 1) }} km</div>
                                            <div class="text-sm">Fuel for travel:</div>
                                            <div class="text-sm font-semibold text-orange-600">{{ number_format($selected_truck_travel_cost ?? 0, 1) }} L</div>
                                            <div class="text-sm">Net at destination:</div>
                                            <div class="text-sm font-semibold text-green-600">{{ number_format($selected_truck_net_available ?? 0, 1) }} L</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <a href="{{ route('admin.dispatch.index') }}" 
                                class="flex-1 text-center px-6 py-3 border-2 border-gray-200 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition-all duration-200">
                                View All Dispatches
                            </a>
                            <button wire:click="resetForm" 
                                class="flex-1 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-indigo-700 hover:to-indigo-600 transition-all duration-200 shadow-lg">
                                Create Another Dispatch
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>