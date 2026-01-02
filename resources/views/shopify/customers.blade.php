@extends('layouts.app')
@section('content')
    <div class=" overflow-hidden w-full">
         @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif
        <div class="mb-6">
            @foreach($customers as $customer)
                @php
                    $defaultAddress = is_array($customer->default_address) ? $customer->default_address : (is_string($customer->default_address) ? json_decode($customer->default_address, true) : []);
                    $addresses = is_array($customer->addresses) ? $customer->addresses : (is_string($customer->addresses) ? json_decode($customer->addresses, true) : []);
                @endphp
            <div x-data="{ open: false, editing: false }" class="mb-4">                    
                <div class="flex items-center gap-10 p-4 transition bg-white rounded-xl shadow mb-4 justify-between">
                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-blue-600 font-bold">{{ strtoupper(substr($customer->first_name ?? 'C', 0, 1)) }}{{ strtoupper(substr($customer->last_name ?? '', 0, 1)) }}</span>
                    </div>
                    <div class="">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $customer->first_name ?? 'No Name' }} {{ $customer->last_name ?? '' }}
                        </div>
                        <div class="text-xs text-gray-500">
                            ID: {{ $customer->shopify_customer_id }}
                        </div>
                    </div>
                    <div class="text-sm text-gray-900">
                        <div class="flex items-center gap-1 mb-1">
                            <i class="bi bi-envelope text-gray-400"></i>
                            {{ $customer->email ?? 'No email' }}
                        </div>
                        @if($customer->phone)
                            <div class="flex items-center gap-1">
                                <i class="bi bi-telephone text-gray-400"></i>
                                {{ $customer->phone }}
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-4">
                        <button type="button" x-on:click="open = !open" class="w-10 h-10 justify-center flex p-2 text-sm items-center bg-gray-200 rounded-lg cursor-pointer text-lg"><i class="bi" :class="open ? 'bi-eye' : 'bi-eye-slash'"></i></button>
                        <button type="button" x-on:click="editing = !editing" class="w-10 h-10 justify-center text-lg flex p-2 text-sm items-center bg-green-400 rounded-lg cursor-pointer"><i class="bi bi-pencil-fill"></i></button>
                        {{-- <a href="{{ route('customers.show', $customer->id) }}" class="flex p-2 text-sm items-center bg-gray-200 rounded-lg">View Details</a> --}}
                        <a href="{{ route('orders.createForCustomer', $customer->id) }}" class="flex p-2 text-sm items-center bg-gray-300 rounded-lg">Create Order</a>
                        <form action="{{ route('customers.delete', $customer->id) }}"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to disable this customer?');">
                            @csrf

                            <button type="submit"class="w-10 h-10 justify-center text-lg flex p-2 text-sm items-center bg-red-400 rounded-lg cursor-pointer">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>

                    </div>
                </div>
                <div class="bg-black/10 fixed inset-0 z-10 flex items-center justify-center" x-show="editing" x-transition>
                    <div class="max-w-lg w-full bg-white rounded-lg shadow-lg p-6 relative" >
                        <button type="button" x-on:click="editing = false" class="absolute -top-4 -right-4 cursor-pointer w-10 h-10 flex items-center justify-center bg-red-100 rounded-full hover:bg-red-200">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                            @csrf
                            <div class="flex gap-4 justify-between">
                                <div>
                                    <label for="fname" class="flex">First Name</label>
                                    <input type="text" id="fname" value="{{ $customer->first_name }}" name="first_name" class="p-2 rounded-lg mb-4 text-sm border border-gray-300 focus:ouline-none w-full"/>
                                </div>
                                <div>
                                    <label for="lname" class="flex">Last Name</label>
                                    <input type="text" value="{{ $customer->last_name }}" name="last_name" class="p-2 rounded-lg mb-4 text-sm border border-gray-300 focus:ouline-none w-full" />
                                </div>
                            </div>
                            <div class="flex gap-4 justify-between">
                                <div>
                                    <label for="email" class="flex">Email</label>
                                    <input type="email" id="email" value="{{ $customer->email }}" name="email" class="p-2 rounded-lg mb-4 text-sm border border-gray-300 focus:ouline-none"/>
                                </div>
                                <div>
                                    <label for="" class="flex">Phone</label>
                                    <input type="text" value="{{ $customer->phone }}" name="phone" class="p-2 rounded-lg mb-4 text-sm border border-gray-300 focus:ouline-none"/>
                                </div>
                            </div>
                            <button type="submit"class="bg-slate-800 text-white px-4 py-2 rounded cursor-pointer">Update</button>
                        </form>
                    </div>
                </div>
                <div x-show="open" x-transition class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-medium text-gray-900 py-4">Customer Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between gap-6">
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
                                <label class="text-sm font-medium text-gray-500">State</label>
                                <p class="mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $customer->state ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}}">
                                        {{ $customer->state ? 'Yes' : 'No' }}
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
                    @php
                        $orders = App\Models\Order::where('customer_email', $customer->email)
                                ->orderBy('shopify_created_at', 'desc')
                                ->get(); 
                    @endphp 
                    
                    <h3 class="text-lg font-medium text-gray-900 py-4">Order History</h3>
                    <div class=" max-h-96 overflow-y-auto">
                        @forelse($orders as $order)
                        <div class="bg-white rounded-xl shadow p-4 mb-4">
                            <div class="flex items-center gap-3 mb-2 justify-between">
                                <div>
                                    <p class="text-lg font-bold text-gray-900">
                                        #{{ $order->order_number }}
                                    </p>
                                    <span class="text-sm text-gray-600">
                                        {{ $order->shopify_created_at ? $order->shopify_created_at->format('M d') : 'N/A' }}
                                    </span>
                                </div>
                                <a href="{{ route('shopify.view-order', $order->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">View Order</a>
                                

                            </div>
                            <div class="flex items-center justify-between">
                            <span class="px-2 py-1 text-xs rounded-full {{ $order->financial_status == 'paid' ? 'bg-green-100 text-green-800' :($order->financial_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')}}">{{ ucfirst($order->financial_status) }}</span>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $order->fulfillment_status == 'fulfilled' ? 'bg-blue-100 text-blue-800' :($order->fulfillment_status == 'partial' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')}}">{{ ucfirst($order->fulfillment_status ?? 'unfulfilled') }}</span>
                                        <span class="text-sm font-bold text-gray-900">{{ $order->currency }} {{ number_format($order->total_price, 2) }}</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 border-t border-gray-200 mt-4">
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
                            @php
                                $lineItems = is_string($order->line_items) ? json_decode($order->line_items, true) : $order->line_items;
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
                                                        {{ $item['name'] ?? 'Unknown Item' }}  × {{ $item['quantity'] ?? 0 }}
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
            @endforeach
        </div>
           
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $customers->links() }}
        </div>
           
    </div>
  

@endsection