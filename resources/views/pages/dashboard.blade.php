@extends('template.dashboard.layout')
@section('content')
    <div class="flex flex-col justify-center items-center bg-black text-white w-full">
        @include('components/dashboard/navbar')
        <div class="flex flex-col items-center justify-center h-screen p-8">
            <h1 class="text-3xl font-bold">Mavnus Dasshboard</h1>
            <h1 class="text-xl">Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam expedita sunt voluptatum cum illum voluptatibus fugit pariatur recusandae corporis sed cumque maxime, quos officia nesciunt amet facilis quisquam!</h1>
        </div>
    </div>
@endsection