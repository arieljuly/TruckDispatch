<div>
    <aside
        class="sidebar fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0 bg-gradient-to-b from-[#1E3A8A] to-[#1E4DB7] shadow-xl">
        <div class="h-full flex flex-col">
            <!-- Logo -->
            <div class="flex items-center justify-between p-4 border-b border-white/10">
                <a href="{{ route('driver.dashboard') }}" class="flex items-center space-x-2" wire:navigate>
                    <div class="w-10 h-10 rounded-lg bg-white p-1.5 flex items-center justify-center">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Truck Icon"
                            class="w-8 h-8 object-contain">
                    </div>
                    <span class="text-xl font-bold text-white">TruckDispatch</span>
                </a>
                <button id="closeSidebar"
                    class="lg:hidden p-2 rounded-md text-white/70 hover:text-white hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <div class="flex-1 overflow-y-auto py-4 scrollbar-thin scrollbar-track-white/10 scrollbar-thumb-white/20">
                <div class="px-3">
                    <h3 class="px-3 text-xs font-semibold text-white/60 uppercase tracking-wider">
                        {{ __('Driver Portal') }}
                    </h3>
                    <div class="mt-2 space-y-1">
                        <!-- Dashboard Button -->
                        <a href="{{ route('driver.dashboard') }}"
                            class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('driver.dashboard') ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:text-white hover:bg-white/10' }}"
                            wire:navigate>
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                            {{ __('Dashboard') }}
                        </a>

                        <!-- Assigned Truck Button -->
                        <a href="{{ route('driver.trucks.assigned') }}"
                            class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('driver.trucks*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:text-white hover:bg-white/10' }}"
                            wire:navigate>
                            <svg class="w-5 h-5 mr-3" fill="currentColor" stroke="none" viewBox="0 0 256 256">
                                <path
                                    d="M224,96.8V96a56.06,56.06,0,0,0-56-56h-8a16,16,0,0,0-16,16V176H128V72a8,8,0,0,0-8-8H16A16,16,0,0,0,0,80V184a32,32,0,0,0,56,21.13A32,32,0,0,0,111,192h82a32,32,0,0,0,63-8V136A40.07,40.07,0,0,0,224,96.8ZM160,56h8a40,40,0,0,1,40,40v8a8,8,0,0,0,8,8,24,24,0,0,1,24,24v20.31A31.71,31.71,0,0,0,224,152a32.06,32.06,0,0,0-31,24H160ZM112,80v96h-1a32,32,0,0,0-55-13.13,31.9,31.9,0,0,0-40-6.56V80ZM32,200a16,16,0,1,1,16-16A16,16,0,0,1,32,200Zm48,0a16,16,0,1,1,16-16A16,16,0,0,1,80,200Zm144,0a16,16,0,1,1,16-16A16,16,0,0,1,224,200Z">
                                </path>
                            </svg>
                            {{ __('Assigned Truck') }}
                        </a>

                        <!-- Maintenance Button -->
                        <a href="{{ route('driver.maintenance.index') }}"
                            class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('driver.maintenance*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:text-white hover:bg-white/10' }}"
                            wire:navigate>
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                            </svg>
                            {{ __('Maintenance') }}
                        </a>

                        <!-- Notifications Button -->
                        <a href="{{ route('driver.notifications.index') }}"
                            class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('driver.notifications*') ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:text-white hover:bg-white/10' }}"
                            wire:navigate>
                            <svg class="w-5 h-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                            {{ __('Notifications') }}
                            @php
                                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile Button -->
            <div class="p-4 border-t border-white/10" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false"
                    class="flex items-center space-x-3 w-full hover:bg-white/10 rounded-lg p-2 transition-all duration-200">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <span class="text-sm font-medium text-white">
                            {{ auth()->user()->initials() ?? substr(auth()->user()->name, 0, 2) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0 text-left">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-white/60 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="w-5 h-5 text-white/60 transition-transform duration-200" :class="{ 'rotate-180': open }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-cloak @click.away="open = false"
                    class="absolute bottom-20 left-4 right-4 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden z-50">
                    <div class="py-1">
                        <a href="{{ route('driver.settings') }}"
                            class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                            </svg>
                            {{ __('Settings') }}
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <button onclick="confirmLogout()"
                            class="flex items-center w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            {{ __('Logout') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Hidden logout form -->
    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
        @csrf
    </form>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .sidebar ::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .sidebar ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }

        .sidebar ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        if (typeof window.confirmLogout === 'undefined') {
            window.confirmLogout = function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to log out of your account!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, logout!',
                    cancelButtonText: 'Cancel',
                    background: '#ffffff',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Logging out...',
                            text: 'Please wait...',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                                document.getElementById('logout-form').submit();
                            }
                        });
                    }
                });
            }
        }

        if (typeof window.sidebarInitialized === 'undefined') {
            window.sidebarInitialized = true;

            document.addEventListener('DOMContentLoaded', function () {
                const closeSidebar = document.getElementById('closeSidebar');
                const sidebar = document.querySelector('.sidebar');

                if (closeSidebar && sidebar) {
                    const newCloseSidebar = closeSidebar.cloneNode(true);
                    closeSidebar.parentNode.replaceChild(newCloseSidebar, closeSidebar);

                    newCloseSidebar.addEventListener('click', () => {
                        sidebar.classList.add('-translate-x-full');
                    });
                }
            });
        }

        if (typeof window.livewireNavigated === 'undefined') {
            window.livewireNavigated = true;

            document.addEventListener('livewire:navigated', () => {
                const closeSidebar = document.getElementById('closeSidebar');
                const sidebar = document.querySelector('.sidebar');

                if (closeSidebar && sidebar) {
                    const newCloseSidebar = closeSidebar.cloneNode(true);
                    if (closeSidebar.parentNode) {
                        closeSidebar.parentNode.replaceChild(newCloseSidebar, closeSidebar);
                    }

                    newCloseSidebar.addEventListener('click', () => {
                        sidebar.classList.add('-translate-x-full');
                    });
                }
            });
        }
    </script>
</div>