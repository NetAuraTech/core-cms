@extends('core-cms::base')

@section('title', __('cms.password.define.value'))

@section('description')
    @parent
@endsection

@section('body')
    <section class="container padding-block-6">
        <div class="car">
            <form method="post" class="grid" action="{{ route('password.define') }}">
                <h1 class="heading-1 text-center">{{ __('cms.password.define.value') }}</h1>
                <p>{{ __('cms.password.define.info') }}</p>
                @csrf
                @include('core-cms::shared.input', ['label' => __('cms.password.new'), 'name' => 'password', 'type' => 'password', 'errorLocation' => 'updatePassword'])
                @include('core-cms::shared.input', ['label' => __('cms.password.confirm'), 'name' => 'password_confirmation', 'type' => 'password', 'errorLocation' => 'updatePassword'])
                @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('cms.password.define.value'), 'name' => 'action', 'value' => 'password', 'color' => 'primary'])
            </form>
        </div>
    </section>
@endsection


