<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'gorgias_tag_id',
        'name',
        'description',
        'color',
        'usage',
        'gorgias_created_datetime',
        'gorgias_deleted_datetime',
    ];

    protected $casts = [
        'usage' => 'integer',
        'gorgias_created_datetime' => 'datetime',
        'gorgias_deleted_datetime' => 'datetime',
    ];

    /**
     * Sync tag from Gorgias API data
     */
    public static function syncFromGorgias(array $gorgiasData): self
    {
        return self::updateOrCreate(
            ['gorgias_tag_id' => $gorgiasData['id']],
            [
                'name' => $gorgiasData['name'],
                'description' => $gorgiasData['description'],
                'color' => $gorgiasData['decoration']['color'] ?? null,
                'usage' => $gorgiasData['usage'] ?? 0,
                'gorgias_created_datetime' => $gorgiasData['created_datetime'] ?? null,
                'gorgias_deleted_datetime' => $gorgiasData['deleted_datetime'] ?? null,
            ]
        );
    }
}
