<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="user-extension" content="{{ Auth::user()->agent ? Auth::user()->agent->extension : 0 }}">
    <link rel="icon" href="{{ URL::asset('favicon.ico') }}" type="image/x-icon"/>


    @if (isset($title))
    <title>{{ $title }}</title>
    @else
    <title>{{ config('app.name', 'Laravel') }}</title>
    @endif



    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    @vite('resources/css/app.css')

    @livewireStyles
    <wireui:scripts />
    <!-- Scripts -->
    @vite('resources/js/app.js')
</head>

<body class="font-sans antialiased">
    <x-jet-banner />
    <x-notifications z-index="z-50" />
    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')
    
    @livewireScripts
    @stack('scripts')
    @livewire('tickets.show')
    @livewire('orders.show')
</body>

</html>
