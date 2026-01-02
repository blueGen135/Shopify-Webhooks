{{-- resources/views/customers/show.blade.php --}}
@extends('layouts.app')

@section('content')
@php
                                    // Decode addresses if needed
$defaultAddress = is_array($customer->default_address) ? $customer->default_address : (is_string($customer->default_address) ? json_decode($customer->default_address, true) : []);
$addresses = is_array($customer->addresses) ? $customer->addresses : (is_string($customer->addresses) ? json_decode($customer->addresses, true) : []);
@endphp
<div class="w-full">
    <a href="{{ route('shopify.customers') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900"><i class="bi bi-arrow-left mr-2"></i>Back to Customers</a>
    @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif
    <div class="bg-white rounded-xl shadow p-6 mb-6 mt-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-bold text-2xl">
                        {{ strtoupper(substr($customer->first_name ?? 'C', 0, 1)) }}{{ strtoupper(substr($customer->last_name ?? '', 0, 1)) }}
                    </span>
                </div>
                <div class="ml-4">
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $customer->first_name ?? 'No Name' }} {{ $customer->last_name ?? '' }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        <span class="text-gray-600">
                            <i class="bi bi-envelope mr-1"></i>{{ $customer->email ?? 'No email' }}
                        </span>
                        @if($customer->phone)
                            <span class="text-gray-600">
                                <i class="bi bi-telephone mr-1"></i>{{ $customer->phone }}
                            </span>
                        @endif
                        <span class="text-gray-600">
                            <i class="bi bi-shop mr-1"></i>ID: {{ $customer->shopify_customer_id }}
                        </span>
                    </div>
                </div>
            </div>
              
        </div>
        <h3 class="text-lg font-medium text-gray-900 py-4">Customer Information</h3>
        <div class="space-y-3">
            <div>
                <label class="text-sm font-medium text-gray-500">Customer ID</label>
                <p class="mt-1 text-gray-900">{{ $customer->shopify_customer_id }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Email</label>
                <p class="mt-1 text-gray-900">{{ $customer->email ?? 'Not provided' }}</p>
            </div>
            @if($customer->phone)
                <div>
                    <label class="text-sm font-medium text-gray-500">Phone</label>
                    <p class="mt-1 text-gray-900">{{ $customer->phone }}</p>
                </div>
            @endif
            <div>
                <label class="text-sm font-medium text-gray-500">Account Status</label>
                <p class="mt-1">
                    <span class="px-2 py-1 text-xs rounded-full {{ $customer->state == 'enabled' ? 'bg-green-100 text-green-800' : ($customer->state == 'disabled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')
                    }}">{{ ucfirst($customer->state) }}</span>
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Email Verified</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs rounded-full {{ 
                            $customer->verified_email ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                        }}">
                            {{ $customer->verified_email ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Accepts Marketing</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 text-xs rounded-full {{ $customer->accepts_marketing ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}}">
                            {{ $customer->accepts_marketing ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
            </div>
            @if($customer->note)
                <div>
                    <label class="text-sm font-medium text-gray-500">Note</label>
                    <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded-lg">
                        {{ $customer->note }}
                    </p>
                </div>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow p-6 mt-4 mb-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center justify-between">
                <span>Default Address</span>
                @if(!empty($defaultAddress))
                    <span class="text-xs font-normal text-green-600">
                        <i class="bi bi-check-circle mr-1"></i>Set
                    </span>
                @endif
            </h3>

                    
            @if(!empty($defaultAddress) && is_array($defaultAddress))
                <div class="space-y-2">
                    <p class="font-medium text-gray-900">
                        {{ $defaultAddress['first_name'] ?? '' }} {{ $defaultAddress['last_name'] ?? '' }}
                    </p>
                    <p class="text-gray-700">{{ $defaultAddress['address1'] ?? '' }}</p>
                    @if(!empty($defaultAddress['address2']))
                        <p class="text-gray-700">{{ $defaultAddress['address2'] }}</p>
                    @endif
                    <p class="text-gray-700">
                        {{ $defaultAddress['city'] ?? '' }}{{ !empty($defaultAddress['province']) ? ', ' . $defaultAddress['province'] : '' }} 
                        {{ $defaultAddress['zip'] ?? '' }}
                    </p>
                    <p class="text-gray-700">{{ $defaultAddress['country'] ?? '' }}</p>
                    @if(!empty($defaultAddress['phone']))
                        <p class="text-gray-700">
                            <i class="bi bi-telephone mr-1"></i>{{ $defaultAddress['phone'] }}
                        </p>
                    @endif
                </div>
            @else
                <p class="text-gray-500 italic text-center py-4">No default address available</p>
            @endif           
        </div>
       
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Order History</h3>
                <span class="text-sm text-gray-600">{{ $customer->orders->count() }} orders</span>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($orders as $order)
                <div class="px-6 py-4 hover:bg-gray-50 transition" x-data="{ showOrderDetails: false }">
                    
                    <div class="flex items-center justify-between">
                                    <div>
                                        
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ 
                                                $order->financial_status == 'paid' ? 'bg-green-100 text-green-800' : 
                                                ($order->financial_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                'bg-red-100 text-red-800')
                                            }}">
                                                {{ ucfirst($order->financial_status) }}
                                            </span>
                                            
                                            <span class="px-2 py-1 text-xs rounded-full {{ 
                                                $order->fulfillment_status == 'fulfilled' ? 'bg-blue-100 text-blue-800' : 
                                                ($order->fulfillment_status == 'partial' ? 'bg-orange-100 text-orange-800' : 
                                                'bg-gray-100 text-gray-800')
                                            }}">
                                                {{ ucfirst($order->fulfillment_status ?? 'unfulfilled') }}
                                            </span>
                                            
                                            <span class="text-sm font-bold text-gray-900">
                                                {{ $order->currency }} {{ number_format($order->total_price, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-3">
                                        {{-- Fulfill Button
                                        @if($order->fulfillment_status != 'fulfilled')
                                            <form action="{{ route('orders.fulfill', $order->id) }}" method="POST" 
                                                  onsubmit="return confirm('Mark order #{{ $order->order_number }} as fulfilled?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition">
                                                    Fulfill
                                                </button>
                                            </form>
                                        @endif --}}
                                        
                                        {{-- Toggle Details Button --}}
                                        <button @click="showOrderDetails = !showOrderDetails" 
                                                class="text-gray-500 hover:text-gray-700">
                                            <i class="bi bi-chevron-down" x-show="!showOrderDetails"></i>
                                            <i class="bi bi-chevron-up" x-show="showOrderDetails"></i>
                                        </button>
                                    </div>
                    </div>
                      
                    <div x-show="showOrderDetails" x-collapse class="mt-4 pt-4 border-t border-gray-100">
                                    
                                    {{-- Line Items --}}
                                    @php
                                        $lineItems = is_array($order->line_items) ? $order->line_items : 
                                                    (is_string($order->line_items) ? json_decode($order->line_items, true) : []);
                                    @endphp
                                    
                                    @if(count($lineItems) > 0)
                                        <div class="mb-4">
                                            <h4 class="font-medium text-gray-700 mb-2">Items</h4>
                                            <div class="space-y-2">
                                                @foreach($lineItems as $item)
                                                    @php
                                                        $item = is_array($item) ? $item : [];
                                                    @endphp
                                                    <div class="flex items-center justify-between text-sm">
                                                        <div class="flex items-center">
                                                            <span class="text-gray-600">
                                                                {{ $item['name'] ?? 'Unknown Item' }} 
                                                                × {{ $item['quantity'] ?? 0 }}
                                                            </span>
                                                            @if(!empty($item['sku']))
                                                                <span class="ml-2 text-xs text-gray-500">
                                                                    SKU: {{ $item['sku'] }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <span class="font-medium">
                                                            {{ $order->currency }} {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 2) }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    {{-- Order Summary --}}
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <h4 class="font-medium text-gray-700 mb-2">Billing</h4>
                                            @php
                                                $billingAddress = is_array($order->billing_address) ? $order->billing_address : 
                                                                (is_string($order->billing_address) ? json_decode($order->billing_address, true) : []);
                                            @endphp
                                            @if(!empty($billingAddress))
                                                <p class="text-gray-600">{{ $billingAddress['city'] ?? '' }}, {{ $billingAddress['country'] ?? '' }}</p>
                                            @else
                                                <p class="text-gray-500 italic">No billing info</p>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-700 mb-2">Shipping</h4>
                                            @php
                                                $shippingAddress = is_array($order->shipping_address) ? $order->shipping_address : 
                                                                 (is_string($order->shipping_address) ? json_decode($order->shipping_address, true) : []);
                                            @endphp
                                            @if(!empty($shippingAddress))
                                                <p class="text-gray-600">{{ $shippingAddress['city'] ?? '' }}, {{ $shippingAddress['country'] ?? '' }}</p>
                                            @else
                                                <p class="text-gray-500 italic">No shipping info</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- Order Actions --}}
                                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between">
                                        <div class="text-xs text-gray-500">
                                            Created: {{ $order->shopify_created_at->format('M d, Y h:i A') }}
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('orders.edit', $order->id) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                               Edit Order
                                            </a>

                                        </div>
                                    </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="bi bi-cart text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">No Orders Found</h4>
                    <p class="text-gray-600 max-w-md mx-auto">
                        This customer hasn't placed any orders yet.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
