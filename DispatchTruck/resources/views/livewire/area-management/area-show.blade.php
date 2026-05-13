<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Area Details</h1>
                <p class="mt-1 text-sm text-gray-600">View area information and location</p>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-3">
                <a href="{{ route('admin.areas.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
                <a href="{{ route('admin.areas.edit', $area->id) }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit Area
                </a>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Area Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Detailed information about the delivery area</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border-b border-gray-200 pb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Area Name</label>
                            <div class="text-gray-900 text-lg font-semibold">{{ $area->area_name }}</div>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Required Liters</label>
                            <div class="flex items-center">
                                <span
                                    class="text-2xl font-bold text-indigo-600">{{ number_format($area->required_liters, 2) }}</span>
                                <span class="ml-2 text-gray-600">Liters</span>
                            </div>
                        </div>

                        <div class="border-b border-gray-200 pb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Coordinates</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">Latitude</div>
                                    <div class="text-gray-900 font-mono">{{ $area->latitude }}</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">Longitude</div>
                                    <div class="text-gray-900 font-mono">{{ $area->longitude }}</div>
                                </div>
                            </div>
                        </div>

                        @if($address)
                            <div class="pb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Full Address</label>
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">{{ $address }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Map Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Location Map</h3>
                    <p class="mt-1 text-sm text-gray-500">Area location on OpenStreetMap</p>
                </div>
                <div class="p-6">
                    <div class="map-container">
                        <iframe width="100%" height="450" frameborder="0" scrolling="no" marginheight="0"
                            marginwidth="0"
                            src="https://www.openstreetmap.org/export/embed.html?bbox={{ $area->longitude - 0.02 }},{{ $area->latitude - 0.02 }},{{ $area->longitude + 0.02 }},{{ $area->latitude + 0.02 }}&layer=mapnik&marker={{ $area->latitude }},{{ $area->longitude }}"
                            style="border-radius: 8px; width: 100%;">
                        </iframe>
                        <div class="mt-3 text-center">
                            <a href="https://www.openstreetmap.org/?mlat={{ $area->latitude }}&mlon={{ $area->longitude }}#map=15/{{ $area->latitude }}/{{ $area->longitude }}"
                                target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                View larger map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>