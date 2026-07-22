@extends('template.layout')
@section('content')
    <main class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/navbar')
        </div>
        <div class="relative flex flex-col justify-center items-center w-full h-full">>
            @include('components/clothes')
        </div>
        @include('components/footer')
    </main>
@endsection
