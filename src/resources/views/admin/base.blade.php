@php
    $menuManager = app(\NetAuraTech\CoreCms\Services\Admin\MenuManager::class);
    $menuItems = $menuManager->getMenuItems();
@endphp
<!DOCTYPE html>
<html lang="{{ Lang::locale() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimal-ui"/>
        @yield('meta')
        @include('theme::assets.css')
        @include('theme::assets.admin.css')
    </head>
    <body>
        <div class="admin">
            <nav class="bg-neutral-200">
                <!-- TODO: Add condition if we have a logo, else write sitename -->
                <h2 class="heading-2 text-center padding-block-8">Sitename</h2>
                <ul>
                    @foreach($menuItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" {{ menu_active(route($item['route'])) }}>{!! icon('home', 'small') !!}
                                {{ $item['label'] }}
                            </a>
                        </li>
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
                @yield('body')
            </main>
        </div>
    </body>
</html>