<div>
    <aside
    class="sidebar fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0 bg-white border-r border-gray-200">
    <div class="h-full flex flex-col">
        <!-- Logo -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <a href="{{ route('client.dashboard') }}" class="flex items-center space-x-2" wire:navigate>
                <image src="{{ asset('assets/images/logo.png') }}" alt="Truck Icon" class="w-10 h-7">
                <span class="text-xl font-bold text-gray-900">TruckDispatch</span>
            </a>
            <button id="closeSidebar"
                class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto py-4">
            <div class="px-3">
                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Client Portal') }}
                </h3>
                <div class="mt-2 space-y-1">
                    <a href="{{ route('client.dashboard') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('client.dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('client.shipments') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('client.shipments') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        {{ __('My Shipments') }}
                    </a>
                    <a href="{{ route('client.create-shipment') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('client.create-shipment') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('Create Shipment') }}
                    </a>
                    <a href="{{ route('client.invoices') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('client.invoices') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        {{ __('Invoices') }}
                    </a>
                    <a href="{{ route('client.support') }}"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('client.support') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}"
                        wire:navigate>
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 5.636L9.172 14.828m0 0l-2.829-2.829m2.829 2.829l2.828 2.829M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        {{ __('Support') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center">
                    <span class="text-sm font-medium text-indigo-600">
                        {{ auth()->user()->initials() ?? substr(auth()->user()->name, 0, 2) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
    const closeSidebar = document.getElementById('closeSidebar');
    const sidebar = document.querySelector('.sidebar');

    if (closeSidebar && sidebar) {
        closeSidebar.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });
    }
</script>
</div>