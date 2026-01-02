<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopifyCustomer extends Model
{
    protected $fillable = [
        'shopify_customer_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'verified_email',
        'state',
        'orders_count',
        'total_spent',
        'currency',
        'accepts_marketing',
        'addresses',
        'default_address',
        'raw_response'
    ];

    protected $casts = [
        'addresses' => 'array',
        'default_address' => 'array',
        'raw_response' => 'array',
        'verified_email' => 'boolean',
    ];

public function orders(){
    return $this->hasMany(Order::class, 'customer_email', 'email');
}
   
    public static function syncFromShopify($shopCustomer)
    {
        return self::updateOrCreate(
            ['shopify_customer_id' => $shopCustomer['id']],
            [
                'first_name' => $shopCustomer['first_name'] ?? null,
                'last_name' => $shopCustomer['last_name'] ?? null,
                'email' => $shopCustomer['email'] ?? null,
                'phone' => $shopCustomer['phone'] ?? null,
                'verified_email' => $shopCustomer['verified_email'] ?? false,
                'state' => $shopCustomer['state'] ?? null,
                'orders_count' => $shopCustomer['orders_count'] ?? 0,
                'total_spent' => $shopCustomer['total_spent'] ?? 0,
                'currency' => $shopCustomer['currency'] ?? null,
                'accepts_marketing' => $shopCustomer['accepts_marketing'] ?? false,
                'addresses' => $shopCustomer['addresses'] ?? [],
                'default_address' => $shopCustomer['default_address'] ?? [],
                'raw_response' => $shopCustomer
            ]
        );
    }
}
