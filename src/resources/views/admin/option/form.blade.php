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
                {!! icon('option', 'small') !!} {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.option.value', 1) }}
            @else
                {!! icon('option', 'small') !!} {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.option.value', 1) }}
            @endif
        </h2>
        <div class="card">
            <form class="grid"
                  action="{{ route($option->exists ? 'admin.option.update' : 'admin.option.store', $option->key) }}"
                  method="POST">
                @csrf
                @method($option->exists ? 'put' : 'post')
                <div class="grid" x-data="{ type: '{{ $option->type }}' }">
                    @include('core-cms::shared.input', ['label' => __('core-cms::admin.option.key'), 'name' => 'key', 'value' => $option->key, 'disabled' => $option->used_by_cms])
                    <div class="form-group">
                        <label for="type" class="required">{{ __('core-cms::admin.option.type') }}</label>
                        <select id="type"
                                name="type"
                                class="form-control @error("type") is-invalid @enderror"
                                x-model="type"
                                @if($option->used_by_cms)
                                    disabled="disabled"
                                @endif
                        >
                            <option value="">{{ __('core-cms::core.select.option.choose') }}</option>
                            <option @selected(old("type", $option->type) === "image") value="image">{{ __('core-cms::admin.option.image.value') }}</option>
                            <option @selected(old("type", $option->type) === "text") value="text">{{ __('core-cms::admin.option.text.value') }}</option>
                            <option @selected(old("type", $option->type) === "theme") value="theme">{{ __('core-cms::admin.option.theme.value') }}</option>
                            <option @selected(old("type", $option->type) === "content") value="content">{{ __('core-cms::admin.option.content.value') }}</option>
                        </select>
                        @error("type")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <template x-if="type === 'text'">
                        @include('core-cms::shared.input', ['label' => __('core-cms::admin.value'), 'name' => 'value', 'value' => $option->value, 'type' => 'textarea'])
                    </template>
                    <template x-if="type === 'theme'">
                        @include('core-cms::shared.input', ['label' => __('core-cms::admin.value'), 'name' => 'value', 'value' => $option->value])
                    </template>
                    <template x-if="type === 'image'">
                        @include('core-cms::shared.attachment', ['label' => __('core-cms::admin.value'), 'name' => 'value', 'value' => $option->value])
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
                    <div class="text-center">
                        <button type="submit" class="button" data-type="primary">{{ __('core-cms::admin.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection