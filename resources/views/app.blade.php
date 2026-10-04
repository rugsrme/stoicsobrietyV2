<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <meta name="theme-color" content="#050a13">

        {{-- Link previews (Facebook, messages, etc.). Posts pass their own via linkPreview. --}}
        @php($preview = $linkPreview ?? [])
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:type" content="{{ $preview['type'] ?? 'website' }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $preview['title'] ?? config('app.name') }}">
        @isset($preview['description'])
            <meta property="og:description" content="{{ $preview['description'] }}">
        @endisset
        @if (! empty($preview['image']))
            <meta property="og:image" content="{{ $preview['image'] }}">
            <meta name="twitter:image" content="{{ $preview['image'] }}">
        @else
            <meta property="og:image" content="{{ asset('brand/og-image.jpg') }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
            <meta property="og:image:alt" content="Sober. Now We Live. Steps, Stoicism, Scripture: a more honest way forward.">
            <meta name="twitter:image" content="{{ asset('brand/og-image.jpg') }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
