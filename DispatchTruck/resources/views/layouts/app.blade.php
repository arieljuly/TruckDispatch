<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruckDispatch</title>

    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])
    @stack('styles')
    @livewireStyles

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            color: #111827;
        }

        /* Ensure all text has dark color */
        .text-white {
            color: #ffffff !important;
        }

        .text-gray-900,
        .text-gray-800,
        .text-gray-700,
        .text-gray-600 {
            color: #111827 !important;
        }
    </style>
</head>

<body class="bg-gray-50">
    @auth
        <!-- Include sidebar based on user role -->
        @if(auth()->user()->role === 'admin')
            @include('admin.sidebar')
        @elseif(auth()->user()->role === 'client')
            @include('client.sidebar')
        @endif

        <!-- Main content with margin for sidebar -->
        <main class="lg:pl-64">
            <div class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </div>
        </main>
    @else
        <!-- No sidebar for guests -->
        {{ $slot }}
    @endauth

    @stack('scripts')
    @livewireScripts
</body>

</html>