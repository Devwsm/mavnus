@extends('template.layout')

@section('title', 'Accessories - Mavnus')
@section('meta_description', 'Koleksi accessories original Mavnus untuk melengkapi gaya kamu.')

@section('content')
    <main id="main-content" class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/navbar')
        </div>
        <div class="relative flex flex-col justify-center items-center w-full h-full">>
            @include('components/accessoris')
        </div>
        @include('components/footer')
    </main>
@endsection
