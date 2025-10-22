@extends('core-cms::admin.base')

@section('title')
    @if($option->exists)
        {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.option.value', 1) }}
    @else
        {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.option.value', 1) }}
    @endif
@endsection

@section('meta')
    <script src="//unpkg.com/alpinejs" defer></script>
    <meta name="turbolinks-visit-control" content="reload">
@endsection

@section('body')
    <section class="grid">
        <h2 class="heading-2 flex-group align-items-center">
            @if($option->exists)
                {!! icon('cog', 'small') !!} {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.option.value', 1) }}
            @else
                {!! icon('cog', 'small') !!} {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.option.value', 1) }}
            @endif
        </h2>
        <div class="card">
            <form class="grid"
                  action="{{ route($option->exists ? 'admin.option.update' : 'admin.option.store', $option->exists ? $option->key : []) }}"
                  method="POST">
                @csrf
                @method($option->exists ? 'put' : 'post')
                <div class="grid" x-data="{ type: '{{ old('type', $option->type) }}', category: '{{ old('category', $option->category) }}' }">
                    @include('core-cms::shared.form-field', [
                        'label' => __('core-cms::admin.option.key'),
                        'name' => 'key',
                        'value' => $option->key,
                        'disabled' => $option->category !== 'custom'
                    ])
                    <div class="form-group">
                        <label for="type" class="required">{{ __('core-cms::admin.option.type.value') }}</label>
                        <select id="type"
                                name="type"
                                class="form-control @error("type") is-invalid @enderror"
                                x-model="type"
                                @if($option->category !== 'custom')
                                    disabled="disabled"
                                @endif
                        >
                            <option value="">{{ __('core-cms::core.select.option.choose') }}</option>
                            <option @selected(old("type", $option->type) === "text") value="text">{{ __('core-cms::admin.option.type.text') }}</option>
                            <option @selected(old("type", $option->type) === "content") value="content">{{ __('core-cms::admin.option.type.content') }}</option>
                            <option @selected(old("type", $option->type) === "template") value="template">{{ __('core-cms::admin.option.type.template') }}</option>
                            <option @selected(old("type", $option->type) === "number") value="number">{{ __('core-cms::admin.option.type.number') }}</option>
                            <option @selected(old("type", $option->type) === "boolean") value="boolean">{{ __('core-cms::admin.option.type.boolean') }}</option>
                            @foreach($formFields as $field)
                                <option @selected(old("type", $option->type) === $field['type']) value="{{ $field['type'] }}">{{ $field['label'] }}</option>
                            @endforeach
                        </select>
                        @error("type")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <template x-if="type === 'text'">
                        <div>
                            @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.value'), 'name' => 'value', 'value' => $option->value, 'type' => 'textarea'])
                            @if(str_starts_with($option->key, 'schedule_'))
                                <div class="margin-block-start-3 padding-4 border-radius-1" style="background-color: var(--neutral-100); border-left: 4px solid var(--accent-400);">
                                    <p class="margin-block-end-2"><strong>{{ __('core-cms::admin.option.schedule.format_hint') }}</strong></p>
                                    <p class="margin-block-end-2">{{ __('core-cms::admin.option.schedule.examples') }}</p>
                                    <ul style="margin-left: 1.5rem; margin-bottom: 0;">
                                        <li>{{ __('core-cms::admin.option.schedule.continuous') }}</li>
                                        <li>{{ __('core-cms::admin.option.schedule.with_break') }}</li>
                                        <li>{{ __('core-cms::admin.option.schedule.closed') }}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </template>

                    <template x-if="type === 'number'">
                        @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.value'), 'name' => 'value', 'value' => $option->value, 'type' => 'number'])
                    </template>
                    <template x-if="type === 'boolean'">
                        @include('core-cms::shared.form-field', ['type' => 'checkbox', 'label' => __('core-cms::admin.value'), 'name' => 'value', 'value' => $option->value])
                    </template>
                    <template x-if="type === 'content'">
                        <div class="form-group">
                            <label for="value" class="required">{{ __('core-cms::admin.value') }}</label>
                            <select id="value"
                                    name="value"
                                    class="form-control @error("type") is-invalid @enderror"
                            >
                                <option value="">{{ __('core-cms::core.select.option.choose') }}</option>
                                <optgroup label="{{ __('core-cms::admin.option.content.article') }}">
                                    @foreach($articles as $post)
                                        <option value="{{ $post->id }}"
                                                @if($option->value== $post->id) selected @endif>{{ $post->title }}</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ __('core-cms::admin.option.content.post') }}">
                                    @foreach($pages as $post)
                                        <option value="{{ $post->id }}"
                                                @if($option->value== $post->id) selected @endif>{{ $post->title }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error("type")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </template>
                    <template x-if="type === 'template'">
                        <div class="form-group">
                            <label for="value" class="required">{{ __('core-cms::admin.value') }}</label>
                            <select id="value"
                                    name="value"
                                    class="form-control @error("type") is-invalid @enderror"
                            >
                                <option value="">{{ __('core-cms::core.select.option.choose') }}</option>
                                @foreach($templates as $post)
                                    <option value="{{ $post->id }}"
                                            @if($option->value== $post->id) selected @endif>{{ $post->title }}</option>
                                @endforeach
                            </select>
                            @error("type")
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </template>
                    @foreach($formFields as $field)
                        @php
                            $fieldValue = old('value', $option->value ?? null);
                        @endphp
                        @include($field['template'], [...$field['props'] ?? [], 'value' => $fieldValue])
                    @endforeach
                    <div class="text-center">
                        <button type="submit" class="button" data-type="primary">{{ __('core-cms::admin.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
