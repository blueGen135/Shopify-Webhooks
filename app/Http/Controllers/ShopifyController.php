<?php

namespace App\Http\Controllers;
use App\Services\ShopifyService;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\ShopifyCustomer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
class ShopifyController extends Controller
{
    
    public function index(ShopifyService $shopify)
    {
        $data = $shopify->getProducts();
        $products = $data['products'];
        return view('shopify.index', compact('products'));
    }

    // public function createProduct(ShopifyService $shopify)
    // {
    //     $data = [
    //         'title' => 'New Product from Laravel',
    //         'body_html' => 'Description here',
    //         'vendor' => 'BGC',
    //         'product_type' => 'Custom'
    //     ];

    //     return $shopify->createProduct($data);
    // }

    // public function getOrdersFromStore(ShopifyService $shopify)
    // {
    //     $data = $shopify->getOrders([
    //         'limit' => 5
    //     ]);
    //     $orders = $data['orders'];
    //     return view('shopify.orders', compact('orders'));
    // }
    public function import(ShopifyService $shopify)
    {
        $result = $shopify->syncProductsToDB();
        return redirect()->back()->with('success', "Successfully imported {$result} products.");
    }
    public function importedProducts(){
        $products = Product::paginate(15);
        return view('shopify.imported-products', compact('products'));
    }
    public function addNewProduct(){
        return view('shopify.create-product');
    }

   public function storeProduct(Request $request, ShopifyService $shopify)
    {
        $request->validate([
            'title' => 'required|string',
            'body_html' => 'nullable|string',
            'vendor' => 'nullable|string',
            'product_type' => 'nullable|string',
            'images.*' => 'nullable|image|max:5048',
        ]);
        $imageUrls = [];
        if ($request->file('image')) {
                $image = $request->file('image');
                $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
                $destinationPath = 'upload/products';
                $image->move($destinationPath,$name_gen);
                $save_url = url('/').'/upload/products/'.$name_gen;
                array_push($imageUrls, $save_url);
            }   

        // STEP 1: Upload all images locally
        // $imageUrls = [];
        // if ($request->hasFile('images')) {
        //     foreach ($request->file('images') as $file) {
        //         $name_gen = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
        //         $destinationPath = 'upload/products';
        //         $file->move($destinationPath,$name_gen);
        //         // $save_url = url('/').asset('upload/products/'.$name_gen);

        //         // $imageUrls[] = $save_url;
        //     }
        // }

            

        // STEP 2: Create base product in Shopify
        $productData = [
            'title' => $request->title,
            'body_html' => $request->body_html,
            'vendor' => $request->vendor,
            'product_type' => $request->product_type,
        ];


        $shopifyResponse = $shopify->createProduct($productData);

        if (!isset($shopifyResponse['product']['id'])) {
            return back()->with('error', 'Failed to create product in Shopify');
        }

        $productId = $shopifyResponse['product']['id'];

        // // STEP 3: Upload images separately to Shopify
        $uploadedImages = [];
        foreach ($imageUrls as $url) {
            $imageUpload = $shopify->makeRequest("products/$productId/images.json", 'POST', [
                'image' => ['src' => $url]
            ]);

            if (isset($imageUpload['image'])) {
                $uploadedImages[] = $imageUpload['image']['src'];
            }
        }

        // // STEP 4: Store product in Laravel DB
        // $product = Product::create([
        //     'shopify_id' => $productId,
        //     'title' => $request->title,
        //     'body_html' => $request->body_html,
        //     'vendor' => $request->vendor,
        //     'product_type' => $request->product_type,
        //     'status' => 'active',
        //     'price' => $shopifyResponse['product']['variants'][0]['price'] ?? null,
        //     'image' => $uploadedImages[0] ?? null
        // ]);

        //       
        return redirect()->back()->with('success', "Successfully created.");
    }


    public function viewProduct($id)
    {
        $product = Product::with(['images', 'variants'])->findOrFail($id);
        return view('shopify.view-product', compact('product'));
    }


    public function importOrders(ShopifyService $shopify)
    {
        $result = $shopify->syncOrdersToDB();
        return redirect()->back()->with('success', "Successfully imported {$result} orders.");
    }
    public function getOrders(){
        $orders = Order::orderBy('id', 'desc')->paginate(15);
        return view('shopify.orders', compact('orders'));
    }
 

