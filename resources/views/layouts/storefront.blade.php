<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $documentTitle = trim($__env->yieldContent('title'));
    @endphp
    <title>{{ $documentTitle !== '' ? $documentTitle.' — Maison Modern' : 'Maison Modern' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600|instrument-sans:400,500,600" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[70] focus:bg-charcoal focus:px-4 focus:py-2 focus:text-ivory">
        Skip to content
    </a>

    @include('layouts.partials.header')

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @livewireScripts
</body>
</html>
