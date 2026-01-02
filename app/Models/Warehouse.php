<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'address',
    'city',
    'state',
    'postal_code',
    'country_code',
    'latitude',
    'longitude',
    'status',
    'priority',
  ];

  protected $casts = [
    'latitude' => 'decimal:7',
    'longitude' => 'decimal:7',
    'status' => 'boolean',
    'priority' => 'integer',
  ];

  /**
   * Scope to get only active warehouses
   */
  public function scopeActive($query)
  {
    return $query->where('status', true);
  }

  /**
   * Scope to order by priority
   */
  public function scopeOrdered($query)
  {
    return $query->orderBy('priority', 'desc')->orderBy('name', 'asc');
  }
}
