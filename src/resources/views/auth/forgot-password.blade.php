@extends('core-cms::base')

@section('title', __('core-cms::auth.account.password.forgotten.value'))

@section('description')
    @parent
@endsection

@section('header')
    @if($options['header'] !== "")
        @foreach($options['header']->getContent() as $block)
            @includeIf('content-manager::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('footer')
    @if($options['footer'] !== "")
        @foreach($options['footer']->getContent() as $block)
            @includeIf('content-manager::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('body')
    <section class="container padding-block-6">
        @if (session('status'))
            <alert-message type="success" is-floating="true">
                {{ __('core-cms::auth.account.password.reset.instruction') }}
            </alert-message>
        @endif
        <div class="card">
            <form class="grid" method="post" action="{{ route('password.email') }}">
                <h1 class="heading-1 text-center">{{ __('core-cms::auth.account.password.forgotten.value') }}</h1>
                <p>
                    {{ __('core-cms::auth.account.password.forgotten.info') }}
                </p>
                @csrf
                @include('core-cms::shared.input', ['label' => __('core-cms::auth.account.email'), 'name' => 'email', 'value' => old('email')])
                @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('core-cms::auth.account.password.reset.send'), 'color' => 'primary'])
            </form>
        </div>
    </section>
@endsection