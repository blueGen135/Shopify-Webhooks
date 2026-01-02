@extends('layouts.app')
@section('content')
<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-gray-200 w-full">
    <a href="{{ route('shopify.create-product') }}" class="bg-purple-800 text-white px-4 py-2 rounded inline-block mt-4 mb-4">Add New Product</a>
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-soft border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">ID</th>
                    <th scope="col" class="px-6 py-3 font-medium">Title</th>
                    <th scope="col" class="px-6 py-3 font-medium">Image</th>
                    <th scope="col" class="px-6 py-3 font-medium">Price</th>
                    <th scope="col" class="px-6 py-3 font-medium">Vendor</th>
                    <th scope="col" class="px-6 py-3 font-medium">Status</th>
                    <th scope="col" class="px-6 py-3 font-medium">View</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="bg-neutral-primary border-b border-gray-200 hover:bg-gray-50">
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product->id }}</td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product->title }}</td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            @if(isset($product->image))
                            <img src="{{ $product->image }}" class="w-16 h-16 object-cover rounded-md">
                            @else
                            —
                            @endif
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product->price }}</td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product->vendor }}</td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product->status ?? 'N/A' }}</td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap"><a class="w-8 h-8 flex items-center justify-center bg-green-600 text-white rounded-md" href="{{ route('shopify.product.show', $product->id) }}"> <i class="bi bi-eye text-lg"></i> </a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="py-20 px-6">
            {{ $products->links() }}
        </div>
    </div>
@endsection