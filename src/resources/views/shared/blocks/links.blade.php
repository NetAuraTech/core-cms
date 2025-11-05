@extends('core-cms::shared.blocks.layouts.layout')

@php
    $block = $block ??  [];
    $useContainer = $useContainer ?? $block['use-container'] ?? true;
    $section = $section ?? 'section';
    $classes = ['links'];
@endphp


@section('class')
    {{ join(' ', $classes) }}
@overwrite

@section('element')
    {{$section}}
@overwrite

@section('content')
    @php
        $linksClasses = [];

        if($useContainer) {
            $linksClasses[] = 'container';
        }
    @endphp
    <div class="{{ join(" ", $linksClasses) }}">
        @if(key_exists('title', $block)  && $block['title'] !== "")
            @include('core-cms::shared.blocks.components.title', ['block' => $block])
        @endif
        <ul>
            @if(key_exists('links', $block))
                @foreach($block['links'] as $link)
                    @php
                        if($link['type'] == 'internal' && $link['url'] !== "") {
                            $json = json_decode($link['url'], true);
                            $path = key_exists('slug', $json) ? route($json['path'], $json['slug']) : route($json['path']);
                            $label = $link['label'] !== '' ? $link['label'] :  $json['label'];
                        } else {
                            $path = $link['url'];
                            $label = $link['label'];
                        }

                    @endphp
                    <li><a href="{{ $path }}" {{ menu_active($path) }}>{{ $label }}</a></li>
                @endforeach
            @endif
        </ul>
    </div>
@overwrite
