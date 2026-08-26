@extends('template.layout')

@section('title', 'Albums - Mavnus')
@section('meta_description', 'Koleksi albums original Mavnus.')

@section('content')
    <main id="main-content" class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/albums')
        </div>
    </main>
@endsection
