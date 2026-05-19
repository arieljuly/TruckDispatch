<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TruckDispatch</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f3f4f6;
            color: #111827;
        }
    </style>

    @livewireStyles
</head>

<body class="bg-gray-50">
    @auth
        <main class="container mx-auto px-4 py-8">
            {{ $slot }}
        </main>
    @else
        {{ $slot }}
    @endauth
@livewireScripts
    @stack('scripts')
</body>

</html>