    public function fulfillOrder($orderId)
    {
        try {
        $order = Order::findOrFail($orderId);
        
        
        
        // Initialize Shopify API
        $shopify = new ShopifyService(env('SHOPIFY_SHOP_DOMAIN'), env('SHOPIFY_ACCESS_TOKEN'));
        
        \Log::info('Starting fulfillment process', [
            'order_id' => $orderId,
            'shopify_order_id' => $order->shopify_order_id,
            'shop_domain' => env('SHOPIFY_SHOP_DOMAIN')
        ]);
        
        // Step 1: Get fulfillment orders for this order
        $fulfillmentOrders = $shopify->makeRequest(
            "orders/{$order->shopify_order_id}/fulfillment_orders.json",
            'GET'
        );
        
        \Log::info('Fulfillment Orders Response:', $fulfillmentOrders);
        
        if (empty($fulfillmentOrders['fulfillment_orders'])) {
            throw new \Exception('No fulfillment orders found. The order may already be fulfilled or cancelled.');
        }
        
        // Use the first fulfillment order
        $fulfillmentOrder = $fulfillmentOrders['fulfillment_orders'][0];
        $fulfillmentOrderId = $fulfillmentOrder['id'];
        
        // Step 2: Get available locations
        $locations = $shopify->makeRequest('locations.json', 'GET');
        
        if (empty($locations['locations'])) {
            throw new \Exception('No locations found in Shopify store');
        }
        
        $locationId = $locations['locations'][0]['id'];
        
        \Log::info('Using location:', [
            'location_id' => $locationId,
            'location_name' => $locations['locations'][0]['name']
        ]);
        
        // Step 3: Prepare line items for fulfillment
        $lineItemsForFulfillment = [];
        foreach ($fulfillmentOrder['line_items'] as $item) {
            if ($item['fulfillable_quantity'] > 0) {
                $lineItemsForFulfillment[] = [
                    'id' => $item['id'],
                    'quantity' => $item['fulfillable_quantity']
                ];
            }
        }
        
        if (empty($lineItemsForFulfillment)) {
            throw new \Exception('No fulfillable items found');
        }
        
        \Log::info('Line items for fulfillment:', $lineItemsForFulfillment);
        
        // Step 4: Create fulfillment
        // For API version 2021-07 or later
        $response = $shopify->makeRequest(
            "fulfillments.json",
            'POST',
            [
                'fulfillment' => [
                    'line_items_by_fulfillment_order' => [
                        [
                            'fulfillment_order_id' => $fulfillmentOrderId
                        ]
                    ],
                    'notify_customer' => true,
                    'location_id' => $locationId
                ]
            ]
        );
        
        \Log::info('Fulfillment Created:', $response);
        
        // Step 5: Update local order
        $order->update([
            'fulfillment_status' => 'fulfilled',
            'fulfilled_at' => now(),
            'tracking_company' => $response['fulfillment']['tracking_company'] ?? null,
            'tracking_number' => $response['fulfillment']['tracking_number'] ?? null,
            'tracking_url' => $response['fulfillment']['tracking_url'] ?? null,
        ]);
        
        return redirect()->back()->with('success', 'Order fulfilled successfully in Shopify!');
        
        } catch (\Exception $e) {
            \Log::error('Fulfillment Error:', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Failed to fulfill order: ' . $e->getMessage());
        }
    }
    public function importCustomers(ShopifyService $shopify)
    {
        $result = $shopify->syncCustomersToDB();
        return redirect()->back()->with('success', "Successfully imported {$result} customers.");
    }
    public function getCustomers(){
        $customers = ShopifyCustomer::orderBy('id', 'asc')->paginate(15);
        return view('shopify.customers', compact('customers'));
    }
  
    public function show($id){
        $customer = ShopifyCustomer::findOrFail($id);
        $products = Product::with('variants')->get(); 
        $orders = Order::where('customer_email', $customer->email)
            ->orderBy('shopify_created_at', 'desc')
            ->get();    
            
        return view('shopify.view-customer', compact('customer', 'orders', 'products'));
    }

    public function createForCustomer($id, ShopifyService $shopify)
    {
        $customer = ShopifyCustomer::findOrFail($id);
        $products = Product::with('variants')->get(); // All Shopify synced products
        $currency = $shopify->getStoreCurrency();
        $existingItems = [];
        return view('shopify.create-order', compact('customer', 'products','currency', 'existingItems'));
    }

    public function storeForCustomer(Request $request,ShopifyCustomer $customer,ShopifyService $shopify) {

        $appliedDiscount = null;

        $discountType = $request->input('discount.type');
        $discountValue = $request->input('discount.value');

        $lineItems = [];
        foreach ($request->input('items', []) as $item) {
            $lineItems[] = [
                // Use 'id' if it's an existing line item, otherwise Shopify creates a new one
                'id' => $item['line_item_id'] ?? null,
                'variant_id' => $item['variant_id'],
                'quantity' => (int) $item['quantity'],
            ];
        }

            if ($discountType && $discountValue > 0) {
                $appliedDiscount = [
                    'description' => 'Manual discount',
                    'value' => (float) $discountValue,
                    'value_type' => $discountType === 'percentage' ? 'percentage' : 'fixed_amount',
                    'amount' => $discountType === 'fixed'
                        ? (float) $discountValue
                        : null,
                ];
            }
         
        
        $orderData = [
            'customer' => [
                'id' => $customer->shopify_customer_id,
            ],
            'line_items' => $lineItems,
            'use_customer_default_address' => true,
            'tags' => 'created-from-laravel',
            'note' => 'Order created from Laravel admin',
        ];

        if ($appliedDiscount) {
            $orderData['applied_discount'] = $appliedDiscount;
        }

        $draft = $shopify->createDraftOrder($orderData);

        if (!$draft || !isset($draft['draft_order']['id'])) {
            return back()->with('error', 'Failed to create draft order in Shopify');
        }

        $draftOrderId = $draft['draft_order']['id'];

        $shopify->completeDraftOrder($draftOrderId);
        return redirect()
            ->route('shopify.customers')
            ->with('success', 'Order created and synced to Shopify successfully!');
    }


    public function updateShopifyCustomer( Request $request,ShopifyCustomer $customer,ShopifyService $shopify) {
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'email'      => 'nullable|email',
            'phone'      => 'nullable|string|max:50',
        ]);

