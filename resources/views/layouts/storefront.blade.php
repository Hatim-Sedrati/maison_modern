<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $fullTitle = trim($__env->yieldContent('title_full'));
        if ($fullTitle === '') {
            $documentTitle = trim($__env->yieldContent('title'));
            $fullTitle = $documentTitle !== '' ? $documentTitle.' — Maison Modern' : 'Maison Modern';
        }
        $metaDescription = trim($__env->yieldContent('meta_description')) ?: 'Maison Modern — contemporary Moroccan fashion for everyday elegance.';
        $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
        $ogImage = trim($__env->yieldContent('og_image')) ?: asset('images/logo.png');
        $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
        $robots = trim($__env->yieldContent('robots'));

        if ($ogImage !== '' && ! str_starts_with($ogImage, 'http://') && ! str_starts_with($ogImage, 'https://')) {
            $ogImage = url($ogImage);
        }
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <link rel="canonical" href="{{ $canonical }}">
    @if ($robots !== '')
        <meta name="robots" content="{{ $robots }}">
    @endif
    <meta property="og:site_name" content="Maison Modern">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <x-json-ld :data="\App\Support\StructuredData::graph()" />
    @stack('structured_data')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|instrument-sans:400,500,600" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="flex min-h-screen flex-col overflow-x-hidden bg-cream text-charcoal">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[70] focus:bg-charcoal focus:px-4 focus:py-2 focus:text-ivory">
        Skip to content
    </a>

    @include('layouts.partials.header')

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @livewire('layout.cart-toast')

    @livewireScripts
</body>
</html>
