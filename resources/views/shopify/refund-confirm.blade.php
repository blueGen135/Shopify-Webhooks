
@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Confirm Refund</h3>
            </div>
            
            <div class="px-6 py-6">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                You are about to refund Order #{{ $order->order_number }}. 
                                This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-3">Order Details</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Order Number:</span>
                                <p class="font-medium">{{ $order->order_number }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Customer:</span>
                                <p class="font-medium">{{ $order->customer_name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Order Total:</span>
                                <p class="font-medium">{{ $order->currency }} {{ number_format($order->total_price, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Already Refunded:</span>
                                <p class="font-medium">{{ $order->currency }} {{ number_format($order->total_refunded ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-medium text-red-800 mb-2">Refund Amount</h4>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $order->currency }} {{ number_format($refundableAmount, 2) }}
                        </p>
                        <p class="text-sm text-red-600 mt-1">
                            Full refund of remaining amount
                        </p>
                    </div>
                    
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">What happens next?</h4>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li><i class="bi bi-check-circle text-green-500 mr-2"></i>Customer will receive email notification</li>
                            <li><i class="bi bi-check-circle text-green-500 mr-2"></i>Payment will be refunded to original payment method</li>
                            <li><i class="bi bi-check-circle text-green-500 mr-2"></i>Order status will be updated to "Refunded"</li>
                            @if($order->fulfillment_status !== 'fulfilled')
                                <li><i class="bi bi-check-circle text-green-500 mr-2"></i>Inventory will be restocked</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between">
                <a href="{{ url()->previous() }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
                
                <form action="{{ route('orders.refund.process', $order->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="confirm" value="1">
                    <button type="submit" 
                            onclick="return confirm('Are you absolutely sure you want to refund this order?')"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                        <i class="bi bi-arrow-clockwise"></i>
                        Confirm Refund
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection