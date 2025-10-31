@extends('core-cms::admin.base')

@section('title')
    @if($role->exists)
        {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.role.value', 1) }}
    @else
        {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.role.value', 1) }}
    @endif
@endsection

@section('meta')
    <meta name="turbolinks-visit-control" content="reload">
@endsection

@section('body')
    <section class="grid">
        <h2 class="heading-2 flex-group align-items-center">
            @if($role->exists)
                {!! icon('role', 'small') !!} {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.role.value', 1) }}
            @else
                {!! icon('role', 'small') !!} {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.role.value', 1) }}
            @endif
        </h2>
        <div class="card">
            <form class="grid"
                  action="{{ route($role->exists ? 'admin.role.update' : 'admin.role.store', $role) }}"
                  method="POST">
                @csrf
                @method($role->exists ? 'put' : 'post')
                <div class="grid">
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.role.name'), 'name' => 'name', 'value' => $role->name])
                    <h2 class="heading-2 flex-group align-items-center">{{ __('core-cms::admin.permission') }}</h2>
                    @foreach($permission as $value)
                        @include('core-cms::shared.form-field', ['type' => 'checkbox', 'label' => $value->name, 'name' => 'permission[]', 'id' => $value->name, 'current' => $value->id, 'value' => in_array($value->id, $rolePermissions) ? true : false])
                    @endforeach
                    <div class="text-center">
                        <button type="submit" class="button" data-type="primary">{{ __('core-cms::admin.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
