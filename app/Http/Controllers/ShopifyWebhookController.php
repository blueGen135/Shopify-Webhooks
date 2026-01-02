<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Order;
use App\Models\Coupon;
use App\Services\ShopifyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ShopifyWebhookController extends Controller
{
    private function verifyWebhook(Request $request)
    {
        $hmac_header = $request->header('X-Shopify-Hmac-Sha256');
        $data = $request->getContent();
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, env('SHOPIFY_API_SECRET'), true));

        return hash_equals($hmac_header, $calculated_hmac);
    }

    /** Product Created */
    public function productCreate(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        Product::syncFromShopify($data);
        return response('Product Synced', 200);
    }

    /** Product Updated */
    public function productUpdate(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        Product::syncFromShopify($data);
        return response('Product Updated', 200);
    }

    /** Product Deleted */
    public function productDelete(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $shopifyId = $data['id'];
        Product::where('shopify_id', $shopifyId)->delete();
        return response('Product Deleted', 200);
    }
    public function orderCreate(Request $request)
    {
     
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        Order::syncFromShopify($decoded);
        return response('Order Received', 200);
    }

    // public function orderUpdate(Request $request)
    // {
    //     $data = $request->getContent();
    //     $decoded = json_decode($data, true);
    //     Order::syncFromShopify($decoded);
    //     return response('Order Updated', 200);
    // }




    public function orderUpdate(Request $request)
    {
        try {
            // Log webhook headers for debugging
            Log::info('Webhook Headers Received', [
                'content-type' => $request->header('Content-Type'),
                'shop-domain' => $request->header('X-Shopify-Shop-Domain'),
                'webhook-topic' => $request->header('X-Shopify-Topic'),
                'webhook-id' => $request->header('X-Shopify-Webhook-Id'),
            ]);

            // Get raw content
            $rawContent = $request->getContent();
            Log::info('Webhook Raw Content Length', ['length' => strlen($rawContent)]);

            // Decode JSON
            $decoded = json_decode($rawContent, true);
            
            // Check for JSON errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error', [
                    'error' => json_last_error_msg(),
                    'content_sample' => substr($rawContent, 0, 200)
                ]);
                return response('Invalid JSON', 400);
            }

            // Log decoded data structure
            Log::info('Webhook Decoded Data', [
                'has_id' => isset($decoded['id']),
                'id' => $decoded['id'] ?? 'missing',
                'has_order_number' => isset($decoded['order_number']),
                'order_number' => $decoded['order_number'] ?? 'missing',
                'keys' => array_keys($decoded)
            ]);

            // Validate required fields
            if (!isset($decoded['id'])) {
                Log::error('Missing order ID in webhook');
                return response('Missing order ID', 400);
            }

            if (!isset($decoded['order_number'])) {
                Log::error('Missing order number in webhook');
                return response('Missing order number', 400);
            }

            // Add webhook metadata to order data
            $decoded['webhook_id'] = $request->header('X-Shopify-Webhook-Id') ?? 'webhook_' . time();
            $decoded['webhook_received_at'] = now()->toDateTimeString();

            // Log before sync
            Log::info('Attempting to sync order', [
                'shopify_order_id' => $decoded['id'],
                'order_number' => $decoded['order_number']
            ]);

            // Try to sync
            try {
                $order = Order::syncFromShopify($decoded);
                
                Log::info('Order sync successful', [
                    'order_id' => $order->id,
                    'shopify_order_id' => $decoded['id'],
                    'was_created' => $order->wasRecentlyCreated,
                    'was_updated' => !$order->wasRecentlyCreated
                ]);
                
            } catch (\Exception $e) {
                Log::error('Order sync failed', [
                    'shopify_order_id' => $decoded['id'],
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            return response('Order Updated Successfully', 200);

        } catch (\Exception $e) {
            Log::error('Webhook Processing Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response('Internal Server Error', 500);
        }
    }
    public function orderFullfilled(Request $request)
    {
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        Order::syncFromShopify($decoded);
        return response('Order Updated', 200);
    }
    public function orderCancelled(Request $request)
    {
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        Order::syncFromShopify($decoded);
        return response('Order Cancelled', 200);
    }
    public function refundedCreated(Request $request)
    {
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        Order::syncFromShopify($decoded);
        return response('Refund Processed', 200);
    }
    public function customerCreate(Request $request)
    {
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        \App\Models\ShopifyCustomer::syncFromShopify($decoded);
        return response('Customer Created', 200);
    }
    public function customerUpdate(Request $request)
    {
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        \App\Models\ShopifyCustomer::syncFromShopify($decoded);
        return response('Customer Updated', 200);
    }
    public function customerDelete(Request $request)
    {
        $data = $request->getContent();
        $decoded = json_decode($data, true);
        $shopifyId = $decoded['id'];
        \App\Models\ShopifyCustomer::where('shopify_customer_id', $shopifyId)->delete();
        return response('Customer Deleted', 200);
    }

   public function discountCreate(Request $request, ShopifyService $shopify)
    {
        $result = $shopify->syncCouponsToDB();
        return response('Discount synced', 200);
    }

    public function discountUpdated(Request $request, ShopifyService $shopify)
    {
        $result = $shopify->syncCouponsToDB();
        return response('Discount synced', 200);
    }

    public function discountDeleted(Request $request, ShopifyService $shopify)
    {
        \Log::info('Discount webhook hit', [
        'headers' => $request->headers->all(),
        'payload' => $request->all(),
        ]);

        $result = $shopify->syncCouponsToDB();
        return response('Discount synced', 200);
    }

}
