@php use Illuminate\Support\Str; @endphp
@extends('core-cms::shared.blocks.layouts.layout')

@php
    $block = $block ??  [];
    $useContainer = $useContainer ?? $block['use-container'] ?? true;
    $section = $section ?? 'section';
    $classes = ['form'];
@endphp


@section('class')
    {{ join(' ', $classes) }}
@overwrite

@section('element')
    {{$section}}
@overwrite

@section('content')
    @php
        $contactClasses = [];

        if($useContainer) {
            $contactClasses[] = 'container';
        }
    @endphp
    <div class="{{ join(" ", $contactClasses) }}">
        @if(key_exists('title', $block)  && $block['title'] !== "")
            @include('core-cms::shared.blocks.components.title', ['block' => $block])
        @endif
        @if(key_exists('content', $block)  && $block['content'] !== "")
            @include('core-cms::shared.blocks.components.content', ['block' => $block])
        @endif
        <div class="card margin-block-start-6">
            <form
                class="grid"
                method="post"
                action="{{ route('forms.submit', ['slug' => $content->slug, 'formType' => 'form']) }}"
            >
                @csrf

                @foreach($block['sections'] as $section)
                    @if(key_exists('visible', $section) && $section['visible'])
                        <div class="grid margin-block-end-6">
                            @if(key_exists('title', $section)  && $section['title'] !== "")
                                @include('core-cms::shared.blocks.components.title', ['block' => $section])
                            @endif
                            @foreach($section['fields'] as $field)
                                @php
                                    $id = Str::slug($field['label'])
                                @endphp
                                @switch($field['type'])
                                    @case('text')
                                            @include('core-cms::shared.form-field', ['label' => $field['label'], 'name' => $id, 'type' => 'text', 'help' => $field['help']])
                                        @break
                                    @case('textarea')
                                            @include('core-cms::shared.form-field', ['label' => $field['label'], 'name' => $id, 'type' => 'textarea', 'help' => $field['help']])
                                        @break
                                    @case('select')
                                        @php
                                            $selectOptions = collect([]);
                                            if (array_key_exists('options', $field)) {
                                                $selectOptions = collect($field['options']);
                                            }
                                        @endphp
                                            @include('core-cms::shared.form-field', ['type' => 'select', 'label' => $field['label'], 'name' => $id, 'selectOptions' => $selectOptions->map(fn($s) => (object)['key' => Str::slug($s['option']),'label' => $s['option']])])
                                        @break
                                    @case('checkbox')
                                            @include('core-cms::shared.form-field', ['type' => 'checkbox', 'label' => $field['label'], 'name' => $id, 'help' => $field['help']])
                                        @break
                                @endswitch
                            @endforeach
                        </div>
                    @endif
                @endforeach
                <div class="form-switch">
                    <input type="checkbox" id="consentement" name="consentement" role="switch" required class="form-control">
                    <label for="consentement"><span class="switch"></span><span>{{ __('core-cms::core.form.consentement') }} <a href="{{ route('page.show', $options['privacy-policy']->slug) }}" target="_blank">{{ __('core-cms::core.privacy-policy') }}</a>.</span></label>
                </div>
                @include('core-cms::shared.captcha', ['label' => __('core-cms::core.captcha.value'), 'name' => 'captcha'])
                <div class="flex-group">
                    <button
                        class="button"
                        data-type="primary"
                        type="submit"
                    >
                        {{ __('core-cms::core.form.send') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@overwrite
