@extends('layouts.app')
@section('content')
<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-gray-200 w-full p-2">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif
     
    <div  class="bg-white rounded-xl shadow p-4 mb-5 cursor-pointer border border-gray-100 hover:shadow-lg transition">
        
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
                    @if ($order->fulfillment_status == 'restocked')
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelled</span>
                    @endif
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
                @if($order->fulfillment_status != 'fulfilled' && !$order->fulfillment_status == 'restocked')
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
                
                
            </div>
        </div>

        {{-- Expandable Details Section --}}
        <div class="mt-4 pt-4 border-t border-gray-200">
            
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
                $lineItems = is_string($order->line_items) ? json_decode($order->line_items, true) : $order->line_items;
            @endphp
            
            @if(is_array($lineItems) && count($lineItems) > 0)
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
                                        // Safely access array elements
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
                {{-- Billing Address --}}
                @php
                    $billingAddress = $order->billing_address; // Uses the accessor
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
        @if ($order->fulfillment_status != 'restocked')
            
        <div class="mt-4 flex justify-between gap-3" x-data="{ showForm: false }">
            <form action="{{ route('orders.duplicate', $order->id) }}" method="POST" >
                @csrf
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm cursor-pointer">Duplicate Order</button>
            </form>
            @if(!$order->cancelled_at)
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                @csrf
                <button class="bg-red-600 text-white px-4 py-2 rounded text-sm cursor-pointer">
                    Cancel Order
                </button>
            </form>
            @endif
            
            {{-- In your orders/show.blade.php --}}
            @if($order->financial_status === 'paid' && ($order->total_refunded ?? 0) < $order->total_price)
              
                    <a href="{{ route('orders.refund.confirm', $order->id) }}" 
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        <i class="bi bi-arrow-clockwise mr-2"></i>
                        Refund Order
                    </a>
                
             @elseif($order->financial_status === 'refunded')
                <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded">
                    <i class="bi bi-check-circle mr-2"></i>
                    Fully Refunded
                </span>
            @endif
             @if($order->financial_status === 'paid' && ($order->total_refunded ?? 0) < $order->total_price)
            
                <button class="bg-orange-600 text-white px-4 py-2 rounded text-sm cursor-pointer" @click="showForm = true">
                    Partial Refund
                </button>
            @endif    
               
            
            <div class="bg-black/10 fixed inset-0 z-10 flex items-center justify-center" x-show="showForm" x-transition>
                <div class="max-w-lg w-full bg-white rounded-lg shadow-lg p-6 relative" >
                    <button type="button" x-on:click="showForm = false" class="absolute -top-4 -right-4 cursor-pointer w-10 h-10 flex items-center justify-center bg-red-100 rounded-full hover:bg-red-200">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <form action="{{ route('orders.refund.partial', $order->id) }}"
                        method="POST"
                        x-data="refundForm()"
                        class="border rounded-lg p-4 mt-4">

                        @csrf

                        <h3 class="font-semibold mb-3">Partial Refund</h3>

                       
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="flex items-center justify-between border-b py-2">
                                <div>
                                    <p class="font-medium" x-text="item.name"></p>
                                    <p class="text-sm text-gray-500">
                                        Price: ₹<span x-text="item.price"></span> |
                                        Ordered: <span x-text="item.maxQty"></span>
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="number"
                                        min="0"
                                        :max="item.maxQty"
                                        x-model.number="item.refundQty"
                                        class="w-20 border rounded p-1 text-sm">

                                    <input type="hidden"
                                        :name="`items[${index}][line_item_id]`"
                                        :value="item.id">

                                    <input type="hidden"
                                        :name="`items[${index}][quantity]`"
                                        :value="item.refundQty">
                                </div>
                            </div>
                        </template>

                       
                            <div class="space-y-3 mt-4"
                                x-data="{
                                    includeShipping: false,
                                    includeTax: false,
                                    shippingAmount: {{ $shippingRefundable }},
                                    taxAmount: {{ $taxRefundable }}
                                }">

                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="flex items-center text-gray-700 cursor-pointer">
                                            <input type="checkbox"
                                                class="mr-2 h-4 w-4 text-primary-600 border-gray-300 rounded"
                                                x-model="includeShipping">
                                            Include Shipping
                                        </label>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Refund shipping charges
                                        </p>
                                    </div>

                                    <span class="font-medium">
                                        ₹<span x-text="includeShipping ? shippingAmount.toFixed(2) : '0.00'"></span>
                                    </span>

                                    <input type="hidden"
                                        name="shipping_amount"
                                        :value="includeShipping ? shippingAmount : 0">
                                </div>

    
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="flex items-center text-gray-700 cursor-pointer">
                                            <input type="checkbox"
                                                class="mr-2 h-4 w-4 text-primary-600 border-gray-300 rounded"
                                                x-model="includeTax">
                                            Include Tax
                                        </label>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Refund applicable taxes
                                        </p>
                                    </div>

                                    <span class="font-medium">
                                        ₹<span x-text="includeTax ? taxAmount.toFixed(2) : '0.00'"></span>
                                    </span>

                                    <input type="hidden"
                                        name="tax_adjustment"
                                        :value="includeTax ? taxAmount : 0">
                                </div>

                                </div>


                        {{-- TOTAL --}}
                        <div class="mt-4 flex justify-between items-center">
                            <div class="font-semibold">
                                Total Refund: ₹<span x-text="totalRefund.toFixed(2)"></span>
                            </div>

                            <button type="submit"
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded"
                                    :disabled="totalRefund <= 0"
                                    :class="{ 'opacity-50 cursor-not-allowed': totalRefund <= 0 }">
                                Process Refund
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        @endif
    </div>
 
  <script>
function refundForm() {
    return {
        items: @json($refundItems),

        refundShipping: false,
        shippingAmount: 0,

        taxAdjustment: 0,

        get totalRefund() {
            const itemsTotal = this.items.reduce(
                (sum, item) => sum + (item.refundQty * item.price),
                0
            );

            const shipping = this.refundShipping
                ? this.shippingAmount
                : 0;

            const tax = this.taxAdjustment || 0;

            return itemsTotal + shipping + tax;
        }
    }
}
</script>


</div>
@endsection