<?php

namespace App\Models;
use App\Services\ShopifyService;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'shopify_price_rule_id',
        'shopify_discount_code_id',
        'code',
        'title',
        'value_type',
        'value',
        'target_type',
        'target_selection',
        'allocation_method',
        'usage_limit',
        'times_used',
        'customer_selection',
        'once_per_customer',
        'starts_at',
        'ends_at',
        'status',
        'raw_price_rule',
        'raw_discount_code',
        'synced_at',
    ];

    protected $casts = [
        'raw_price_rule' => 'array',
        'raw_discount_code' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'once_per_customer' => 'boolean',
        'usage_limit' => 'integer',
        'times_used' => 'integer',
    ];

    /** Check coupon status */
    public static function determineStatus(array $priceRule): string
    {
        $now = now();

        $startsAt = isset($priceRule['starts_at']) ? \Carbon\Carbon::parse($priceRule['starts_at']) : null;
        $endsAt = isset($priceRule['ends_at']) ? \Carbon\Carbon::parse($priceRule['ends_at']) : null;

        if ($startsAt && $now->lt($startsAt)) {
            return 'inactive';
        }

        if ($endsAt && $now->gt($endsAt)) {
            return 'expired';
        }

        return 'active';
    }

    public static function fromGraphQL(array $discount, string $code, string $nodeId): array
    {
        $valueType = 'fixed_amount';
        $value = 0;

        if (isset($discount['customerGets']['value']['percentage'])) {
            $valueType = 'percentage';
            $value = $discount['customerGets']['value']['percentage'] * 100;
        }

        if (isset($discount['customerGets']['value']['amount'])) {
            $valueType = 'fixed_amount';
            $value = $discount['customerGets']['value']['amount']['amount'];
        }

        return [
            'code' => $code,
            'title' => $discount['title'] ?? null,
            'value_type' => $valueType,
            'value' => abs((float) $value),
            'starts_at' => $discount['startsAt'] ?? null,
            'ends_at' => $discount['endsAt'] ?? null,
            'status' => now()->between(
                \Carbon\Carbon::parse($discount['startsAt'] ?? now()),
                \Carbon\Carbon::parse($discount['endsAt'] ?? now()->addYears(10))
            ) ? 'active' : 'inactive',
            'source' => 'graphql',
            'shopify_price_rule_id' => null,
            'raw_discount' => array_merge($discount, [
                'discount_node_id' => $nodeId, // 🔥 STORE THIS
            ]),
        ];
    }

    public static function storeFromGraphQL(array $node): void
    {
        $discount = $node['codeDiscount'];

        foreach ($discount['codes']['nodes'] as $codeNode) {

            $data = self::fromGraphQL(
                $discount,
                $codeNode['code'],
                $node['id'] // DiscountNode GID
            );

            self::updateOrCreate(
                ['code' => $data['code']],
                array_merge($data, [
                    'source' => 'graphql',
                    'synced_at' => now(),
                ])
            );
        }
    }

    public static function syncFromRest(array $priceRule, array $discountCode)
    {
        $data = self::fromRest($priceRule, $discountCode);

        return self::updateOrCreate(
            ['code' => $data['code']],
            array_merge($data, [
                'source' => 'rest',
                'synced_at' => now(),
            ])
        );
    }


}
