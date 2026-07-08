@extends('template.layout')
@section('content')
    <div class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
        <div class="relative flex flex-col justify-center items-center w-full h-full">
            @include('components/navbar')
        </div>
        <div class="relative flex flex-col justify-center items-center w-full h-full">>
            @include('components/product-detail')
        </div>
        @include('components/footer')
    </div>
@endsection
