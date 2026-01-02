@extends('layouts.app')
@section('content')
<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-gray-200 w-full p-10">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">Create Product</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

       {{ url('/'); }}

        <form action="{{ route('shopify.store-product') }}" method="POST" enctype="multipart/form-data"
            x-data="productCreator()">
            @csrf

            <h2 class="font-bold text-xl mb-3">Create Product</h2>

            <label>Title</label>
            <input type="text" name="title" required class="border p-2 w-full mb-3">

            <label>Description</label>
            <textarea name="body_html" class="border p-2 w-full mb-3"></textarea>

            <label>Vendor</label>
            <input type="text" name="vendor" class="border p-2 w-full mb-3">

            <label>Product Type</label>
            <input type="text" name="product_type" class="border p-2 w-full mb-3">

            <label>Images</label>
            <input type="file" name="image"  class="border p-2 w-full mb-3">

          

            <button type="submit" class="bg-green-600 text-white px-4 py-2 mt-4 rounded">
                Create Product
            </button>
        </form>

    </div>
</div>
@endsection