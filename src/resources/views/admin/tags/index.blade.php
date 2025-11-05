@extends('core-cms::admin.base')

@section('title')
    {{ __('core-cms::admin.manage') }} {{ trans_choice('core-cms::admin.content.tag.value', 2) }}
@endsection

@section('body')
    <section class="grid">
        <div class="flex-group justify-content-space-between align-items-center" style="width: initial">
            <h2 class="heading-2 flex-group align-items-center">
                {!! icon('tag', 'small') !!}
                {{ __('core-cms::admin.manage') }} {{ trans_choice('core-cms::admin.content.tag.value', 2) }}
            </h2>
            <a class="button" href="{{ route('admin.tags.create') }}" data-type="primary">
                {{ __('core-cms::admin.add') }} {{ trans_choice('core-cms::admin.content.tag.value', 1) }}
            </a>
        </div>
        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('core-cms::admin.content.name') }}</th>
                    <th>{{ __('core-cms::admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tags as $tag)
                <tr>
                    <td>
                        <a href="{{ route('admin.tags.edit', $tag) }}">{{ $tag->id }}</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.tags.edit', $tag) }}">{{ $tag->name }}</a>
                    </td>
                    <td>
                        <div class="flex-group align-items-center justify-content-flex-end" style="width: initial">
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.edit') }} {{ $tag->name }}">{!! icon('edit', 'small') !!}</a>
                            <form
                                    class="clr-red-300"
                                    action="{{ route('admin.tags.destroy', $tag) }}"
                                    method="post"
                                    onsubmit="return confirm('{{ __('core-cms::admin.delete.confirm') }}')">
                                @csrf
                                @method('delete')
                                <button type="submit" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.delete.value') }} {{ $tag->name }}">
                                    {!! icon('trash', 'small') !!}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            {{$tags->links()}}
        </div>
    </section>
@endsection