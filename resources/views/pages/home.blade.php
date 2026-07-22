@extends('template.layout')
@section('content')
    <main class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/navbar')
            @include('components/banner')
        </div>
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/merch')
        </div>
        @include('components/footer')
    </main>
@endsection
