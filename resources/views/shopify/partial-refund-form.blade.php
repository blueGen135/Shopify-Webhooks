@extends('layouts.app')
@section('content')
<div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-gray-200 w-full p-2">
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                        <i class="fas fa-undo-alt mr-3 text-primary-600"></i>
                        Partial Refund
                    </h1>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                            <i class="fas fa-hashtag mr-1"></i>
                            Order #{{ $order->order_number }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            @if($order->financial_status == 'paid') bg-green-100 text-green-800
                            @elseif($order->financial_status == 'partially_refunded') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            <i class="fas fa-money-bill-wave mr-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $order->financial_status)) }}
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    
                    <button id="submitRefundBtn"
                            disabled
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-paper-plane mr-3"></i>
                        <span id="submitText">Process Refund</span>
                        <span id="loadingSpinner" class="hidden ml-2">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Refund Progress</span>
                    <span id="progressPercentage" class="text-sm font-bold text-primary-600">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-primary-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-boxes mr-2"></i>
                            Order Items
                            <span class="text-gray-500 text-sm font-normal ml-2">({{ count($fulfillmentItems) }} items)</span>
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <form id="refundForm" action="" method="POST">
                            @csrf
                            
                            <!-- Search and Filter -->
                            <div class="mb-6">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-search text-gray-400"></i>
                                            </div>
                                            <input type="text" 
                                                   id="searchItems" 
                                                   placeholder="Search items..." 
                                                   class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="button" 
                                                id="selectAllBtn"
                                                class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                                            Select All
                                        </button>
                                        <button type="button" 
                                                id="clearAllBtn"
                                                class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                                            Clear All
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items List -->
                            <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 scrollbar-hide" id="itemsContainer">
                                @foreach($fulfillmentItems as $index => $item)
                                    <div class="item-card border border-gray-200 rounded-xl p-4 hover:border-primary-300 transition-all duration-200"
                                         data-item-id="{{ $item['id'] }}"
                                         data-item-name="{{ strtolower($item['name']) }}">
                                        <div class="flex items-start justify-between">
                                            <!-- Item Info -->
                                            <div class="flex-1">
                                                <div class="flex items-start space-x-4">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                                            <i class="fas fa-box text-gray-400 text-xl"></i>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex-1 min-w-0">
                                                        <h3 class="text-lg font-medium text-gray-900 truncate">
                                                            {{ $item['name'] }}
                                                        </h3>
                                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                <i class="fas fa-tag mr-1"></i>
                                                                {{ $item['id'] }}
                                                            </span>
                                                            <span class="text-sm text-gray-500">
                                                                Unit Price: <span class="font-semibold">₹{{ number_format($item['price'], 2) }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Quantity Controls -->
                                                <div class="mt-4 pl-20">
                                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                        <div>
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                Quantity to Refund
                                                            </label>
                                                            <div class="flex items-center space-x-3">
                                                                <button type="button"
                                                                        class="qty-decrease w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                                        data-item-index="{{ $index }}"
                                                                        {{ $item['maxQty'] == 0 ? 'disabled' : '' }}>
                                                                    <i class="fas fa-minus text-gray-600"></i>
                                                                </button>
                                                                
                                                                <div class="relative">
                                                                    <input type="number"
                                                                           name="items[{{ $item['id'] }}]"
                                                                           id="qty_{{ $index }}"
                                                                           value="0"
                                                                           min="0"
                                                                           max="{{ $item['maxQty'] }}"
                                                                           data-price="{{ $item['price'] }}"
                                                                           data-max="{{ $item['maxQty'] }}"
                                                                           data-index="{{ $index }}"
                                                                           class="qty-input w-24 px-4 py-2 border border-gray-300 rounded-lg text-center text-lg font-semibold focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                                                    <div class="absolute -bottom-6 left-0 right-0 text-xs text-gray-500 text-center">
                                                                        Max: {{ $item['maxQty'] }}
                                                                    </div>
                                                                </div>
                                                                
                                                                <button type="button"
                                                                        class="qty-increase w-10 h-10 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                                        data-item-index="{{ $index }}"
                                                                        {{ $item['maxQty'] == 0 ? 'disabled' : '' }}>
                                                                    <i class="fas fa-plus text-gray-600"></i>
                                                                </button>
                                                                
                                                                <div class="text-sm text-gray-600 ml-4">
                                                                    <span id="itemTotal_{{ $index }}" class="font-bold text-gray-900">
                                                                        ₹0.00
                                                                    </span>
                                                                    <span class="text-gray-500 ml-2">
                                                                        ({{ $item['maxQty'] - 0 }} remaining)
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="flex items-center">
                                                            <div class="relative inline-block w-10 mr-2 align-middle select-none">
                                                                <input type="checkbox"
                                                                       id="toggle_{{ $index }}"
                                                                       name="toggle[{{ $item['id'] }}]"
                                                                       class="item-toggle sr-only"
                                                                       data-item-index="{{ $index }}">
                                                                <label for="toggle_{{ $index }}"
                                                                       class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer">
                                                                </label>
                                                            </div>
                                                            <label for="toggle_{{ $index }}" class="text-sm text-gray-700 cursor-pointer">
                                                                Include in refund
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Empty State -->
                            <div id="emptyState" class="hidden text-center py-12">
                                <div class="mx-auto w-24 h-24 text-gray-400 mb-4">
                                    <i class="fas fa-search text-6xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No items found</h3>
                                <p class="text-gray-500">Try adjusting your search or filter</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Refund Summary -->
            <div class="lg:col-span-1">
                <div class="space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-6">
                            <i class="fas fa-calculator mr-2"></i>
                            Refund Summary
                        </h2>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Items Selected</span>
                                <span id="selectedCount" class="text-lg font-bold text-gray-900">0</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Quantity</span>
                                <span id="totalQuantity" class="text-lg font-bold text-gray-900">0</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Items Subtotal</span>
                                <span id="itemsSubtotal" class="text-lg font-bold text-gray-900">₹0.00</span>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Shipping Refund -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="flex items-center text-gray-700 cursor-pointer">
                                            <input type="checkbox" 
                                                   id="includeShipping" 
                                                   name="include_shipping"
                                                   class="mr-2 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                            Include Shipping
                                        </label>
                                        <p class="text-sm text-gray-500 mt-1">Refund shipping charges</p>
                                    </div>
                                    <span id="shippingAmount" class="font-medium">₹{{ number_format($order->total_shipping_price ?? 0, 2) }}</span>
                                </div>
                                
                                <!-- Tax Refund -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="flex items-center text-gray-700 cursor-pointer">
                                            <input type="checkbox" 
                                                   id="includeTax" 
                                                   name="include_tax"
                                                   class="mr-2 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                            Include Tax
                                        </label>
                                        <p class="text-sm text-gray-500 mt-1">Refund applicable taxes</p>
                                    </div>
                                    <span id="taxAmount" class="font-medium">₹{{ number_format($order->total_tax ?? 0, 2) }}</span>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <!-- Total Refund -->
                            <div class="bg-gray-50 -mx-6 -mb-6 mt-6 px-6 py-6 rounded-b-xl">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-lg font-semibold text-gray-800">Total Refund</span>
                                    <span id="totalRefund" class="text-2xl font-bold text-primary-600">₹0.00</span>
                                </div>
                                
                                <!-- Refund Reason -->
                                <div class="mt-4">
                                    <label for="refundReason" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-comment-alt mr-2"></i>
                                        Refund Reason (Optional)
                                    </label>
                                    <textarea id="refundReason" 
                                              name="refund_reason"
                                              rows="3"
                                              placeholder="Enter reason for refund..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Info Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-user mr-2"></i>
                            Customer Information
                        </h2>
                        
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <i class="fas fa-user-circle text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-700">
                                    {{ $order->customer_first_name }} {{ $order->customer_last_name }}
                                </span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 mr-3 w-5"></i>
                                <a href="mailto:{{ $order->customer_email }}" class="text-primary-600 hover:underline">
                                    {{ $order->customer_email }}
                                </a>
                            </div>
                            @if($order->customer_phone)
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-3 w-5"></i>
                                <a href="tel:{{ $order->customer_phone }}" class="text-primary-600 hover:underline">
                                    {{ $order->customer_phone }}
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-3">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Quick Actions
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" 
                                    id="refundHalfBtn"
                                    class="px-3 py-2 text-sm border border-yellow-300 rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                Refund 50%
                            </button>
                            <button type="button" 
                                    id="refundOneEachBtn"
                                    class="px-3 py-2 text-sm border border-yellow-300 rounded-lg bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                1 Each
                            </button>
                        </div>
                        <p class="text-sm text-yellow-700 mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            These actions apply to all selected items
                        </p>
                    </div>
                </div>
            </div>
        </div>
  

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Refund Successful!</h3>
                <p class="text-gray-600 mb-6" id="successMessage"></p>
                <div class="flex justify-center space-x-4">
                    <button id="closeModal" 
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let refundItems = @json($fulfillmentItems);
            let selectedItems = new Set();
            let totalRefundAmount = 0;
            let shippingAmount = parseFloat({{ $order->total_shipping_price ?? 0 }});
            let taxAmount = parseFloat({{ $order->total_tax ?? 0 }});
            
            // Initialize toggle labels
            document.querySelectorAll('.toggle-label').forEach(label => {
                label.innerHTML = '<span class="toggle-dot block w-6 h-6 rounded-full bg-white transform translate-x-0 transition-transform duration-300"></span>';
            });
            
            // Quantity controls
            document.querySelectorAll('.qty-increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = this.dataset.itemIndex;
                    const input = document.getElementById(`qty_${index}`);
                    const max = parseInt(input.dataset.max);
                    
                    if (parseInt(input.value) < max) {
                        input.value = parseInt(input.value) + 1;
                        updateItem(index, parseInt(input.value));
                        updateToggleState(index, true);
                    }
                });
            });
            
            document.querySelectorAll('.qty-decrease').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = this.dataset.itemIndex;
                    const input = document.getElementById(`qty_${index}`);
                    
                    if (parseInt(input.value) > 0) {
                        input.value = parseInt(input.value) - 1;
                        updateItem(index, parseInt(input.value));
                    }
                });
            });
            
            // Quantity input change
            document.querySelectorAll('.qty-input').forEach(input => {
                input.addEventListener('input', function() {
                    const index = this.dataset.index;
                    const max = parseInt(this.dataset.max);
                    let value = parseInt(this.value) || 0;
                    
                    if (value < 0) value = 0;
                    if (value > max) value = max;
                    
                    this.value = value;
                    updateItem(index, value);
                    updateToggleState(index, value > 0);
                });
            });
            
            // Toggle switches
            document.querySelectorAll('.item-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const index = this.dataset.itemIndex;
                    const input = document.getElementById(`qty_${index}`);
                    const max = parseInt(input.dataset.max);
                    
                    if (this.checked) {
                        input.value = max;
                        updateItem(index, max);
                    } else {
                        input.value = 0;
                        updateItem(index, 0);
                    }
                    updateUI();
                });
            });
            
            // Search functionality
            document.getElementById('searchItems').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const items = document.querySelectorAll('[data-item-name]');
                let visibleCount = 0;
                
                items.forEach(item => {
                    const itemName = item.dataset.itemName;
                    if (itemName.includes(searchTerm)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                // Show/hide empty state
                const emptyState = document.getElementById('emptyState');
                if (visibleCount === 0 && searchTerm) {
                    emptyState.classList.remove('hidden');
                } else {
                    emptyState.classList.add('hidden');
                }
            });
            
            // Quick actions
            document.getElementById('refundHalfBtn').addEventListener('click', function() {
                document.querySelectorAll('.qty-input').forEach(input => {
                    const max = parseInt(input.dataset.max);
                    input.value = Math.floor(max / 2);
                    const index = input.dataset.index;
                    updateItem(index, input.value);
                    updateToggleState(index, input.value > 0);
                });
                updateUI();
            });
            
            document.getElementById('refundOneEachBtn').addEventListener('click', function() {
                document.querySelectorAll('.qty-input').forEach(input => {
                    const max = parseInt(input.dataset.max);
                    input.value = max > 0 ? 1 : 0;
                    const index = input.dataset.index;
                    updateItem(index, input.value);
                    updateToggleState(index, input.value > 0);
                });
                updateUI();
            });
            
            // Select all / Clear all
            document.getElementById('selectAllBtn').addEventListener('click', function() {
                document.querySelectorAll('.qty-input').forEach(input => {
                    const max = parseInt(input.dataset.max);
                    if (max > 0) {
                        input.value = max;
                        const index = input.dataset.index;
                        updateItem(index, max);
                        updateToggleState(index, true);
                    }
                });
                updateUI();
            });
            
            document.getElementById('clearAllBtn').addEventListener('click', function() {
                document.querySelectorAll('.qty-input').forEach(input => {
                    input.value = 0;
                    const index = input.dataset.index;
                    updateItem(index, 0);
                    updateToggleState(index, false);
                });
                updateUI();
            });
            
            // Shipping and Tax checkboxes
            document.getElementById('includeShipping').addEventListener('change', updateUI);
            document.getElementById('includeTax').addEventListener('change', updateUI);
            
            // Update item function
            function updateItem(index, quantity) {
                const item = refundItems[index];
                const price = item.price;
                const maxQty = item.maxQty;
                
                // Update UI elements
                const itemCard = document.querySelector(`[data-item-index="${index}"]`);
                const itemTotalEl = document.getElementById(`itemTotal_${index}`);
                const remainingEl = itemCard.querySelector('.text-gray-500 span');
                
                const itemAmount = price * quantity;
                itemTotalEl.textContent = `₹${itemAmount.toFixed(2)}`;
                
                if (remainingEl) {
                    remainingEl.textContent = maxQty - quantity;
                }
                
                // Update selected items set
                if (quantity > 0) {
                    selectedItems.add(index);
                    itemCard.classList.add('item-selected');
                } else {
                    selectedItems.delete(index);
                    itemCard.classList.remove('item-selected');
                }
                
                updateUI();
            }
            
            // Update toggle state
            function updateToggleState(index, isChecked) {
                const toggle = document.getElementById(`toggle_${index}`);
                const toggleLabel = toggle.nextElementSibling;
                const toggleDot = toggleLabel.querySelector('.toggle-dot');
                
                if (toggle) {
                    toggle.checked = isChecked;
                    if (isChecked) {
                        toggleLabel.classList.remove('bg-gray-300');
                        toggleLabel.classList.add('bg-primary-600');
                        toggleDot.classList.add('translate-x-4');
                    } else {
                        toggleLabel.classList.remove('bg-primary-600');
                        toggleLabel.classList.add('bg-gray-300');
                        toggleDot.classList.remove('translate-x-4');
                    }
                }
            }
            
            // Update overall UI
            function updateUI() {
                let totalQuantity = 0;
                let itemsSubtotal = 0;
                let selectedCount = 0;
                
                // Calculate totals
                refundItems.forEach((item, index) => {
                    const input = document.getElementById(`qty_${index}`);
                    const quantity = parseInt(input.value) || 0;
                    
                    if (quantity > 0) {
                        totalQuantity += quantity;
                        itemsSubtotal += item.price * quantity;
                        selectedCount++;
                    }
                });
                
                // Add shipping and tax if selected
                let additionalAmount = 0;
                if (document.getElementById('includeShipping').checked) {
                    additionalAmount += shippingAmount;
                }
                if (document.getElementById('includeTax').checked) {
                    additionalAmount += taxAmount;
                }
                
                totalRefundAmount = itemsSubtotal + additionalAmount;
                
                // Update summary
                document.getElementById('selectedCount').textContent = selectedCount;
                document.getElementById('totalQuantity').textContent = totalQuantity;
                document.getElementById('itemsSubtotal').textContent = `₹${itemsSubtotal.toFixed(2)}`;
                document.getElementById('totalRefund').textContent = `₹${totalRefundAmount.toFixed(2)}`;
                
                // Update progress
                const totalItems = refundItems.length;
                const progressPercentage = totalItems > 0 ? (selectedCount / totalItems) * 100 : 0;
                document.getElementById('progressBar').style.width = `${progressPercentage}%`;
                document.getElementById('progressPercentage').textContent = `${Math.round(progressPercentage)}%`;
                
                // Update submit button
                const submitBtn = document.getElementById('submitRefundBtn');
                const submitText = document.getElementById('submitText');
                
                if (totalQuantity > 0) {
                    submitBtn.disabled = false;
                    submitText.textContent = `Refund ₹${totalRefundAmount.toFixed(2)}`;
                } else {
                    submitBtn.disabled = true;
                    submitText.textContent = 'Process Refund';
                }
            }
            
            // Form submission
            document.getElementById('submitRefundBtn').addEventListener('click', async function(e) {
                e.preventDefault();
                
                const submitBtn = this;
                const spinner = document.getElementById('loadingSpinner');
                const form = document.getElementById('refundForm');
                
                // Collect form data
                const formData = new FormData(form);
                formData.append('include_shipping', document.getElementById('includeShipping').checked);
                formData.append('include_tax', document.getElementById('includeTax').checked);
                formData.append('refund_reason', document.getElementById('refundReason').value);
                
                // Show loading
                submitBtn.disabled = true;
                spinner.classList.remove('hidden');
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        // Show success modal
                        const modal = document.getElementById('successModal');
                        const successMessage = document.getElementById('successMessage');
                        successMessage.textContent = result.message || `Successfully refunded ₹${totalRefundAmount.toFixed(2)}`;
                        modal.classList.remove('hidden');
                        
                        // Clear form
                        document.getElementById('refundForm').reset();
                        refundItems.forEach((item, index) => {
                            updateItem(index, 0);
                            updateToggleState(index, false);
                        });
                        updateUI();
                    } else {
                        throw new Error(result.message || 'Refund failed');
                    }
                    
                } catch (error) {
                    alert(`Error: ${error.message}`);
                } finally {
                    submitBtn.disabled = false;
                    spinner.classList.add('hidden');
                }
            });
            
            // Close modal
            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('successModal').classList.add('hidden');
            });
            
            // Initialize UI
            updateUI();
        });
    </script>

</div>
@endsection