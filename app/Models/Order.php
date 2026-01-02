<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
   protected $fillable = [
    'shopify_order_id',
    'order_number',
    'customer_name',
    'customer_email',
    'total_price',
    'subtotal_price',
    'total_tax',
    'total_discounts',
    'total_line_items_price',
    'total_price_usd',
    'total_shipping_price',
    'total_refunded',
    'currency',
    'presentment_currency',
    'financial_status',
    'fulfillment_status',
    'processing_method',
    'billing_address',
    'shipping_address',
    'line_items',
    'total_price_set',
    'subtotal_price_set',
    'total_discounts_set',
    'total_shipping_price_set',
    'total_tax_set',
    'webhook_id',
    'webhook_received_at',
    'shopify_created_at',
    'shopify_updated_at',
    'cancelled_at',
    'closed_at',
    'synced_at',
];
    protected $casts = [
    // Decimal fields (already handled by database, but safe to cast)
    'total_price' => 'decimal:2',
    'subtotal_price' => 'decimal:2',
    'total_tax' => 'decimal:2',
    'total_discounts' => 'decimal:2',
    'total_line_items_price' => 'decimal:2',
    'total_price_usd' => 'decimal:2',
    'total_shipping_price' => 'decimal:2',
    'total_refunded' => 'decimal:2',
    
    // JSON fields
    'billing_address' => 'array',
    'shipping_address' => 'array',
    'line_items' => 'array',
    'total_price_set' => 'array',
    'subtotal_price_set' => 'array',
    'total_discounts_set' => 'array',
    'total_shipping_price_set' => 'array',
    'total_tax_set' => 'array',
    
    // Date fields
    'webhook_received_at' => 'datetime',
    'shopify_created_at' => 'datetime',
    'shopify_updated_at' => 'datetime',
    'cancelled_at' => 'datetime',
    'closed_at' => 'datetime',
    'synced_at' => 'datetime',
];

public static function syncFromShopify($orderData)
{
   
        $toDecimal = function ($value) {
            if (is_null($value) || $value === '') {
                return null;
            }
            
            if (is_numeric($value)) {
                return (float) $value;
            }
            
            if (is_string($value)) {
                // Remove any currency symbols and commas
                $cleaned = preg_replace('/[^\d\.\-]/', '', $value);
                return $cleaned !== '' ? (float) $cleaned : null;
            }
            
            return null;
        };
        
 
        
        // Extract shipping price
        $shippingPrice = null;
        if (isset($orderData['total_shipping_price_set']['shop_money']['amount'])) {
            $shippingPrice = $toDecimal($orderData['total_shipping_price_set']['shop_money']['amount']);
        } elseif (isset($orderData['shipping_lines'][0]['price'])) {
            $shippingPrice = $toDecimal($orderData['shipping_lines'][0]['price']);
        }
        
        // Calculate total line items quantity
        $totalQuantity = 0;
        if (isset($orderData['line_items']) && is_array($orderData['line_items'])) {
            foreach ($orderData['line_items'] as $item) {
                $totalQuantity += $item['quantity'] ?? 0;
            }
        }
        
        // Prepare the data array
        $data = [
            // Shopify Reference
            'order_number' => $orderData['order_number'] ?? null,
            
            // Customer Information
            'customer_name' => trim(($orderData['customer']['first_name'] ?? '') . ' ' . ($orderData['customer']['last_name'] ?? '')),
            'customer_email' => $orderData['customer']['email'] ?? null,
            
            // Pricing
            'total_price' => $toDecimal($orderData['total_price'] ?? null),
            'subtotal_price' => $toDecimal($orderData['subtotal_price'] ?? null),
            'total_tax' => $toDecimal($orderData['total_tax'] ?? null),
            'total_discounts' => $toDecimal($orderData['total_discounts'] ?? null),
            'total_line_items_price' => $toDecimal($orderData['total_line_items_price'] ?? null),
            'total_price_usd' => $toDecimal($orderData['total_price_usd'] ?? null),
            'total_shipping_price' => $shippingPrice,
            'total_refunded' => $toDecimal($orderData['total_refunded'] ?? 0),
            
            // Currency
            'currency' => $orderData['currency'] ?? null,
            'presentment_currency' => $orderData['presentment_currency'] ?? null,
            
            // Status
            'financial_status' => $orderData['financial_status'] ?? null,
            'fulfillment_status' => $orderData['fulfillment_status'] ?? null,
            'processing_method' => $orderData['processing_method'] ?? null,
            
            // JSON Data
            'billing_address' => $orderData['billing_address'] ?? null,
            'shipping_address' => $orderData['shipping_address'] ?? null,
            'line_items' => $orderData['line_items'] ?? null,
            'total_price_set' => $orderData['total_price_set'] ?? null,
            'subtotal_price_set' => $orderData['subtotal_price_set'] ?? null,
            'total_discounts_set' => $orderData['total_discounts_set'] ?? null,
            'total_shipping_price_set' => $orderData['total_shipping_price_set'] ?? null,
            'total_tax_set' =>$orderData['total_tax_set'] ?? null,
            
            // Timestamps
            'shopify_created_at' => isset($orderData['created_at']) ? Carbon::parse($orderData['created_at']) : null,
            'shopify_updated_at' => isset($orderData['updated_at']) ? Carbon::parse($orderData['updated_at']) : null,
            'cancelled_at' => isset($orderData['cancelled_at']) ? Carbon::parse($orderData['cancelled_at']) : null,
            'closed_at' => isset($orderData['closed_at']) ? Carbon::parse($orderData['closed_at']) : null,
            'synced_at' => now(),
        ];
        
        
        
        // Update or create the order
        $order = self::updateOrCreate(
            ['shopify_order_id' => $orderData['id']],
            $data
        );
        
        return $order;
   }
             
}                            
