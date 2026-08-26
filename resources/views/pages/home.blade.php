@extends('template.layout')

@section('title', 'Mavnus - Clothing, Accessories & Albums Original')
@section('meta_description',
    'Belanja clothes, accessories, dan albums original Mavnus. Kualitas terbaik, desain
    eksklusif, pengiriman ke seluruh Indonesia.')

@section('content')
    <main id="main-content" class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            <h1 class="sr-only">Mavnus — Official Merchandise Whisnu Santika</h1>
            @include('components/banner')
        </div>
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/merch')
        </div>
    </main>
@endsection
