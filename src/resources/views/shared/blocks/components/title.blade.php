@php
    $blockKey = $blockKey ?? 'title';
    $block = $block ?? [];

    $titleClasses = ["margin-block-end-6", "block__" . substr(md5(json_encode($block)), 0, 8) . "-{$blockKey}"];
    $titleStyles = [];

    if(key_exists("{$blockKey}_animation", $block) && $block["{$blockKey}_animation"] !== '') {
        $titleClasses[] = $block["{$blockKey}_animation"];

        if(key_exists("{$blockKey}_delay", $block) && $block["{$blockKey}_delay"] !== "0") {
            $titleStyles[] = '--delay: ' . $block["{$blockKey}_delay"] . 's;';
        }
    }
@endphp

@if(key_exists("{$blockKey}-level", $block) && $block["{$blockKey}-level"] === 'h1')
    @php
        $titleClasses[] = 'heading-1'
    @endphp
    <h1
        class="{{ join(" ", $titleClasses) }}"
        @if(count($titleStyles) > 0)style="{{ implode(";", $titleStyles) }}"@endif
    >
        {{ $block[$blockKey] }}
    </h1>
@elseif(key_exists("{$blockKey}-level", $block) && $block["{$blockKey}-level"] === 'h2')
    @php
        $titleClasses[] = 'heading-2'
    @endphp
    <h2
        class="{{ join(" ", $titleClasses) }}"
        @if(count($titleStyles) > 0)style="{{ implode(";", $titleStyles) }}"@endif
    >
        {{ $block[$blockKey] }}
    </h2>
@elseif(key_exists("{$blockKey}-level", $block) && $block["{$blockKey}-level"] === 'h3')
    @php
        $titleClasses[] = 'heading-3'
    @endphp
    <h3
        class="{{ join(" ", $titleClasses) }}"
        @if(count($titleStyles) > 0)style="{{ implode(";", $titleStyles) }}"@endif
    >
        {{ $block[$blockKey] }}
    </h3>
@elseif(key_exists("{$blockKey}-level", $block) && $block["{$blockKey}-level"] === 'h4')
    @php
        $titleClasses[] = 'heading-4'
    @endphp
    <h4
        class="{{ join(" ", $titleClasses) }}"
        @if(count($titleStyles) > 0)style="{{ implode(";", $titleStyles) }}"@endif
    >
        {{ $block[$blockKey] }}
    </h4>
@else
    @php
        $titleClasses[] = 'heading-5'
    @endphp
    <h5
        class="{{ join(" ", $titleClasses) }}"
        @if(count($titleStyles) > 0)style="{{ implode(";", $titleStyles) }}"@endif
    >
        {{ $block[$blockKey] }}
    </h5>
@endif
