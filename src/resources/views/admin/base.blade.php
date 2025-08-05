@php
    $menuManager = app(\Netauratech\CoreCms\Services\Admin\MenuManager::class);
    $menuItems = $menuManager->getMenuItems();
@endphp
<!DOCTYPE html>
<html lang="{{ Lang::locale() }}">
    <head>
        <meta charset="UTF-8">
        <title>@yield('title') | {{ $options['site_name'] }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimal-ui"/>
        <script defer src="{{ route('translations') }}"></script>
        @yield('meta')
        @vite(['resources/ts/app.ts'])
        @vite(['resources/ts/admin.ts'])
        @includeIf('theme::assets.css')
        @includeIf('theme::assets.admin.css')
    </head>
    <body>
        <div class="admin">
            <nav class="bg-neutral-200">
                <!-- TODO: Add condition if we have a logo, else write sitename -->
                <h2 class="heading-2 text-center padding-block-8">{{ $options['site_name'] }}</h2>
                <ul>
                    @foreach($menuItems as $item)
                        @if(isset($item['children']))
                            <h4 class="heading-4 padding-inline-4 padding-block-2">{{ $item['label'] }}</h4>
                            @foreach($item['children'] as $child)
                                <li>
                                    <a href="{{ route($child['route']) }}" {{ menu_active(route($child['route'])) }}>{!! icon($child['icon'], 'small') !!}
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li>
                                <a href="{{ route($item['route']) }}" {{ menu_active(route($item['route'])) }}>{!! icon($item['icon'], 'small') !!}
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </nav>
            <header style="padding-inline: 2rem">
                <div class="flex-group align-items-center justify-content-space-between" style="width: 100%">
                    <site-notifications></site-notifications>
                    <div class="flex-group align-items-center">
                        <!-- Count spam -->
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button class="button padding-0" data-type="transparent">{!! icon('logout', 'small') !!}</button>
                        </form>
                    </div>
                </div>
            </header>
            <main>
                @include('core-cms::shared.partials.flash', ['floating' => true, 'duration' => 2])
                @yield('body')
            </main>
        </div>
    </body>
    <spotlight-bar></spotlight-bar>
    <script>
        window.cms = {
            ...(window.cms || {}),
            USER: {{ Auth::user() ? Auth::user()->id : 'null' }},
            NOTIFICATION: new Date({{ (\Illuminate\Support\Facades\Auth::user() and \Illuminate\Support\Facades\Auth::user()->notifications_read_at) ? \Illuminate\Support\Facades\Auth::user()->getNotificationsReadAtTimestamp() : 0 }} * 1000)
        };
    </script>
    @yield('javascripts_footer')
</html>