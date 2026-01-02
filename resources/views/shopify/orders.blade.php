@extends('layouts.app')
@section('content')
<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-gray-200 w-full p-2">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif
     
   @foreach ($orders as $order)
    <div x-data="{ open: false }" class="bg-white rounded-xl shadow p-4 mb-5 cursor-pointer border border-gray-100 hover:shadow-lg transition">
        
        {{-- Order Header --}}
        <div class="flex justify-between items-center mb-3">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <p class="text-lg font-bold text-gray-900">
                        #{{ $order->order_number }}
                    </p>
                    <span class="text-sm text-gray-600">
                        {{ $order->shopify_created_at ? $order->shopify_created_at->format('M d') : 'N/A' }}
                    </span>
                </div>
                
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person text-gray-500"></i>
                        <span class="text-gray-700">{{ $order->customer_name ?? 'Guest' }}</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <i class="bi bi-envelope text-gray-500"></i>
                        <span class="text-gray-700">{{ $order->customer_email ?? 'No email' }}</span>
                    </div>
                    
                    <div class="font-bold text-red-600">
                        {{ $order->currency }} {{ number_format($order->total_price, 2) }}
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 mt-2">
                    {{-- Financial Status Badge --}}
                    <span class="px-2 py-1 text-xs rounded-full {{ 
                        $order->financial_status == 'paid' ? 'bg-green-100 text-green-800' : 
                        ($order->financial_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                        ($order->financial_status == 'refunded' ? 'bg-red-100 text-red-800' : 
                        'bg-gray-100 text-gray-800')) 
                    }}">
                        {{ ucfirst($order->financial_status ?? 'unknown') }}
                    </span>
                    
                    {{-- Fulfillment Status Badge --}}
                    <span class="px-2 py-1 text-xs rounded-full {{ 
                        $order->fulfillment_status == 'fulfilled' ? 'bg-blue-100 text-blue-800' : 
                        ($order->fulfillment_status == 'partial' ? 'bg-orange-100 text-orange-800' : 
                        'bg-gray-100 text-gray-800') 
                    }}">
                        {{ ucfirst($order->fulfillment_status ?? 'unfulfilled') }}
                    </span>
                    
                    {{-- Processing Method --}}
                    @if($order->processing_method)
                        <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                            {{ ucfirst($order->processing_method) }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Mark as Fulfilled Button --}}
                @if($order->fulfillment_status != 'fulfilled')
                    <form action="{{ route('orders.fulfill', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                            <i class="bi bi-check-circle"></i>
                            Fulfill
                        </button>
                    </form>
                @else
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium flex items-center gap-2">
                        <i class="bi bi-check2-circle"></i>
                        Fulfilled
                    </span>
                @endif
                
                {{-- Expand/Collapse Button --}}
                <button @click="open = !open" 
                        class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-full hover:bg-gray-100 transition">
                    <span x-show="!open" class="text-gray-500 text-lg">
                        <i class="bi bi-chevron-down"></i>
                    </span>
                    <span x-show="open" class="text-gray-500 text-lg">
                        <i class="bi bi-chevron-up"></i>
                    </span>
                </button>
            </div>
        </div>

        {{-- Expandable Details Section --}}
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            
            {{-- Order Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Price Breakdown --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-3">Order Summary</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">{{ $order->currency }} {{ number_format($order->subtotal_price ?? 0, 2) }}</span>
                        </div>
                        
                        @if($order->total_shipping_price)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium">{{ $order->currency }} {{ number_format($order->total_shipping_price, 2) }}</span>
                            </div>
                        @endif
                        
                        @if($order->total_tax)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium">{{ $order->currency }} {{ number_format($order->total_tax, 2) }}</span>
                            </div>
                        @endif
                        
                        @if($order->total_discounts)
                            <div class="flex justify-between text-green-600">
                                <span>Discounts:</span>
                                <span class="font-medium">-{{ $order->currency }} {{ number_format($order->total_discounts, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between font-bold pt-3 border-t">
                            <span>Total:</span>
                            <span class="text-red-600">{{ $order->currency }} {{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                {{-- Order Timeline --}}
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-3">Order Timeline</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Created:</span>
                            <span class="font-medium">{{ $order->shopify_created_at ? $order->shopify_created_at->format('M d, Y h:i A') : 'N/A' }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Updated:</span>
                            <span class="font-medium">{{ $order->shopify_updated_at ? $order->shopify_updated_at->format('M d, Y h:i A') : 'N/A' }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Synced:</span>
                            <span class="font-medium">{{ $order->synced_at ? $order->synced_at->format('M d, Y h:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Line Items --}}
            @php
                // Use the accessor method
               $lineItems = is_string($order->line_items) ? json_decode($order->line_items, true) : $order->line_items;
              
            @endphp
            
            @if(count($lineItems) > 0)
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-3">Order Items</h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($lineItems as $item)
                                    @php
                                        $item = is_array($item) ? $item : [];
                                        $itemPrice = (float) ($item['price'] ?? 0);
                                        $itemQuantity = (int) ($item['quantity'] ?? 0);
                                        $itemTotal = $itemPrice * $itemQuantity;
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                @if(!empty($item['image']) && is_array($item['image']) && !empty($item['image']['src']))
                                                    <img src="{{ $item['image']['src'] }}" 
                                                         alt="{{ $item['name'] ?? 'Product' }}" 
                                                         class="w-10 h-10 rounded mr-3 object-cover">
                                                @endif
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $item['name'] ?? 'Unknown Item' }}</p>
                                                    @if(!empty($item['sku']))
                                                        <p class="text-xs text-gray-500">SKU: {{ $item['sku'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $order->currency }} {{ number_format($itemPrice, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $itemQuantity }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $order->currency }} {{ number_format($itemTotal, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="mb-6 text-gray-500 italic">
                    No line items available
                </div>
            @endif
            
            {{-- Addresses --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $billingAddress = $order->billing_address;
                @endphp
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="bi bi-credit-card"></i> Billing Address
                    </h4>
                    
                    @if(is_array($billingAddress) && !empty($billingAddress))
                        <div class="space-y-1 text-sm">
                            <p class="font-medium">{{ $billingAddress['first_name'] ?? '' }} {{ $billingAddress['last_name'] ?? '' }}</p>
                            <p>{{ $billingAddress['address1'] ?? '' }}</p>
                            @if(!empty($billingAddress['address2']))
                                <p>{{ $billingAddress['address2'] }}</p>
                            @endif
                            <p>{{ $billingAddress['city'] ?? '' }}{{ !empty($billingAddress['province']) ? ', ' . $billingAddress['province'] : '' }} {{ $billingAddress['zip'] ?? '' }}</p>
                            <p>{{ $billingAddress['country'] ?? '' }}</p>
                            @if(!empty($billingAddress['phone']))
                                <p class="pt-2 text-gray-600">
                                    <i class="bi bi-telephone mr-1"></i>{{ $billingAddress['phone'] }}
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 italic">No billing information available</p>
                    @endif
                </div>
                
                {{-- Shipping Address --}}
                @php
                    $shippingAddress = $order->shipping_address; // Uses the accessor
                @endphp
                
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <i class="bi bi-truck"></i> Shipping Address
                    </h4>
                    
                    @if(is_array($shippingAddress) && !empty($shippingAddress))
                        <div class="space-y-1 text-sm">
                            <p class="font-medium">{{ $shippingAddress['first_name'] ?? '' }} {{ $shippingAddress['last_name'] ?? '' }}</p>
                            <p>{{ $shippingAddress['address1'] ?? '' }}</p>
                            @if(!empty($shippingAddress['address2']))
                                <p>{{ $shippingAddress['address2'] }}</p>
                            @endif
                            <p>{{ $shippingAddress['city'] ?? '' }}{{ !empty($shippingAddress['province']) ? ', ' . $shippingAddress['province'] : '' }} {{ $shippingAddress['zip'] ?? '' }}</p>
                            <p>{{ $shippingAddress['country'] ?? '' }}</p>
                            @if(!empty($shippingAddress['phone']))
                                <p class="pt-2 text-gray-600">
                                    <i class="bi bi-telephone mr-1"></i>{{ $shippingAddress['phone'] }}
                                </p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 italic">No shipping information available</p>
                    @endif
                </div>
            </div>
            
        </div>

    </div>
@endforeach

{{-- Pagination --}}
@if($orders->hasPages())
    <div class="py-10 px-6">
        <div class="flex justify-center">
            {{ $orders->links() }}
        </div>
    </div>
@endif



</div>
@endsection