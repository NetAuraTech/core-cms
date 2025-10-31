@extends('core-cms::admin.base')

@section('title')
    @if($user->exists)
        {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.user.value', 1) }}
    @else
        {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.user.value', 1) }}
    @endif
@endsection

@section('meta')
    <meta name="turbolinks-visit-control" content="reload">
@endsection

@section('body')
    <section class="grid">
        <h2 class="heading-2 flex-group align-items-center">
            @if($user->exists)
                {!! icon('users', 'small') !!} {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.user.value', 1) }}
            @else
                {!! icon('users', 'small') !!} {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.user.value', 1) }}
            @endif
        </h2>
        <div class="card">
            <form class="grid"
                  action="{{ route($user->exists ? 'admin.user.update' : 'admin.user.store', $user) }}"
                  method="POST">
                @csrf
                @method($user->exists ? 'put' : 'post')
                <div class="grid">
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.user.username'), 'name' => 'username', 'value' => $user->username])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.user.email'), 'name' => 'email', 'value' => $user->email, 'type' => 'email'])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.user.password.value'), 'name' => 'new_password', 'type' => 'password'])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.user.password.confirm'), 'name' => 'new_password_confirmation', 'type' => 'password'])
                    <h3 class="heading-3">{{ trans_choice('core-cms::admin.role.value', 0) }}</h3>
                    @foreach($roles as $value)
                        @include('core-cms::shared.form-field', ['type' => 'checkbox', 'label' => $value->name, 'name' => 'role[]', 'id' => $value->name, 'current' => $value->id, 'value' => in_array($value->id, $userRoles)])
                    @endforeach
                    <div class="text-center">
                        <button type="submit" class="button" data-type="primary">{{ __('core-cms::admin.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
