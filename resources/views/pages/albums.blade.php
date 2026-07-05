@extends('template.layout')
@section('content')
    <div class="flex flex-col justify-center items-center w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/navbar')
        </div>
        <div class="relative flex flex-col justify-center items-center w-full h-full">>
            @include('components/albums')
        </div>
        @include('components/footer')
    </div>
@endsection
