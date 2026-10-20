@extends('template.layout')

@section('title', __('site.home.title'))
@section('meta_description', __('site.home.description'))

@section('content')
    <main id="main-content" class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            <h1 class="sr-only">{{ __('site.home.heading') }}</h1>
            @include('components/banner')
        </div>
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/merch')
        </div>
    </main>
@endsection
