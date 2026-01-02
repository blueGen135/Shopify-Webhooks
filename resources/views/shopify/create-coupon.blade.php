<!-- resources/views/coupons/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-2xl font-bold mb-6">Create Shopify Coupon</h1>
    
    <div id="alert-container"></div>
    
    <form id="create-coupon-form" action="{{ route('shopify.store-coupon') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Title -->
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-bold mb-2">Coupon Title *</label>
                <input type="text" name="title" id="title" class="form-input w-full rounded-md shadow-sm p-3" required>
                <small class="text-gray-500">Internal name for the coupon</small>
            </div>

            <!-- Code -->
            <div class="mb-4">
                <label for="code" class="block text-gray-700 font-bold mb-2">Discount Code *</label>
                <input type="text" name="code" id="code" class="form-input w-full rounded-md shadow-sm p-3" required>
                <small class="text-gray-500">Customers will use this code at checkout</small>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Value -->
            <div class="mb-4">
                <label for="value" class="block text-gray-700 font-bold mb-2">Discount Value *</label>
                <input type="number" name="value" id="value" class="form-input w-full rounded-md shadow-sm p-3" step="0.01" required>
            </div>

            <!-- Value Type -->
            <div class="mb-4">
                <label for="value_type" class="block text-gray-700 font-bold mb-2">Value Type *</label>
                <select name="value_type" id="value_type" class="form-select w-full rounded-md shadow-sm p-3" required>
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed_amount">Fixed Amount</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Start Date -->
            <div class="mb-4">
                <label for="starts_at" class="block text-gray-700 font-bold mb-2">Start Date & Time *</label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="form-input w-full rounded-md shadow-sm p-3" required>
            </div>

            <!-- End Date -->
            <div class="mb-4">
                <label for="ends_at" class="block text-gray-700 font-bold mb-2">End Date & Time</label>
                <input type="datetime-local" name="ends_at" id="ends_at" class="form-input w-full rounded-md shadow-sm p-3">
                <small class="text-gray-500">Optional - leave empty for no expiration</small>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Target Type -->
            <div>
                <label for="target_type" class="block text-gray-700 font-bold mb-2">Target Type *</label>
                <select name="target_type" id="target_type" class="form-select w-full rounded-md shadow-sm p-3" required>
                    <option value="line_item">Line Item</option>
                    <option value="shipping_line">Shipping Line</option>
                </select>
            </div>

            <!-- Target Selection -->
            <div>
                <label for="target_selection" class="block text-gray-700 font-bold mb-2">Target Selection *</label>
                <select name="target_selection" id="target_selection" class="form-select w-full rounded-md shadow-sm p-3" required>
                    <option value="all">All Items</option>
                    <option value="entitled">Specific Items</option>
                </select>
            </div>

            <!-- Allocation Method -->
            <div>
                <label for="allocation_method" class="block text-gray-700 font-bold mb-2">Allocation Method *</label>
                <select name="allocation_method" id="allocation_method" class="form-select w-full rounded-md shadow-sm p-3" required>
                    <option value="across">Apply across all items</option>
                    <option value="each">Apply to each item</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Customer Selection -->
            <div class="mb-4">
                <label for="customer_selection" class="block text-gray-700 font-bold mb-2">Customer Selection *</label>
                <select name="customer_selection" id="customer_selection" class="form-select w-full rounded-md shadow-sm p-3" required>
                    <option value="all">All Customers</option>
                    <option value="prerequisite">Specific Customers</option>
                </select>
            </div>

            <!-- Usage Limit -->
            <div class="mb-4">
                <label for="usage_limit" class="block text-gray-700 font-bold mb-2">Usage Limit</label>
                <input type="number" name="usage_limit" id="usage_limit" class="form-input w-full rounded-md shadow-sm p-3" min="1">
                <small class="text-gray-500">Total number of times coupon can be used</small>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Once Per Customer -->
            <div>
                <label class="flex items-center p-3 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="once_per_customer" id="once_per_customer" class="mr-3 h-5 w-5" value="1">
                    <div>
                        <span class="text-gray-700 font-bold">Once per customer</span>
                        <p class="text-gray-500 text-sm mt-1">Limit to one use per customer</p>
                    </div>
                </label>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-gray-700 font-bold mb-2">Status *</label>
                <select name="status" id="status" class="form-select w-full rounded-md shadow-sm p-3" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end space-x-3 pt-6 border-t">
            <a href="{{ route('shopify.all-coupons') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-md font-medium">
                Cancel
            </a>
            <button type="submit" id="submit-btn" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-md font-medium">
                Create Coupon
            </button>
            
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        // tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('starts_at').value = today.toISOString().slice(0, 16);
        
    });
</script>
@endsection