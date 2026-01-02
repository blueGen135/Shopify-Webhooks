@extends('layouts.app')
@section('content')
<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-gray-200 w-full p-10">
   
        <h2 class="text-2xl font-semibold mb-4">Create Order</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form action="{{ route('orders.storeForCustomer', $customer->id) }}" method="POST" class="mt-4 space-y-4"
                x-data="{ 
                    items: [{
                        product_id: '', 
                        variant_id: '', 
                        quantity: 1 
                    }],
                    discount: { type: '', value: 0 },
                    products: {{ $products->toJson() }},
                    currency: '{{ $currency }}'
                }"
                >
                @csrf
                <input type="hidden" name="customer_email" value="{{ $customer->email }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-semibold mb-3">Order Items</h3>

                        <div id="order-items">
                            @foreach ($existingItems as $index => $item)
                                <div class="border rounded p-3 mb-3 flex items-center justify-between item-row">
                                    <div>
                                        <p class="font-medium text-sm">{{ $item['title'] ?? 'Item' }}</p>
                                        <p class="text-sm text-gray-500">Variant ID: {{ $item['variant_id'] }}</p>
                                        <input type="text" name="items[{{ $index }}][line_item_id]" value="{{ $item['line_item_id'] }}">
                                        <input type="text" name="items[{{ $index }}][variant_id]" value="{{ $item['variant_id'] }}">
                                    </div>

                                        <div class="flex items-center gap-4">
                                            <input type="number"
                                                name="items[{{ $index }}][quantity]"
                                                value="{{ $item['quantity'] }}"
                                                min="1"
                                                class="w-20 border rounded p-1">

                                            <button type="button" onclick="removeOrderItem(this)" class="text-red-600 text-sm w-8 h-8 bg-red-100 rounded-full flex items-center justify-center cursor-pointer">✕</button>
                                        </div>
                                </div>
                            @endforeach
                        </div>

                        <p id="empty-order-msg"
                        class="text-sm text-gray-500 hidden">
                            No items in order
                        </p>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-3">Products</h3>

                        <div class="border rounded max-h-[500px] overflow-y-auto p-3 space-y-3">

                            @foreach ($products as $product)
                                <div class="border-b pb-2">
                                    <h4 class="font-medium">{{ $product->title }}</h4>

                                    <div class="mt-2 space-y-1">
                                        @foreach ($product->variants as $variant)
                                            <button type="button"
                                                    class="w-full text-left px-3 py-2 border rounded hover:bg-gray-50"
                                                    onclick="addVariantToOrder(
                                                        '{{ $variant->shopify_variant_id }}',
                                                        '{{ addslashes($product->title) }} — {{ addslashes($variant->title) }}',
                                                        '{{ $variant->price }}',
                                                    )">
                                                {{ $variant->title }}
                                                <span class="float-right text-sm text-gray-500">
                                                    {{ number_format($variant->price, 2) }} {{ $currency }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

            </div>
               
                <div class="border p-4 rounded mt-4">
                    <h4 class="font-semibold mb-2">Discount</h4>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- Discount Type -->
                        <select x-model="discount.type"
                                name="discount[type]"
                                class="border p-2 rounded">
                            <option value="">No Discount</option>
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage</option>
                        </select>

                        <!-- Discount Value -->
                        <input type="number"
                            step="0.01"
                            min="0"
                            x-model.number="discount.value"
                            name="discount[value]"
                            placeholder="Discount value"
                            class="border p-2 rounded"
                            :disabled="!discount.type">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 
                                   focus:ring-indigo-500">
                        Create Order
                    </button>
                </div>
        </form>
   
</div>

<script>
    let itemIndex = {{ count($existingItems) }};

    function addVariantToOrder(variantId, title, price) {
        const container = document.getElementById('order-items');

        const row = document.createElement('div');
        row.className = 'border rounded p-3 mb-3 flex items-center justify-between item-row';

        row.innerHTML = `
            <div>
                <p class="font-medium text-sm">${title}</p>
                 <p class="text-sm text-gray-500">Variant ID: ${variantId}</p>
                <input type="text" name="items[${itemIndex}][variant_id]" value="${variantId}">
                <input type="text" name="items[${itemIndex}][line_item_id]" value="">
            </div>

            <div class="flex items-center gap-2">
                <input type="number"
                    name="items[${itemIndex}][quantity]"
                    value="1"
                    min="1"
                    class="w-20 border rounded p-1">

                <button type="button"
                        onclick="removeOrderItem(this)"
                        class="text-red-600 text-sm">✕</button>
            </div>
        `;

        container.appendChild(row);
        itemIndex++;

        document.getElementById('empty-order-msg')?.classList.add('hidden');
    }

    function removeOrderItem(btn) {
        const row = btn.closest('.item-row');
        row.remove();

        if (!document.querySelector('.item-row')) {
            document.getElementById('empty-order-msg')?.classList.remove('hidden');
        }
    }

    
</script>
@endsection