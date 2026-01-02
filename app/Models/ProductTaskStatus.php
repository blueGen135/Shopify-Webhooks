<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTaskStatus extends Model
{
    protected $fillable = [
        'task_id',
        'product_id',
        'action',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Get the task that owns this product status.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(TicketTask::class, 'task_id');
    }

    /**
     * Action constants for better type safety.
     */
    const ACTION_VERIFIED = 'verified';
    const ACTION_NOT_VERIFIED = 'not_verified';
    const ACTION_READY_FOR_REPLACEMENT = 'ready_for_replacement';
    const ACTION_FULL_REFUND = 'full_refund';
    const ACTION_PARTIAL_REFUND = 'partial_refund';
    const ACTION_WAIT_FOR_RESTOCK = 'wait_for_restock';

    /**
     * Get available actions.
     */
    public static function getAvailableActions(): array
    {
        return [
            self::ACTION_VERIFIED => 'Verified',
            self::ACTION_NOT_VERIFIED => 'Not Verified',
            self::ACTION_READY_FOR_REPLACEMENT => 'Ready for Replacement',
            self::ACTION_FULL_REFUND => 'Full Refund',
            self::ACTION_PARTIAL_REFUND => 'Partial Refund',
            self::ACTION_WAIT_FOR_RESTOCK => 'Wait for Restock',
        ];
    }

    /**
     * Get processed inventory data for this product.
     * 
     * @param int $requiredQty Required quantity
     * @return array
     */
    public function getProcessedInventoryData(int $requiredQty = 1): array
    {
        $inventoryData = $this->details['inventory_data'] ?? null;

        if (!$inventoryData) {
            return [
                'available_qty' => 0,
                'restock_date' => null,
                'warehouses_with_stock' => 0,
                'has_stock' => false,
                'single_warehouse_can_fulfill' => false,
            ];
        }

        $availableQty = $inventoryData['total_available'] ?? 0;
        $restockDate = $inventoryData['earliest_restock_date'] ?? null;

        // Always calculate warehouses_with_stock and single_warehouse_can_fulfill from raw_data
        $warehousesWithStock = 0;
        $singleWarehouseCanFulfill = false;

        if (isset($inventoryData['raw_data']['companies'])) {
            foreach ($inventoryData['raw_data']['companies'] as $company) {
                foreach ($company['warehouses'] as $warehouse) {
                    $warehouseQty = $warehouse['available_qty'] ?? 0;
                    if ($warehouseQty > 0) {
                        $warehousesWithStock++;
                        // Check if this single warehouse can fulfill the requirement
                        if ($warehouseQty >= $requiredQty) {
                            $singleWarehouseCanFulfill = true;
                        }
                    }
                }
            }
        }

        return [
            'available_qty' => $availableQty,
            'restock_date' => $restockDate,
            'warehouses_with_stock' => $warehousesWithStock,
            'has_stock' => $availableQty >= $requiredQty,
            'single_warehouse_can_fulfill' => $singleWarehouseCanFulfill,
        ];
    }
}
