<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white antialiased">
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <!-- Left Side - Hero Section -->
        <div class="relative hidden h-full flex-col p-10 text-white lg:flex bg-gray-900">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900 to-gray-900"></div>
            
            <!-- Logo -->
            <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium text-white">
                <span class="flex h-10 w-10 items-center justify-center rounded-md bg-white/10">
                    <x-app-logo-icon class="me-2 h-6 w-6 fill-current text-white" />
                </span>
                <span class="ml-2">{{ config('app.name', 'Truck Dispatch') }}</span>
            </a>

            <!-- Inspirational Quote -->
            @php
                [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
            @endphp

            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-xl italic text-white">&ldquo;{{ trim($message) }}&rdquo;</p>
                    <footer class="text-sm text-gray-300">{{ trim($author) }}</footer>
                </blockquote>
            </div>
        </div>

        <!-- Right Side - Form Section -->
        <div class="w-full lg:p-8 bg-white">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <!-- Mobile Logo -->
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium lg:hidden">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md bg-gray-100">
                        <x-app-logo-icon class="h-6 w-6 fill-current text-gray-900" />
                    </span>
                    <span class="text-sm text-gray-600">{{ config('app.name', 'Truck Dispatch') }}</span>
                </a>
                
                <!-- Form Content -->
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