        if (!$customer->shopify_customer_id) {
            return back()->with('error', 'Shopify Customer ID missing');
        }

        $shopify->updateCustomer(
            $customer->shopify_customer_id,
            $validated
        );

        return redirect()->route('shopify.customers')->with('success', 'Customer updated successfully');
    }

    public function destroyCustomer(ShopifyCustomer $customer,ShopifyService $shopify) {
        if (!$customer->shopify_customer_id) {
            return back()->with('error', 'Shopify Customer ID missing');
        }

        // 🔴 Disable customer in Shopify (NOT delete)
        $shopify->deleteCustomer($customer->shopify_customer_id);
        $customer->delete();
        return back()->with('success', 'Customer disabled successfully');
    }

    public function viewOrder(Order $order)
    {
        $lineItems = is_array($order->line_items) ? $order->line_items : json_decode($order->line_items, true);
        $shippingRefundable = (float) ($order->total_shipping_price ?? 0);
        $taxRefundable = collect($lineItems)->flatMap(fn ($i) => $i['tax_lines'] ?? [])->sum(fn ($t) => (float) $t['price']);

        $refundItems = collect($lineItems ?? [])
            ->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'price' => (float) ($item['price'] ?? 0),
                    'maxQty' => (int) ($item['quantity'] ?? 0),
                    'refundQty' => 0,
                ];
            })
            ->values();

        return view('shopify.view-order', compact('order', 'refundItems', 'shippingRefundable', 'taxRefundable'));
    }

    public function duplicateOrder(Order $order, ShopifyService $shopify)
    {   
        if (!$order->customer_email) {
            return back()->with('error', 'Order has no Shopify customer');
        }
        $customer = ShopifyCustomer::where('email', $order->customer_email)->first();
        if (!$customer) {
            return back()->with('error', 'Customer not found in local database');
        }
        $orderItems = is_array($order->line_items) ? $order->line_items : json_decode($order->line_items, true);
        $lineItems = collect($orderItems)
            ->map(function ($item) {
                return [
                    'variant_id' => $item['variant_id'] ?? $item['id'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                ];
            })
            ->filter(fn ($item) => !empty($item['variant_id']))
            ->values()
            ->toArray();
        
       

        if (empty($lineItems)) {
            return back()->with('error', 'No valid line items to duplicate');
        }

        // Prepare draft order payload
        $payload = [
            'customer' => [
                'id' => $customer->shopify_customer_id,
            ],
            'line_items' => $lineItems,
            'use_customer_default_address' => true,
            'tags' => 'duplicated-from-order-' . $order->order_number,
            'note' => 'Duplicated from Order #' . $order->order_number,
        ];

        //  Apply discount if exists
        if (!empty($order->total_discounts) && $order->total_discounts > 0) {
            $payload['applied_discount'] = [
                'description' => 'Duplicated discount',
                'value_type' => 'fixed_amount',
                'value' => (float) $order->total_discounts,
                'amount' => (float) $order->total_discounts,
            ];
        }

        // Create draft order in Shopify
        $draft = $shopify->createDraftOrder($payload);

        if (!isset($draft['draft_order']['id'])) {
            return back()->with('error', 'Failed to duplicate order in Shopify');
        }

        // OPTIONAL: Auto-complete the duplicated order
        $shopify->completeDraftOrder($draft['draft_order']['id']);

        return back()->with('success', 'Order duplicated successfully');
    }
    public function cancelOrder(Order $order, ShopifyService $shopify)
    {
        if (!$order->shopify_order_id) {
            return back()->with('error', 'Shopify Order ID missing');
        }

        if ($order->cancelled_at) {
            return back()->with('error', 'Order already cancelled');
        }

        if ($order->fulfillment_status === 'fulfilled') {
            return back()->with('error', 'Fulfilled orders cannot be cancelled');
        }

        $shopify->cancelOrder(
            (int) $order->shopify_order_id,
            restock: true,
            reason: 'customer'
        );

        return back()->with('success', 'Order cancelled successfully');
    }

    
    public function refundOrder(Request $request, Order $order, ShopifyService $shopify)
    {
        $refundedAmount = $order->total_refunded ?? 0;
        $refundableAmount = $order->total_price - $refundedAmount;
        
        if ($refundableAmount <= 0) {
            return back()->with('error', 'Order has already been fully refunded.');
        }
        
        return view('shopify.refund-confirm', compact('order', 'refundableAmount'));
    }

/**
 * Show refund confirmation page
 */
    private function showRefundConfirmation(Order $order)
    {
        // Validate order can be refunded
        if (!$order->shopify_order_id) {
            return back()->with('error', 'Shopify Order ID missing');
        }
        
        if ($order->financial_status !== 'paid') {
            return back()->with('error', 'Order is not paid. Cannot refund unpaid order.');
        }
        
        $refundedAmount = $order->total_refunded ?? 0;
        $refundableAmount = $order->total_price - $refundedAmount;
        
        if ($refundableAmount <= 0) {
            return back()->with('error', 'Order has already been fully refunded.');
        }
        
        return view('shopify.refund-confirm', compact('order', 'refundableAmount'));
    }

/**
 * Process the refund
 */
    public function processRefund(Request $request, Order $order, ShopifyService $shopify)
    {
        try {
            $request->validate([
                '_token' => 'required'
            ]);
            
            // Validate order can be refunded
            if (!$order->shopify_order_id) {
                return back()->with('error', 'Shopify Order ID missing');
            }
            
            if ($order->financial_status !== 'paid') {
                return back()->with('error', 'Order is not paid. Cannot refund unpaid order.');
            }
            
            $refundedAmount = $order->total_refunded ?? 0;
            $refundableAmount = $order->total_price - $refundedAmount;
            
            if ($refundableAmount <= 0) {
                return back()->with('error', 'Order has already been fully refunded.');
            }
            $isFulfilled = $order->fulfillment_status === 'fulfilled';

            if ($isFulfilled) {
                $response = $shopify->refundFulfilledOrder(
                    (int) $order->shopify_order_id,
                    $order->currency ?? 'INR'
                );
            } else {
                // Process refund for unfulfilled order (with restocking)
                $response = $shopify->refundFullOrder(
                    (int) $order->shopify_order_id,
                    $order->currency ?? 'INR'
                );
            }
            
            if (!isset($response['refund'])) {
                throw new \Exception('Refund response invalid: ' . json_encode($response));
            }
            
            // Update local order record
            $refundAmount = $response['refund']['transactions'][0]['amount'] ?? $order->total_price;
            $order->update([
                'total_refunded' => $refundedAmount + $refundAmount,
                'financial_status' => $refundableAmount <= $refundAmount ? 'refunded' : 'partially_refunded',
                'refunds' => json_encode(array_merge(
                    json_decode($order->refunds ?? '[]', true),
                    [$response['refund']]
                ))
            ]);
            
            return redirect()->route('shopify.view-order', $order->id)
                ->with('success', 
                    "Order refunded successfully. Amount: {$order->currency} {$refundAmount}"
                );
            
        } catch (\Exception $e) {
            return back()->with('error', 
                'Refund failed: ' . $e->getMessage() . 
                '. Please check Shopify admin or contact support.'
            );
        }
    }

    public function partialRefundForm(Order $order, ShopifyService $shopify)
    {
        $lineItems = is_array($order->line_items) ? $order->line_items : json_decode($order->line_items, true);
        $fulfillmentItems  = collect($lineItems)
        ->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => (float) $item['price'],
                'maxQty' => (int) $item['quantity'],
                'refundQty' => 0,
            ];
        })
        ->values();
        $locations = $shopify->getLocations();
        return view('shopify.partial-refund-form', compact('order', 'fulfillmentItems', 'locations'));
    }

    public function partialRefund(Request $request, Order $order, ShopifyService $shopify)
    {

        $validated = $request->validate([
        'items' => 'required|array',
        'items.*.line_item_id' => 'required|numeric',
        'items.*.quantity' => 'required|integer|min:0',
        ]);

        $refundLineItems = collect($validated['items'])
        ->filter(fn ($i) => $i['quantity'] > 0)
        ->map(fn ($i) => [
            'line_item_id' => (int) $i['line_item_id'],
            'quantity' => (int) $i['quantity'],
            'restock_type' => 'return',
        ])
        ->values()
        ->toArray();
        
        if (empty($refundLineItems)) {
            return back()->with('error', 'No items selected');
        }

        // Calculate refund amount
        $orderItems = is_string($order->line_items) ? json_decode($order->line_items, true) : $order->line_items;

        $orderItems = collect($orderItems);
        $refundAmount = 0;
        $shippingAmount = (float) $request->input('shipping_amount', 0);
        $taxAdjustment = (float) $request->input('tax_adjustment', 0);

        foreach ($refundLineItems as $item) {
            $original = $orderItems->firstWhere('id', $item['line_item_id']);
            if ($original) {
                $refundAmount += ((float) $original['price'] * $item['quantity']);
            }
        }

        try {
        if ($order->financial_status === 'paid') {
            // 💰 Paid → refund money + items
            $response = $shopify->refundPartialOrder(
                (int) $order->shopify_order_id,
                $refundLineItems,
                $order->currency,
                $shippingAmount,
                $taxAdjustment
            );
        } else {
            // 📦 Unpaid → restock only
            $response = $shopify->restockOnly(
                (int) $order->shopify_order_id,
                $refundLineItems
            );
        }

        $alreadyRefunded = (float) ($order->total_refunded ?? 0);
        $newTotalRefunded = $alreadyRefunded + $refundAmount;
        $financialStatus = $newTotalRefunded >= (float) $order->total_price ? 'refunded' : 'partially_refunded';
        $existingRefunds = $order->refunds ?? [];
        $existingRefunds[] = $response['refund'];

        $order->update([
            'total_refunded' => number_format($newTotalRefunded, 2, '.', ''),
            'financial_status' => $financialStatus,
            'refunds' => json_encode($existingRefunds),
        ]);


        return back()->with('success', 'Partial refund processed');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editOrder(Order $order, ShopifyService $shopify)
    {
        if ($order->fulfillment_status === 'fulfilled') {
            return back()->with('error', 'Cannot edit fulfilled orders.');
        }

        

        $products = Product::with('variants')->get();
        $currency = $shopify->getStoreCurrency();

        $existingItems = collect($order->line_items)
            ->map(fn ($item) => [
                'line_item_id' => $item['id'],
                'variant_id' => $item['variant_id'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'title' => $item['name'],
            ])
            ->values();

        return view('shopify.edit-order', compact(
            'order',
            'products',
            'existingItems',
            'currency'
        ));
    }

    public function updateOrder(Request $request, Order $order, ShopifyService $shopify)
    {
        
        if ($order->fulfillment_status === 'fulfilled') {
            return back()->with('error', 'Cannot edit fulfilled orders.');
        }

        if (
            in_array($order->financial_status, ['refunded', 'partially_refunded']) ||
            ($order->total_refunded && $order->total_refunded > 0)
        ) {
            return back()->with('error', 'Refunded orders cannot be edited.');
        }
        $customer = ShopifyCustomer::where('email', $order->customer_email)->first();

        $lineItems = [];
        foreach ($request->input('items', []) as $item) {
            $lineItems[] = [
                'variant_id' => $item['variant_id'],
                'quantity' => (int) $item['quantity'],
            ];
        }

        if (empty($lineItems)) {
            return back()->with('error', 'Order must contain at least one item.');
        }


        $payload = [
            'customer' => [
                'id' => $customer->shopify_customer_id,
            ],
            'line_items' => $lineItems,
            'note'       => 'Order created and updated from Laravel admin',
        ];

        $draft = $shopify->createDraftOrder($payload);

        if (!$draft || !isset($draft['draft_order']['id'])) {
            return back()->with('error', 'Failed to update draft order in Shopify.');
        }

        $completed = $shopify->completeDraftOrder($draft['draft_order']['id']);

        if (!$completed || !isset($completed['draft_order']['order_id'])) {
            return back()->with('error', 'Failed to complete updated order.');
        }

        return redirect()
            ->back()
            ->with('success', 'Order updated successfully.');
    }



    public function Coupons(){
        $coupons = Coupon::all();
        return view('shopify.coupons', compact('coupons'));
    }

    public function importCoupons(ShopifyService $shopify)
    {
        $result = $shopify->syncCouponsToDB();
        return redirect()->back()->with('success', "Successfully imported {$result} coupons.");
    }
   
    public function deleteCoupon(Coupon $coupon, ShopifyService $shopify)
    {
        try {
            $shopify->deletePriceRule($coupon->shopify_price_rule_id);
            $coupon->delete();

            return back()->with('success', 'Coupon deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function createCoupon(){
        $customers = ShopifyCustomer::all();
        return view('shopify.create-coupon', compact('customers'));
    }


    public function storeCoupon(Request $request, ShopifyService $shopify)
    {   
            $result = $shopify->createRestCoupon($request->all());
             if (!$result['success']) {
                    return back()->withErrors('Failed to sync with Shopify');
                }

                $rule = $result['price_rule'];
                $code = $result['discount_code'];

                // Sync with your Laravel Database
                Coupon::create([
                    'shopify_price_rule_id' => $rule['id'],
                    'shopify_discount_code_id' => $code['id'] ?? null,
                    'code' => $code['code'],
                    'title' => $rule['title'],
                    'value_type' => $rule['value_type'],
                    'value' => $rule['value'],
                    'target_type' => $rule['target_type'],
                    'target_selection' => $rule['target_selection'],
                    'allocation_method' => $rule['allocation_method'],
                    'usage_limit' => $rule['usage_limit'],
                    'starts_at' => $rule['starts_at'],
                    'ends_at' => $rule['ends_at'],
                    'status' => 'active',
                    'raw_price_rule' => $rule,
                    'raw_discount_code' => $code,
                    'synced_at' => now(),
                ]);

                return redirect()->route('shopify.all-coupons')->with('success', 'Coupon created successfully!');
    }

        

}

