<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Area</h1>
                <p class="mt-1 text-sm text-gray-600">Add a new delivery area with location details</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.areas.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('message') }}</p>
                    </div>
                    <div class="ml-auto pl-3">
                        <div class="-mx-1.5 -my-1.5">
                            <button type="button"
                                class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none"
                                data-bs-dismiss="alert">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Area Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Fill in the details for the new delivery area</p>
                </div>

                <div class="p-6">
                    <form wire:submit.prevent="save">
                        <!-- Search Location -->
                        <div class="mb-6">
                            <label for="search_query" class="block text-sm font-medium text-gray-700 mb-2">Search
                                Location</label>
                            <div class="relative">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text"
                                        class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('search_query') border-red-300 @enderror"
                                        id="search_query" wire:model.live.debounce.500ms="search_query"
                                        wire:keyup="searchLocation" placeholder="Search for city, street, or address...">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Type at least 3 characters to search</p>

                                @if($show_search && count($search_results) > 0)
                                    <div
                                        class="absolute w-full mt-1 bg-white rounded-md shadow-lg z-50 max-h-60 overflow-y-auto border border-gray-200">
                                        @foreach($search_results as $result)
                                            <button type="button"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors duration-150 border-b border-gray-100 last:border-0"
                                                wire:click="selectLocation('{{ $result['lat'] }}', '{{ $result['lon'] }}', '{{ addslashes($result['display_name']) }}')">
                                                <div class="text-sm font-medium text-gray-900">{{ $result['display_name'] }}</div>
                                                <div class="text-xs text-gray-500 mt-1">Lat: {{ $result['lat'] }}, Lon:
                                                    {{ $result['lon'] }}</div>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($selected_address)
                            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">Selected Location: {{ $selected_address }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                            <div>
                                <label for="area_name" class="block text-sm font-medium text-gray-700 mb-2">Area Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('area_name') border-red-300 @enderror"
                                    id="area_name" wire:model="area_name">
                                @error('area_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="required_liters" class="block text-sm font-medium text-gray-700 mb-2">Required
                                    Liters <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('required_liters') border-red-300 @enderror"
                                    id="required_liters" wire:model="required_liters">
                                @error('required_liters')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude <span
                                        class="text-red-500">*</span></label>
                                <input type="number" step="any"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('latitude') border-red-300 @enderror"
                                    id="latitude" wire:model.live="latitude" wire:blur="reverseGeocode">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude <span
                                        class="text-red-500">*</span></label>
                                <input type="number" step="any"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('longitude') border-red-300 @enderror"
                                    id="longitude" wire:model.live="longitude" wire:blur="reverseGeocode">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.areas.index') }}"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                Create Area
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Map Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Location Map</h3>
                    <p class="mt-1 text-sm text-gray-500">View location on map</p>
                </div>
                <div class="p-6">
                    <iframe
                        id="mapFrame"
                        width="100%"
                        height="450"
                        frameborder="0"
                        scrolling="no"
                        marginheight="0"
                        marginwidth="0"
                        src="https://www.openstreetmap.org/export/embed.html?bbox={{ $longitude - 0.05 }},{{ $latitude - 0.05 }},{{ $longitude + 0.05 }},{{ $latitude + 0.05 }}&layer=mapnik&marker={{ $latitude }},{{ $longitude }}"
                        style="border-radius: 8px;">
                    </iframe>
                    <div class="mt-3 text-center">
                        <a href="https://www.openstreetmap.org/?mlat={{ $latitude }}&mlon={{ $longitude }}#map=13/{{ $latitude }}/{{ $longitude }}"
                           target="_blank"
                           class="text-sm text-indigo-600 hover:text-indigo-900">
                            View larger map
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('updateMap', (data) => {
            const lat = data.lat;
            const lon = data.lon;
            const mapFrame = document.getElementById('mapFrame');
            if (mapFrame) {
                const newSrc = `https://www.openstreetmap.org/export/embed.html?bbox=${lon - 0.05},${lat - 0.05},${lon + 0.05},${lat + 0.05}&layer=mapnik&marker=${lat},${lon}`;
                mapFrame.src = newSrc;
            }
        });
    });
</script>
@endpush