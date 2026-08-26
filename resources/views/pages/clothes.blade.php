@extends('template.layout')

@section('title', 'Clothes - Mavnus')
@section('meta_description', 'Koleksi clothes original Mavnus. Berbagai warna, material, dan ukuran S sampai XL.')

@section('content')
    <main id="main-content" class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/clothes')
        </div>
    </main>
@endsection
