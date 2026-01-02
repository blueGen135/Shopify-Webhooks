<?php

namespace App\Http\Controllers;
use App\Services\ShopifyService;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

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
        $orders = Order::orderBy('id', 'asc')->paginate(15);
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
        $customers = \App\Models\ShopifyCustomer::orderBy('id', 'asc')->paginate(15);
        return view('shopify.customers', compact('customers'));
    }
    public function getStats(){}
    public function orders(){}
    public function importcust(){}
    public function export(){}
}

