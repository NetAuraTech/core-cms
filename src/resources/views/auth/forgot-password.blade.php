@extends('core-cms::base')

@section('title', __('cms.password.forgotten.value'))

@section('description')
    @parent
@endsection

@section('body')
    <section class="container padding-block-6">
        @if (session('status'))
            <alert-message type="success" is-floating="true">
                {{ __('cms.instruction.password.reset') }}
            </alert-message>
        @endif
        <div class="card">
            <form class="grid" method="post" action="{{ route('password.email') }}">
                <h1 class="heading-1 text-center">{{ __('cms.password.forgotten.value') }}</h1>
                <p>
                    {{ __('cms.password.forgotten.info') }}
                </p>
                @csrf
                @include('core-cms::shared.input', ['label' => __('cms.email'), 'name' => 'email', 'value' => old('email')])
                @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('cms.instruction.send'), 'color' => 'primary'])
            </form>
        </div>
    </section>
@endsection


