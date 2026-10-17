@extends('template.layout')

@section('title', __('site.listing.clothes_title'))
@section('meta_description', __('site.listing.clothes_description'))

@section('content')
    <main id="main-content" class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/clothes')
        </div>
    </main>
@endsection
