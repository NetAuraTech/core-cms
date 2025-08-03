@extends('core-cms::base')

@section('title', __('cms.password.reset.value'))

@section('description')
    @parent
@endsection

@section('body')
    <section class="container padding-block-6">
        <div class="card">
            <form class="grid" method="post" action="{{ route('password.email') }}">
                <h1 class="heading-1 text-center">{{ __('cms.password.reset.value') }}</h1>
                @csrf
                @include('core-cms::shared.input', ['type' => 'hidden', 'name' => 'token', 'value' => $request->route('token')])
                @include('core-cms::shared.input', ['label' => __('cms.email'), 'name' => 'email', 'value' => old('email')])
                @include('core-cms::shared.input', ['label' => __('cms.password.value'), 'type' => 'password', 'name' => 'password'])
                @include('core-cms::shared.input', ['label' => __('cms.password.confirm'), 'type' => 'password', 'name' => 'password_confirmation'])
                @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('cms.password.reset.value'), 'color' => 'primary'])
            </form>
        </div>
    </section>
@endsection
