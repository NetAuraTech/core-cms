@extends('core-cms::shared.blocks.layouts.layout')

@php
    $block = $block ??  [];
    $useContainer = $useContainer ?? $block['use-container'] ?? true;
    $section = $section ?? 'section';
    $classes = ['contact'];
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
                action="{{ route('forms.submit', ['slug' => $content->slug, 'formType' => 'contact']) }}"
            >
                @csrf
                <div class="grid-auto-fit align-items-center" style="width: initial;">
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::core.form.contact.lastname'), 'name' => 'lastname',])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::core.form.contact.firstname'), 'name' => 'firstname',])
                </div>
                <div class="grid-auto-fit align-items-center" style="width: initial;">
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::core.form.contact.email'), 'name' => 'email', 'type' => 'email',])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::core.form.contact.phone'), 'name' => 'phone'])
                </div>
                @php
                    $subjects = collect([]);

                    if (array_key_exists('subjects', $block)) {
                        $subjects = collect($block['subjects']);
                    }

                @endphp
                @include('core-cms::shared.form-field', ['type' => 'select', 'label' => __('core-cms::core.form.contact.subject'), 'name' => 'subject', 'selectOptions' => $subjects->map(fn($s) => (object)['key' => $s['option'],'label' => $s['option']])])
                @include('core-cms::shared.form-field', ['label' => __('core-cms::core.form.contact.message'), 'name' => 'content', 'type' => 'textarea'])
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
