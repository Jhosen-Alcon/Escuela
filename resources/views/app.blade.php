<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('layout/vendors/mdi/css/materialdesignicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('layout/vendors/css/vendor.bundle.base.css') }}">
        <link rel="stylesheet" href="{{ asset('layout/css/style.css') }}">
        <link rel="shortcut icon" href="{{ asset('layout/images/favicon.png') }}" />
        
        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        <script src="{{ asset('layout/vendors/js/vendor.bundle.base.js') }}"></script>
        <script src="{{ asset('layout/vendors/chart.js/Chart.min.js') }}"></script>
        <script src="{{ asset('layout/js/off-canvas.js') }}"></script>
        <script src="{{ asset('layout/js/hoverable-collapse.js') }}"></script>
        <script src="{{ asset('layout/js/template.js') }}"></script>        
    </body>
</html>
