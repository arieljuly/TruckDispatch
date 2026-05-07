<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white "> 
    <!-- Header -->
    <header class="border-b border-gray-200 bg-gray-50 sticky top-0 z-50">
        <div class="mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Mobile Menu Button -->
            <button id="mobileMenuButton" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Logo -->
            <x-app-logo href="{{ route('dashboard') }}" wire:navigate />

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-1 -mb-px">
                <a href="{{ route('dashboard') }}" 
                    class="px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </a>
            </nav>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Right Side Icons -->
            <div class="flex items-center space-x-1 mr-1.5">
                <!-- Search -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 ">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    
                    <!-- Search Dropdown -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white  ring-1 ring-black ring-opacity-5">
                        <div class="p-4">
                            <input type="text" placeholder="{{ __('Search...') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 ">
                        </div>
                    </div>
                </div>

                <!-- Repository Link -->
                <a href="https://github.com/laravel/livewire-starter-kit" target="_blank" 
                    class="hidden lg:flex p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </a>

                <!-- Documentation Link -->
                <a href="https://laravel.com/docs/starter-kits#livewire" target="_blank"
                    class="hidden lg:flex p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </a>

                <!-- User Menu -->
                <x-desktop-user-menu />
            </div>
        </div>
    </header>

    <!-- Mobile Menu Sidebar -->
    <div id="mobileSidebar" class="fixed inset-0 z-50 hidden lg:hidden">
        <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-y-0 left-0 max-w-full flex">
            <div class="w-64 bg-white shadow-xl border-r border-gray-200">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                    <button id="closeMobileMenu" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <nav class="flex-1 p-4 space-y-6">
                    <!-- Platform Section -->
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Platform') }}</h3>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('dashboard') }}" 
                                class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50 ' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100' }}"
                                wire:navigate>
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                                {{ __('Dashboard') }}
                            </a>
                        </div>
                    </div>
                </nav>

                <div class="flex-1"></div>

                <!-- Bottom Links -->
                <nav class="p-4 border-t border-gray-200 space-y-1">
                    <a href="https://github.com/laravel/livewire-starter-kit" target="_blank"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                        {{ __('Repository') }}
                    </a>
                    <a href="https://laravel.com/docs/starter-kits#livewire" target="_blank"
                        class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        {{ __('Documentation') }}
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Toast Messages -->
    <div id="toastContainer" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <script>
        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const closeMobileMenu = document.getElementById('closeMobileMenu');

        function openMobileMenu() {
            mobileSidebar.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenuFunc() {
            mobileSidebar.classList.add('hidden');
            document.body.style.overflow = '';
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', openMobileMenu);
        }

        if (closeMobileMenu) {
            closeMobileMenu.addEventListener('click', closeMobileMenuFunc);
        }

        // Close sidebar when clicking outside
        mobileSidebar?.addEventListener('click', function(e) {
            if (e.target === mobileSidebar) {
                closeMobileMenuFunc();
            }
        });

        // Toast notification system
        window.showToast = function(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            const icon = type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ';
            
            toast.className = `flex items-center p-4 rounded-lg shadow-lg text-white ${bgColor} transform transition-all duration-300 translate-x-full`;
            toast.innerHTML = `
                <div class="flex-shrink-0 mr-3">
                    <span class="text-lg font-bold">${icon}</span>
                </div>
                <div class="flex-1 text-sm font-medium">${message}</div>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.remove()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }, 10);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast && toast.parentElement) {
                    toast.classList.remove('translate-x-0');
                    toast.classList.add('translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        };
    </script>

    @stack('scripts')
</body>
</html>