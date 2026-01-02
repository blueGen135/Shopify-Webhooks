@extends('layouts.app')
@section('content')
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-gray-200 w-full p-10">
        <h2 class="text-2xl font-semibold mb-4">{{ $product->title }}</h2>
    <p><strong>Vendor:</strong> {{ $product->vendor }}</p>
    <p><strong>Type:</strong> {{ $product->product_type }}</p>
    <p><strong>Status:</strong> {{ $product->status }}</p>
    <p><strong>Main Price:</strong> ₹{{ $product->price }}</p>
    <p><strong>Description:</strong> {!! $product->body_html !!}</p>

    <hr>

    {{-- Product Images --}}
    <h4>Images</h4>
    @if($product->images->count())
        <div class="grid grid-cols-6 gap-4 mb-4">
            @foreach ($product->images as $img)
                <img src="{{ $img->src }}" class="img-fluid rounded-md shadow w-32 h-32 object-cover">
            @endforeach
        </div>
    @else
        <p>No images found</p>
    @endif

    <hr>

    {{-- Variants --}}
    <h4>Variants</h4>
    @if($product->variants->count())
        <table class="w-full text-sm text-left rtl:text-right text-body" >
            <tr>
                <th scope="col" class="px-6 py-3 font-medium">Title</th>
                <th scope="col" class="px-6 py-3 font-medium">SKU</th>
                <th scope="col" class="px-6 py-3 font-medium">Price</th>
                <th scope="col" class="px-6 py-3 font-medium">Inventory</th>
            </tr>
            @foreach ($product->variants as $variant)
            <tr class="bg-neutral-primary border-b border-gray-200 hover:bg-gray-50">
                <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $variant->title }}</td>
                <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $variant->sku }}</td>
                <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $variant->price }}</td>
                <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $variant->inventory_quantity }}</td>
            </tr>
            @endforeach
        </table>
    @else
        <p>No variants found</p>
    @endif

    </div>
@endsection