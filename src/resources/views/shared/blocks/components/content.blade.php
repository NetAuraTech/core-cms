@php
    $blockKey = $blockKey ?? 'content';
    $block = $block ?? [];

    $contentClasses = $contentClasses ?? [];
    $contentClasses[] = "block__" . substr(md5(json_encode($block)), 0, 8) . "-{$blockKey}";

    $contentStyles = [];

    if(key_exists("{$blockKey}_animation", $block) && $block["{$blockKey}_animation"] !== '') {
        $contentClasses[] = $block["{$blockKey}_animation"];

        if(key_exists("{$blockKey}_delay", $block) && $block["{$blockKey}_delay"] !== "0") {
            $contentStyles[] = '--delay: ' . $block["{$blockKey}_delay"] . 's;';
        }
    }
@endphp

<div
    class="{{ join(" ", $contentClasses) }}"
    @if(count($contentStyles) > 0)style="{{ implode(";", $contentStyles) }}"@endif
>
    @shortcode($block['content'], ['content' => $content ?? '', 'options' => $options ?? []])
</div>
