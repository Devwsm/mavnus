@extends('template.layout')

@section('title', $product->name . ' - Mavnus')
@section('meta_description',
    \Illuminate\Support\Str::limit(
    strip_tags($product->description) ?:
    $product->name .
    ' by
    Mavnus. ' .
    $product->formatted_price,
    155,
    ))
    @if ($product->images->first())
        @section('og_image', Storage::url($product->images->first()->image_path))
    @endif

    @section('content')
        <main id="main-content" class="flex flex-col justify-center items-center bg-[#FBFBFD] w-full">
            <div class="relative flex flex-col justify-center items-center w-full h-full">
                @include('components/product-detail')
            </div>
        </main>
    @endsection
