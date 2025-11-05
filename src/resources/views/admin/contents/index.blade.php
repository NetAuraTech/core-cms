@extends('core-cms::admin.base')

@php
    $contentType = $contentType ?? "";
    $transKey = "$contentType-manager::admin.content.$contentType.value";

    if (trans()->has("core-cms::admin.content.$contentType.value")) {
        $transKey = "core-cms::admin.content.$contentType.value";
    }
@endphp

@section('title')
    {{ __('core-cms::admin.manage') }} {{ trans_choice($transKey, 2) }}
@endsection

@section('body')
    <section class="grid">
        <div class="flex-group justify-content-space-between align-items-center" style="width: initial">
            <h2 class="heading-2 flex-group align-items-center">
                @php
                    switch ($contentType) {
                        case 'template':
                            $icon = 'template';
                            break;
                        default:
                            $icon = $contentType;
                            break;
                    }
                @endphp
                {!! icon($icon, 'small') !!}
                {{ __('core-cms::admin.manage') }} {{ trans_choice($transKey, 2) }}
            </h2>
            <a class="button" href="{{ route('admin.contents.create', ['type' => $contentType]) }}" data-type="primary">
                {{ __('core-cms::admin.add') }} {{ trans_choice($transKey, 1) }}
            </a>
        </div>
        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('core-cms::admin.content.title') }}</th>
                    <th>{{ __('core-cms::admin.content.status.value') }}</th>
                    <th>{{ __('core-cms::admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($contents as $content)
                    <tr>
                        <td>
                            <a href="{{ route('admin.contents.edit', $content) }}">{{ $content->id }}</a>
                        </td>
                        <td>
                            <a href="{{ route('admin.contents.edit', $content) }}">{{ $content->title }}</a>
                        </td>
                        <td>{{ __('core-cms::admin.content.status.' . $content->status) }}</td>
                        <td>
                            <div class="flex-group align-items-center justify-content-flex-end" style="width: initial">
                                <a href="{{ route('admin.contents.edit', $content) }}" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.edit') }} {{ $content->title }}">{!! icon('edit', 'small') !!}</a>
                                <form
                                        class="clr-red-300"
                                        action="{{ route('admin.contents.destroy', $content) }}"
                                        method="post"
                                        onsubmit="return confirm('{{ __('core-cms::admin.delete.confirm') }}')">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.delete.value') }} {{ $content->title }}">
                                        {!! icon('trash', 'small') !!}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$contents->links()}}
        </div>
    </section>
@endsection
