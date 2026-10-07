<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <meta
        name="description"
        content="MonSiège - Réservez facilement votre place pour vos voyages à Madagascar."
    >

    <title>
        @yield('title', 'MonSiège - Réservez votre voyage')
    </title>

    {{-- Favicon --}}
    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/monsiege-icon.png') }}"
    >

    {{-- Tailwind + JavaScript --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('head')
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">

    {{-- Navigation --}}
    @include('partials.navbar')

    {{-- Contenu principal --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    @stack('scripts')
</body>

</html>