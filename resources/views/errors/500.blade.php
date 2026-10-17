@extends('template.bare-layout')
@section('content')
    <div class="min-h-screen bg-black flex flex-col uppercase items-center justify-center text-center px-4">
        <h1 class="text-8xl font-bold text-white">500</h1>
        <p class="text-2xl font-semibold text-white mt-4">{{ __('site.errors.server_title') }}</p>
        <p class="text-white mt-2">{{ __('site.errors.server_text') }}</p>
        <a href="{{ route('home') }}"
            class="mt-6 px-4 py-2 bg-white text-black rounded-lg font-semibold hover:bg-gray-100 transition">
            {{ __('site.errors.back_home') }}
        </a>
    </div>
@endsection
