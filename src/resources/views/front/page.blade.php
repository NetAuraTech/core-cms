@php
    $options = $options ?? [];
    $site_name = $options['site_name'] ?? config('app.name');
    $openGraphLogo = $openGraphLogo ?? (object)['id' => '', 'width' => '', 'height' => '', 'alt' => ''];
    $logo = url('/') . str_replace('&amp;', '&', image_url($openGraphLogo->id ?? ''));

    $description = ($isHomepage ?? false) ? ($options['description'] ?? '') : ($content->description ?? '');
@endphp

@extends('core-cms::base')

@section('title', $content->title)

@section('description')
    <meta property='og:description' content="{{ $description }}"/>
    <meta name='twitter:description' content="{{ $description }}"/>
    <meta name="description" content="{{ $description }}"/>
@endsection

@section('meta')
    @parent

    @php
        $regionCodesByCountry = [
            'FR' => [
                'Auvergne-Rhône-Alpes' => 'FR-ARA',
                'Bourgogne-Franche-Comté' => 'FR-BFC',
                'Bretagne' => 'FR-BRE',
                'Centre-Val de Loire' => 'FR-CVL',
                'Corse' => 'FR-COR',
                'Grand Est' => 'FR-GES',
                'Hauts-de-France' => 'FR-HDF',
                'Île-de-France' => 'FR-IDF',
                'Normandie' => 'FR-NOR',
                'Nouvelle-Aquitaine' => 'FR-NAQ',
                'Occitanie' => 'FR-OCC',
                'Pays de la Loire' => 'FR-PDL',
                "Provence-Alpes-Côte d'Azur" => 'FR-PAC',
            ],
        ];

        $userCountry = strtoupper($options['address_country'] ?? 'FR');
        $userRegion = $options['address_region'] ?? '';

        $normalizedInput = strtolower(str_replace([' ', '-', "'", 'ü', 'ö', 'ä', 'ß'], ['', '', '', 'u', 'o', 'a', 'ss'], $userRegion));

        $regionCode = null;
        if (isset($regionCodesByCountry[$userCountry])) {
            foreach ($regionCodesByCountry[$userCountry] as $name => $code) {
                $normalizedName = strtolower(str_replace([' ', '-', "'", 'ü', 'ö', 'ä', 'ß'], ['', '', '', 'u', 'o', 'a', 'ss'], $name));
                if ($normalizedInput === $normalizedName) {
                    $regionCode = $code;
                    break;
                }
            }
        }
    @endphp

    @if($isHomepage && !empty($options['address_city']))
        <meta property="og:type" content="business.business"/>
        <meta property="business:contact_data:street_address" content="{{ $options['address'] ?? '' }}"/>
        <meta property="business:contact_data:locality" content="{{ $options['address_city'] }}"/>
        <meta property="business:contact_data:region" content="{{ $options['address_region'] }}"/>
        <meta property="business:contact_data:postal_code" content="{{ $options['address_postal-code'] }}"/>
        <meta property="business:contact_data:country_name" content="{{ $options['address_country'] }}"/>
        @if(!empty($options['phone']))
            <meta property="business:contact_data:phone_number" content="{{ $options['phone'] }}"/>
        @endif
        @if(!empty($options['contact-email']))
            <meta property="business:contact_data:email" content="{{ $options['contact-email'] }}"/>
        @endif
        @if($regionCode)
            <meta name="geo.region" content="{{ $regionCode }}">
        @endif
        <meta name="geo.placename" content="{{ $options['address_city'] }}"/>
        <meta name="geo.position" content="{{ $options['address_latitude'] }};{{ $options['address_longitude'] }}"/>
        <meta name="ICBM" content="{{ $options['address_latitude'] }}, {{ $options['address_longitude'] }}"/>
    @endif

    @foreach($metas as $meta)
        @include($meta['template'], ['content' => $content, 'openGraphLogo' => $openGraphLogo])
    @endforeach
@endsection

@section('jsonLd')
    @parent
    @if($isHomepage)
        @php
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
                "@type" => "ProfessionalService",
                "name" => $site_name,
                "legalName" => $site_name,
                "image" => $logo,
                "url" => Request::url(),
                "email" => $options['contact-email'],
                "description" => $description,
                "contactPoint" => [
                    "@type" => "ContactPoint",
                    "contactType" => "customer service",
                    "telephone" => $options['phone'],
                    "email" => $options['contact-email'],
                    "availableLanguage" => ["French"],
                    "areaServed" => "FR"
                ]
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
                $jsonLdLocalBusiness["location"] = [
                    "type" => "Place",
                    "geo" => [
                        "@type" => "GeoCoordinates",
                        "latitude" => $options['address_latitude'],
                        "longitude" => $options['address_longitude'],
                        "address" => [
                            "@type" => "PostalAddress",
                            "addressCountry" => $options['address_country']
                        ]
                    ],
                    "hasMap" => "https://www.google.com/maps/search/?api=1&query={$options['address_latitude']},{$options['address_longitude']}"
                ];
            }

            if (!empty($areaServedObjects)) {
                $jsonLdLocalBusiness["areaServed"] = $areaServedObjects;
            }

            $daysMapping = [
                'monday' => 'Monday',
                'tuesday' => 'Tuesday',
                'wednesday' => 'Wednesday',
                'thursday' => 'Thursday',
                'friday' => 'Friday',
                'saturday' => 'Saturday',
                'sunday' => 'Sunday'
            ];

            $openingHoursSpecification = [];

            foreach ($daysMapping as $dayKey => $dayName) {
                $schedule = $options["schedule_{$dayKey}"] ?? '';

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

            if (!empty($openingHoursSpecification)) {
                $jsonLdLocalBusiness["openingHoursSpecification"] = $openingHoursSpecification;
            }

            if (!empty($sameAs)) {
                $jsonLdLocalBusiness["sameAs"] = $sameAs;
            }
        @endphp

        @if(!empty($options['address_city']))
            <script type="application/ld+json">
                {!! json_encode($jsonLdLocalBusiness, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
            </script>
        @endif
    @endif
@endsection

@section('header')
    @if($options['header'] !== "")
        @foreach($options['header']->getContent() as $block)
            @includeIf('core-cms::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('footer')
    @if($options['footer'] !== "")
        @foreach($options['footer']->getContent() as $block)
            @includeIf('core-cms::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('stylesheets')
    @php
        $contents = [$content, $options['header'], $options['footer']];
    @endphp
    @foreach($contents as $item)
        @php
            $cacheBuster = substr(md5(json_encode($item->updated_at)), 0, 8);
            $cssPath = 'css/' . $item->slug . '.css';
        @endphp
        <link rel="preload" href="{{ route('assets.show', ['path' => $cssPath]) }}?v={{ $cacheBuster }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link rel="stylesheet" href="{{ route('assets.show', ['path' => $cssPath]) }}?v={{ $cacheBuster }}">
        </noscript>
    @endforeach
@overwrite

@section('body')
    @foreach($content->getContent() as $block)
        @includeIf('core-cms::shared.blocks.renderer', ['block' => $block, 'content' => $content])
    @endforeach
@endsection