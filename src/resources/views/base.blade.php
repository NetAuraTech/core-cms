<!DOCTYPE html>
<html lang="{{ Lang::locale() }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>@yield('title')</title>
        @include('theme::assets.css')
        @yield('stylesheets')
        @yield('meta')
        @yield('description')
    </head>
    <body id="page-wrapper">
        <main class="body">
            @yield('body')
        </main>
    </body>
</html>