<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TruckDispatch</title>
    
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css'])
    @stack('styles')
    
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
        
        .text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600 {
            color: #111827 !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    {{ $slot }}
    @stack('scripts')
</body>
</html>