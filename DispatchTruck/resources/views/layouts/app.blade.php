<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Truck Dispatch System</title>
    @livewireStyles
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        @yield('content')
    </div>
    @livewireScripts
</body>
</html>
