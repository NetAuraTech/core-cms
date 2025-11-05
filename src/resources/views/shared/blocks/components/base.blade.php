@php
    $block = $block ?? [];

    /**
     * Classes management
     */
    $classes = ['block', 'block__' . substr(md5(json_encode($block)), 0, 8)];

    if(key_exists('background-image', $block) && $block['background-image'] != "") {
        $classes[] = 'block__background-image';
    }

    if (key_exists('padding-block', $block)) {
        $classes[] = 'padding-block-' . $block['padding-block'];
    }

    if (key_exists('padding-inline', $block)) {
        $classes[] = 'padding-inline-' . $block['padding-inline'];
    }

    if (key_exists('border-top-left-radius', $block) && $block['border-top-left-radius'] > 0) {
        $classes[] = 'border-radius-top-left-' . $block['border-top-left-radius'] ;
    }

    if (key_exists('border-top-right-radius', $block) && $block['border-top-right-radius'] > 0) {
        $classes[] = 'border-radius-top-right-' . $block['border-top-right-radius'] ;
    }

    if (key_exists('border-bottom-left-radius', $block) && $block['border-bottom-left-radius'] > 0) {
        $classes[] = 'border-radius-bottom-left-' . $block['border-bottom-left-radius'] ;
    }

    if (key_exists('border-bottom-right-radius', $block) && $block['border-bottom-right-radius'] > 0) {
        $classes[] = 'border-radius-bottom-right-' . $block['border-bottom-right-radius'] ;
    }

    $id = null;
    if(key_exists('id', $block) && $block['id'] !== "") {
        $id = $block['id'];
    }
@endphp

<section
    class="{{ join(" ", $classes) }}"
    @if($id)id="{{ $id }}" @endif
>
    @php
        $animations = [];
        $styles = [];

        if(key_exists('general_animation', $block) && $block['general_animation'] !== "") {
            $animations[] = $block['general_animation'];

            if(key_exists("general_delay", $block) && $block["general_delay"] !== "0") {
                $styles[] = '--delay: ' . $block["general_delay"] . 's;';
            }
        }
    @endphp
    <div
        class="container {{ join(" ", $animations) }}"
        @if(count($styles) > 0)style="{{ implode(";", $styles) }}"@endif
    >
        @yield('content')
    </div>
</section>
