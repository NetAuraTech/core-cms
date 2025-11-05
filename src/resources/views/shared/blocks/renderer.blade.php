@php
    $block = $block ?? [];
    $key = $key ?? '_name';
    $props = $props ?? [];
    $options = $options ?? [];
    $css = $css ?? null;

    $theme = $options['theme'] ?? 'default';
    $viewName = $block[$key] ?? $view ?? 'missing';

    $sharedPath = "core-cms::shared.blocks.$viewName";
    $themePath = "theme::$viewName";
    $extensionPath = "extensions::$viewName";

    $commonView = [
        'automatic-gallery',
        'carousel',
        'contact',
        'media',
        'links',
        'section',
        'theme-switcher',
        'form'
    ];

    $resolvedView =
        View::exists($themePath)
            ? $themePath
        : (View::exists($extensionPath)
            ? $extensionPath
        : (View::exists($sharedPath)
            ? $sharedPath
        : 'core-cms::shared.blocks.missing'));
@endphp

@if($css)
    <div>
        <style>{!! $css !!}</style>
        @endif

        @include($resolvedView, ['block' => $block, 'key' => $key, ...$props])

        @if($css)
    </div>
@endif
