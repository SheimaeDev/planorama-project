<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('images/planorama.png') }}" type="image/png" />

        <title>{{ config('app.name', 'Planorama') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <link rel="stylesheet" href="{{ asset('css/clock.css') }}">
        <script src="{{ asset('js/clock.js') }}" defer></script>
        @yield('styles')

    </head>
    <body class="font-sans antialiased">

        <div class="min-h-screen bg-gray-100 dark:bg-gray-100">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

           <main>
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>

        </div>
       <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div id="clock-container" class="d-flex justify-content-center align-items-center">
                        <div id="clock" class="digital-clock"></div>
                    </div>
                </div>
            </div>
        </div>

    @yield('scripts')

    </body>
</html>
