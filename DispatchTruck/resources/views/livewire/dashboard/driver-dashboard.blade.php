<div>
    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Driver Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}! Here's your driving summary.</p>
        </div>

        <!-- Stats Grid -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Current Load -->
                <div class="bg-white dark:bg-zinc-800 overflow-hidden rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Current Load</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">LD-001</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Miles -->
                <div class="bg-white dark:bg-zinc-800 overflow-hidden rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Miles</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">2,345</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deliveries This Month -->
                <div class="bg-white dark:bg-zinc-800 overflow-hidden rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Deliveries This Month</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">24</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Earnings -->
                <div class="bg-white dark:bg-zinc-800 overflow-hidden rounded-lg shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Earnings (MTD)</dt>
                                    <dd class="text-2xl font-semibold text-gray-900 dark:text-white">$3,456</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Trip -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8">
            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 dark:border-zinc-700 sm:px-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Current Trip</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Load Number</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">LD-001</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Pickup Location</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">123 Main St, New York, NY</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivery Location</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">456 Oak Ave, Los Angeles, CA</p>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Pickup Time</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">Today, 10:00 AM</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Delivery Time</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">Tomorrow, 2:00 PM</p>
                            </div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</label>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">In Transit</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex space-x-3">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Update Status
                        </button>
                        <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-zinc-700 dark:text-white dark:border-zinc-600 dark:hover:bg-zinc-600">
                            Contact Dispatch
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Trips -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8">
            <div class="bg-white dark:bg-zinc-800 shadow rounded-lg">
                <div class="px-4 py-5 border-b border-gray-200 dark:border-zinc-700 sm:px-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Trips</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                        <thead class="bg-gray-50 dark:bg-zinc-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Load #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Earnings</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">LD-005</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">NY → Chicago</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Mar 15, 2025</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Completed</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">$450</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">LD-008</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Chicago → Dallas</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Mar 10, 2025</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Completed</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">$380</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>