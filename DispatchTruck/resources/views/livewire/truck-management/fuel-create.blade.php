<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Fuel Type</h1>
                    <p class="mt-1 text-sm text-gray-600">New fuel type to your inventory</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.fuel.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
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
                    </div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Fuel Information</h3>
                    <p class="mt-1 text-sm text-gray-500">Fill in the details for the new fuel type</p>
                </div>

                <div class="p-6">
                    <form wire:submit.prevent="createFuel">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mb-6">
                            <div>
                                <label for="fuel_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fuel Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white uppercase @error('fuel_code') border-red-300 @enderror"
                                    id="fuel_code" wire:model.live="fuel_code" placeholder="e.g., DIESEL, PETROL95, EV">
                                @error('fuel_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Unique identifier for this fuel type
                                    (automatically uppercase)</p>
                            </div>

                            <div>
                                <label for="fuel_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Fuel Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900 bg-white @error('fuel_name') border-red-300 @enderror"
                                    id="fuel_name" wire:model.live="fuel_name"
                                    placeholder="e.g., Diesel, Petrol 95, Electric">
                                @error('fuel_name')
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
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Inactive fuel types won't be available for
                                    selection</p>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.fuel.index') }}"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                Create Fuel Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>