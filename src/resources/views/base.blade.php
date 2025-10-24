<?php
    $options = $options ?? [];
    $site_name = $options['site_name'] ?? config('app.name');
    $openGraphLogo = $openGraphLogo ?? '';
    $logo = url('/') . str_replace('&amp;', '&', image_url($openGraphLogo->id ?? ''));
?>

<!DOCTYPE html>
<html lang="{{ Lang::locale() }}">
    <head>
        <meta charset="UTF-8">
        <title>@yield('title') | {{ $site_name }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <script src="{{ route('translations') }}"></script>
        @vite(['resources/ts/app.ts'])
        @include('theme::assets.css', ['header' => $options['header'], 'footer' => $options['footer']]) {{-- Includes theme-specific CSS --}}
        @include('theme::assets.js') {{-- Includes theme-specific JS --}}
        @yield('stylesheets') {{-- For page-specific CSS --}}
        @yield('meta') {{-- For additional meta tags --}}
        @yield('description') {{-- The page description is managed in the specific view --}}
        <meta name="csrf-token" content="">
        <meta name="view-transition" content="same-origin">
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

        <meta name='twitter:card' content='summary_large_image'/>
        <meta name='twitter:site' content="{{ Request::url() }}"/>
        <meta name='twitter:title' content="@yield('title') | {{ $site_name }}"/>

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

            $jsonLdOrganization = [
                "@context" => "https://schema.org",
                "@type" => "Organization",
                "name" => $site_name,
                "url" => Request::url(),
                "logo" => $logo,
                "contactPoint" => [
                    "@type" => "ContactPoint",
                    "email" => $options['contact-email'] ?? 'contact@example.com',
                ],
            ];

            if (!empty($alternateNames)) {
                $jsonLdOrganization["alternateName"] = $alternateNames;
            }

            if (!empty($sameAs)) {
                $jsonLdOrganization["sameAs"] = $sameAs;
            }
        @endphp
        <script type="application/ld+json">
            {!! json_encode($jsonLdOrganization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
        </script>
        @section('jsonLd')
        @show
    </head>
    <body id="page-wrapper">
        @unless(isset($hideHeaderFooter) && $hideHeaderFooter)
            @yield('header')
        @endunless
        <main class="body">
            @yield('body')
        </main>
        @unless(isset($hideHeaderFooter) && $hideHeaderFooter)
            <footer class="site-footer">
                @yield('footer')
            </footer>
        @endunless
        <script>
            @php use Illuminate\Support\Facades\Auth; @endphp
            window.auth = {
                ...(window.auth || {}),
                USER: {{ Auth::user() ? Auth::user()->id : 'null' }},
                NOTIFICATION: new Date({{ (Auth::user() and Auth::user()->notifications_read_at) ? Auth::user()->getNotificationsReadAtTimestamp() : 0 }})
            };
        </script>
        @php
            $assetManager = app(\Netauratech\CoreCms\Services\AssetManager::class);
        @endphp
        @foreach($assetManager->getViewAssets() as $asset)
            @includeIf($asset)
        @endforeach
    </body>
</html>