<?php
    $options = $options ?? [];
    $site_name = $options['site_name'] ?? config('app.name');

    $favicon = $options['favicon'] ?? null;
    $openGraphLogo = $options['logo'] ?? null;
?>

<!DOCTYPE html>
<html lang="{{ Lang::locale() }}">
    <head>
        <meta charset="UTF-8">
        <title>@yield('title') | {{ $site_name }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <script src="{{ route('translations') }}"></script>
        @vite(['resources/ts/app.ts'])
        @include('theme::assets.css') {{-- Includes theme-specific CSS --}}
        @include('theme::assets.js') {{-- Includes theme-specific JS --}}
        @yield('stylesheets') {{-- For page-specific CSS --}}
        @yield('meta') {{-- For additional meta tags --}}
        @yield('description') {{-- The page description is managed in the specific view --}}
        <meta name="view-transition" content="same-origin">
        <meta name="turbolinks-cache-control" content="no-cache"/>

        {{-- Favicon --}}
        @if($favicon)
            <link rel="apple-touch-icon" sizes="128x128" href="{{ $favicon }}">
            <link rel="icon" type="image/webp" href="{{ $favicon }}"/>
        @endif

        {{-- Open Graph / Twitter Cards --}}
        <meta property='og:locale' content='{{ Lang::locale() }}'/>
        <meta property='og:type' content='website'/> {{-- Default ‘website’, can be overridden in views --}}
        <meta property="og:title" content="@yield('title')"/>
        <meta property="og:site_name" content="{{ $site_name }}"/>
        <meta property="og:language" content="fr"/>
        <meta property='og:url' content="{{ Request::url() }}"/>
        @if($openGraphLogo)
            <meta property='og:image' content="{{ $openGraphLogo }}"/>
        @endif
        <meta name='twitter:card' content='summary'/>
        <meta name='twitter:site' content="{{ Request::url() }}"/>
        <meta name='twitter:title' content="@yield('title') | {{ $site_name }}"/>
        @if($openGraphLogo)
            <meta name='twitter:image' content="{{ $openGraphLogo }}"/>
        @endif

        <style>
            @view-transition {
                navigation: auto;
            }
        </style>
        <link rel='canonical' href="{{ Request::url() }}"/>
        @php
            $alternateNames = array_values(array_filter(generateNameVariants($site_name), fn($v) => strtolower($v) !== strtolower($site_name)));

            $sameAs = [];
            $links = ['facebook', 'instagram', 'twitter', 'linkedin', 'youtube'];

            foreach ($links as $link) {
                if (!empty($options[$link] ?? '')) {
                    $sameAs[] = $options[$link];
                }
            }
        @endphp

        @section('jsonLd')
            @php
                $logo = Request::url() . str_replace('&amp;', '&', $openGraphLogo);

                $jsonLd = [
                    "@context" => "https://schema.org",
                    "@type" => "Organization",
                    "name" => $site_name,
                    "url" => Request::url(),
                    "logo" => $logo,
                    "contactPoint" => [
                        "@type" => "ContactPoint",
                        "email" => $options['contact-email'] ?? 'contact@example.com',
                    ],
                    "mainEntityOfPage" => [
                        "@type" => "WebPage",
                        "@id" => Request::url(),
                    ],
                ];

                if (!empty($alternateNames)) {
                    $jsonLd["alternateName"] = $alternateNames;
                }

                if (!empty($sameAs)) {
                    $jsonLd["sameAs"] = $sameAs;
                }
            @endphp

            <script type="application/ld+json">
                {!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
            </script>
        @show
    </head>
    <body id="page-wrapper">
        <main class="body">
            @unless(isset($hideHeaderFooter) && $hideHeaderFooter)
                {{-- TODO: Display header --}}
            @endunless
            @yield('body')
            @unless(isset($hideHeaderFooter) && $hideHeaderFooter)
                {{-- TODO: Display header --}}
            @endunless
        </main>
    </body>
</html>