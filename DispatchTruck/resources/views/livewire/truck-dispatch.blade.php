<div class="max-w-7xl mx-auto px-4 py-8">
    @if($error)
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ $error }}</span>
        <button wire:click="clearResults" class="absolute top-0 bottom-0 right-0 px-4 py-3">
            <span class="text-red-700">×</span>
        </button>
    </div>
    @endif

    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Truck Dispatch System
        </h1>
        <p class="text-xl text-gray-600">
            Optimize truck assignments using greedy algorithm
        </p>
    </div>

    <div class="flex justify-center space-x-4 mb-8">
        <button wire:click="runSimulation" wire:loading.attr="disabled" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg text-lg transition duration-200">
            <span wire:loading.remove wire:target="runSimulation">🚛 Run Simulation</span>
            <span wire:loading wire:target="runSimulation">Running...</span>
        </button>

        @if($result)
        <button wire:click="clearResults" 
                class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg text-lg transition duration-200">
            Clear Results
        </button>
        @endif
    </div>

    <div wire:loading wire:target="runSimulation" class="flex justify-center mb-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    @if($result)
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Simulation Results</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Demand:</span>
                        <span class="font-semibold">{{ number_format($result['summary']['total_demand_liters']) }}L</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Supply:</span>
                        <span class="font-semibold">{{ number_format($result['summary']['total_supply_liters']) }}L</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fulfilled:</span>
                        <span class="font-semibold text-green-600">{{ number_format($result['summary']['fulfilled_demand_liters']) }}L</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Algorithm:</span>
                        <span class="font-semibold">{{ $result['summary']['algorithm_used'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Primary Matches:</span>
                        <span class="font-semibold">{{ $result['summary']['primary_matches'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cross Matches:</span>
                        <span class="font-semibold">{{ $result['summary']['cross_matches'] }}</span>
                    </div>

                    @php
                    $efficiency = ($result['summary']['fulfilled_demand_liters'] / $result['summary']['total_demand_liters']) * 100;
                    @endphp
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Efficiency</span>
                            <span class="font-semibold">{{ number_format($efficiency, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 rounded-full h-2" style="width: {{ $efficiency }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Dispatch Steps</h3>
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($result['steps'] as $step)
                    <div class="text-sm border-b border-gray-200 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-medium text-blue-600">Step {{ $step['step'] }}:</span>
                                <span class="mx-1">{{ $step['truck_name'] }}</span>
                                <span class="text-gray-500">→</span>
                                <span class="mx-1">{{ $step['area_name'] }}</span>
                            </div>
                            <div>
                                <span class="text-green-600 font-semibold">{{ number_format($step['allocated']) }}L</span>
                                @if($step['is_primary_area'])
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Primary</span>
                                @else
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Cross</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
