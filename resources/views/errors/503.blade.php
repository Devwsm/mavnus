@extends('template.bare-layout')
@section('content')
    <div class="min-h-screen bg-black flex text-white items-center justify-center text-center">
        <img src="{{ \App\Support\Img::url('aset/mavnus-maintenance.webp', 'aset/mavnus-maintenance.png') }}"
            fetchpriority="high" alt="mavnus" class="object-cover object-center w-100 rounded-full">
    </div>
@endsection
