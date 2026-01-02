@extends('layouts.app')
@section('content')
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-gray-200 w-full">
        
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-soft border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">ID</th>
                    <th scope="col" class="px-6 py-3 font-medium">Title</th>
                    <th scope="col" class="px-6 py-3 font-medium">Image</th>
                    <th scope="col" class="px-6 py-3 font-medium">Vendor</th>
                    <th scope="col" class="px-6 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="bg-neutral-primary border-b border-gray-200 hover:bg-gray-50">
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product['id'] }}</td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product['title'] }}</td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            @if(isset($product['image']['src']))
                            <img src="{{ $product['image']['src'] }}" class="w-16 h-16 object-cover rounded-md">
                            @else
                            —
                            @endif
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product['vendor'] }}</td>
                        <td scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">{{ $product['status'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
@endsection                
