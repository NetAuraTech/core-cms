<?php
    $options = $options ?? [];
    $site_name = $options['site_name'] ?? config('app.name');
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

        <meta name='twitter:card' content='summary'/>
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

            $daysMapping = [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday',
            ];

            $openingHoursSpecification = [];

            foreach ($daysMapping as $key => $dayName) {
                $schedule = $options["schedule_{$key}"] ?? '';

                if (empty($schedule)) {
                    continue;
                }

                $slots = explode('/', $schedule);

                foreach ($slots as $slot) {
                    $times = explode('-', trim($slot));

                    if (count($times) === 2) {
                        $openingHoursSpecification[] = [
                            "@type" => "OpeningHoursSpecification",
                            "dayOfWeek" => $dayName,
                            "opens" => trim($times[0]),
                            "closes" => trim($times[1]),
                        ];
                    }
                }
            }
        @endphp

        @section('jsonLd')
            @php
                $logo = Request::url() . str_replace('&amp;', '&', $openGraphLogo);

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
                    "mainEntityOfPage" => [
                        "@type" => "WebPage",
                        "@id" => Request::url(),
                    ],
                ];

                if (!empty($alternateNames)) {
                    $jsonLdOrganization["alternateName"] = $alternateNames;
                }

                if (!empty($sameAs)) {
                    $jsonLdOrganization["sameAs"] = $sameAs;
                }

                $areaServedNames = !empty($options['area_served']) ? explode(", ", $options['area_served']) : [];
                $areaServedObjects = [];

                if (!empty($options['address_region'])) {
                    $areaServedObjects[] = [
                        "@type" => "State",
                        "name" => $options['address_region']
                    ];
                }

                foreach ($areaServedNames as $cityName) {
                    if (!empty(trim($cityName))) {
                        $areaServedObjects[] = [
                            "@type" => "City",
                            "name" => trim($cityName)
                        ];
                    }
                }

                $jsonLdLocalBusiness = [
                    "@context" => "https://schema.org",
                    "@type" => "LocalBusiness",
                    "name" => $site_name,
                    "image" => $logo,
                    "url" => Request::url(),
                ];

                if (!empty($options['phone'])) {
                    $jsonLdLocalBusiness["telephone"] = $options['phone'];
                }

                if (!empty($options['price_range'])) {
                    $jsonLdLocalBusiness["priceRange"] = $options['price_range'];
                } else {
                    $jsonLdLocalBusiness["priceRange"] = "€€";
                }

                $address = [
                    "@type" => "PostalAddress",
                ];

                if (!empty($options['address'])) {
                    $address["streetAddress"] = $options['address'];
                }

                if (!empty($options['address_city'])) {
                    $address["addressLocality"] = $options['address_city'];
                }

                if (!empty($options['address_postal-code'])) {
                    $address["postalCode"] = $options['address_postal-code'];
                }

                if (!empty($options['address_region'])) {
                    $address["addressRegion"] = $options['address_region'];
                }

                if (!empty($options['address_country'])) {
                    $address["addressCountry"] = $options['address_country'];
                }

                if (!empty($options['address_city'])) {
                    $jsonLdLocalBusiness["address"] = $address;
                }

                if (!empty($options['address_latitude']) && !empty($options['address_longitude'])) {
                    $jsonLdLocalBusiness["geo"] = [
                        "@type" => "GeoCoordinates",
                        "latitude" => $options['address_latitude'],
                        "longitude" => $options['address_longitude']
                    ];
                }

                if (!empty($areaServedObjects)) {
                    $jsonLdLocalBusiness["areaServed"] = $areaServedObjects;
                }

                if (!empty($openingHoursSpecification)) {
                    $jsonLdLocalBusiness["openingHoursSpecification"] = $openingHoursSpecification;
                }

                if (!empty($sameAs)) {
                    $jsonLdLocalBusiness["sameAs"] = $sameAs;
                }
            @endphp

            <script type="application/ld+json">
                {!! json_encode($jsonLdOrganization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
            </script>

            @if(!empty($options['address_city']))
                <script type="application/ld+json">
                    {!! json_encode($jsonLdLocalBusiness, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
                </script>
            @endif
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