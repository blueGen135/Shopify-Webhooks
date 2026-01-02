<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public static function syncFromShopify($shopProduct)
    {
        $product = self::updateOrCreate(
            ['shopify_id' => $shopProduct['id']],
            [
                'title' => $shopProduct['title'],
                'body_html' => $shopProduct['body_html'] ?? null,
                'vendor' => $shopProduct['vendor'] ?? null,
                'product_type' => $shopProduct['product_type'] ?? null,
                'status' => $shopProduct['status'] ?? null,
                'image' => $shopProduct['image']['src'] ?? null,
                'price' => $shopProduct['variants'][0]['price'] ?? null,
            ]
        );

        // Update product images/delete old ones
        $product->images()->delete();
        foreach ($shopProduct['images'] ?? [] as $img) {
            $product->images()->create([
                'src' => $img['src']
            ]);
        }

        // update product variants
        foreach ($shopProduct['variants'] ?? [] as $variant) {
            ProductVariant::updateOrCreate(
                ['shopify_variant_id' => $variant['id']],
                [
                    'product_id' => $product->id,
                    'title' => $variant['title'] ?? null,
                    'price' => $variant['price'] ?? 0,
                    'sku' => $variant['sku'] ?? null,
                    'inventory_quantity' => $variant['inventory_quantity'] ?? 0,
                ]
            );
        }

        return $product;
    }

}
