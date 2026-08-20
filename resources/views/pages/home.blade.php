@extends('template.layout')
@section('content')
    <main id="main-content" class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/navbar')
            <h1 class="sr-only">Mavnus — Official Merchandise Whisnu Santika</h1>
            @include('components/banner')
        </div>
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/merch')
        </div>
        @include('components/footer')
    </main>
@endsection
