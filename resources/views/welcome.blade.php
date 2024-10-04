<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
        @vite(['resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div id="app"></div>
    </body>

    <script>
        // Pass Laravel auth user to JavaScript (Vue)
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            user: @json(auth()->user()) // Pass authenticated user or null if not authenticated
        };
    </script>
</html>
