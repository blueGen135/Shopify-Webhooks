<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCustomFieldDefinition extends Model
{
  use HasFactory;

  protected $fillable = [
    'gorgias_field_id',
    'external_id',
    'object_type',
    'label',
    'description',
    'priority',
    'required',
    'requirement_type',
    'managed_type',
    'definition',
    'gorgias_created_datetime',
    'gorgias_updated_datetime',
    'deactivated_datetime',
  ];

  protected $casts = [
    'gorgias_field_id' => 'integer',
    'priority' => 'integer',
    'required' => 'boolean',
    'definition' => 'object',
    'gorgias_created_datetime' => 'datetime',
    'gorgias_updated_datetime' => 'datetime',
    'deactivated_datetime' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  /**
   * Check if field is active
   */
  public function isActive(): bool
  {
    return $this->deactivated_datetime === null;
  }

  /**
   * Get input type from definition
   */
  public function getInputTypeAttribute(): ?string
  {
    return $this->definition->input_settings->input_type ?? null;
  }

  /**
   * Get choices from definition
   */
  public function getChoicesAttribute(): ?array
  {
    return $this->definition->input_settings->choices ?? null;
  }

  /**
   * Get data type from definition
   */
  public function getDataTypeAttribute(): ?string
  {
    return $this->definition->data_type ?? null;
  }

  /**
   * Sync custom field definition from Gorgias API response
   */
  public static function syncFromGorgias(array $data): self
  {
    return static::updateOrCreate(
      ['gorgias_field_id' => $data['id']],
      [
        'external_id' => $data['external_id'] ?? null,
        'object_type' => $data['object_type'] ?? 'Ticket',
        'label' => $data['label'],
        'description' => $data['description'] ?? null,
        'priority' => $data['priority'] ?? 0,
        'required' => $data['required'] ?? false,
        'requirement_type' => $data['requirement_type'] ?? 'visible',
        'managed_type' => $data['managed_type'] ?? null,
        'definition' => $data['definition'] ?? null,
        'gorgias_created_datetime' => $data['created_datetime'] ?? null,
        'gorgias_updated_datetime' => $data['updated_datetime'] ?? null,
        'deactivated_datetime' => $data['deactivated_datetime'] ?? null,
      ]
    );
  }

  /**
   * Scope query to only active custom fields
   */
  public function scopeActive($query)
  {
    return $query->whereNull('deactivated_datetime');
  }

  /**
   * Get all active custom fields
   */
  public static function getActive()
  {
    return static::active()
      ->orderBy('priority', 'desc')
      ->get();
  }

  /**
   * Get custom field by managed type
   */
  public static function getByManagedType(string $managedType): ?self
  {
    return static::active()
      ->where('managed_type', $managedType)
      ->first();
  }
}
