<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
  use HasFactory;

  protected $fillable = [
    'gorgias_ticket_id',
    'order_id',
    'subject',
    'summary',
    'uri',
    'status',
    'priority',
    'requester_id',
    'requester_email',
    'requester_name',
    'requester_firstname',
    'requester_lastname',
    'assignee_user_id',
    'assignee_team_id',
    'gorgias_user_id',
    'is_unread',
    'created_datetime',
    'opened_datetime',
    'last_received_message_datetime',
    'last_message_datetime',
    'updated_datetime',
    'closed_datetime',
    'trashed_datetime',
    'snooze_datetime',
    'custom_fields',
  ];

  protected $casts = [
    'gorgias_ticket_id' => 'integer',
    'requester_id' => 'integer',
    'assignee_user_id' => 'integer',
    'assignee_team_id' => 'integer',
    'is_unread' => 'boolean',
    'created_datetime' => 'datetime',
    'opened_datetime' => 'datetime',
    'last_received_message_datetime' => 'datetime',
    'last_message_datetime' => 'datetime',
    'updated_datetime' => 'datetime',
    'closed_datetime' => 'datetime',
    'trashed_datetime' => 'datetime',
    'snooze_datetime' => 'datetime',
    'custom_fields' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  /**
   * Get the local user who created the ticket.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'gorgias_user_id');
  }

  /**
   * Get the order associated with this ticket.
   */
  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class);
  }

  /**
   * Check if ticket has an associated order.
   */
  public function hasOrder(): bool
  {
    return !is_null($this->order_id);
  }

  /**
   * Get all activity logs for this ticket.
   */
  public function activityLogs(): HasMany
  {
    return $this->hasMany(TicketActivityLog::class)->orderBy('created_at', 'desc');
  }

  /**
   * Get all tasks for this ticket.
   */
  public function tasks(): HasMany
  {
    return $this->hasMany(TicketTask::class)->orderBy('order');
  }

  /**
   * Helper to log activity on this ticket.
   */
  public function logActivity(string $action, ?string $title = null, ?string $status = null, ?array $metaData = null): TicketActivityLog
  {
    return TicketActivityLog::logActivity($this->id, $action, $title, $status, $metaData);
  }

  /**
   * Create tasks for this ticket based on type.
   */
  public function createTasksForType(string $type): void
  {
    $tasks = match ($type) {
      'misship' => [
        ['type' => TicketTask::TYPE_VERIFICATION, 'status' => TicketTask::STATUS_PENDING, 'order' => 1],
        ['type' => TicketTask::TYPE_INVENTORY_CHECK_RESOLUTION, 'status' => TicketTask::STATUS_PENDING, 'order' => 2],
        ['type' => TicketTask::TYPE_CLOSE_TICKET, 'status' => TicketTask::STATUS_PENDING, 'order' => 3],
      ],
      default => [],
    };

    foreach ($tasks as $taskData) {
      $this->tasks()->create($taskData);
    }
  }

  public function getStatusForDisplayAttribute(): ?string
  {
    return ucfirst($this->status);
  }

  /**
   * Get the status badge color.
   */
  public function getStatusBadgeAttribute(): string
  {
    return match (strtolower($this->status ?? '')) {
      'open' => 'bg-label-primary',
      'in_progress', 'pending' => 'bg-label-warning',
      'resolved', 'solved' => 'bg-label-success',
      'closed' => 'bg-label-secondary',
      default => 'bg-label-info',
    };
  }

  /**
   * Get the priority badge color.
   */
  public function getPriorityBadgeAttribute(): string
  {
    return match (strtolower($this->priority ?? '')) {
      'low' => 'bg-label-success',
      'medium', 'normal' => 'bg-label-warning',
      'high' => 'bg-label-danger',
      'urgent', 'critical' => 'bg-label-danger',
      default => 'bg-label-info',
    };
  }

  /**
   * Get custom field value by managed type (e.g., 'contact_reason', 'product', 'resolution')
   */
  public function getCustomField(string $managedType): ?string
  {
    if (!$this->custom_fields) {
      return null;
    }

    return $this->custom_fields[$managedType] ?? null;
  }

  /**
   * Set custom field value
   */
  public function setCustomField(string $managedType, $value): void
  {
    $fields = $this->custom_fields ?? [];
    $fields[$managedType] = $value;
    $this->custom_fields = $fields;
  }

  /**
   * Get resolution from custom fields
   */
  public function getResolutionAttribute(): ?string
  {
    return $this->getCustomField('resolution');
  }

  /**
   * Get formatted requester name
   */
  public function getRequesterFullNameAttribute(): string
  {
    if ($this->requester_name) {
      return $this->requester_name;
    }

    if ($this->requester_firstname && $this->requester_lastname) {
      return trim($this->requester_firstname . ' ' . $this->requester_lastname);
    }

    return $this->requester_email ?? 'Unknown';
  }

  /**
   * Check if ticket is open
   */
  public function isOpen(): bool
  {
    return strtolower($this->status ?? '') === 'open';
  }

  /**
   * Check if ticket is closed
   */
  public function isClosed(): bool
  {
    return in_array(strtolower($this->status ?? ''), ['closed', 'resolved', 'solved']);
  }

  /**
   * Check if ticket is trashed
   */
  public function isTrashed(): bool
  {
    return $this->trashed_datetime !== null;
  }

  /**
   * Check if ticket is snoozed
   */
  public function isSnoozed(): bool
  {
    return $this->snooze_datetime !== null && $this->snooze_datetime->isFuture();
  }

  public function getCreatedDateForHumansAttribute(): string
  {
    return $this->created_datetime ? $this->created_datetime->diffForHumans() : 'N/A';
  }

  public function parseDateFormat(string $datetime): string
  {
    $dt = \Carbon\Carbon::parse($datetime);
    return $dt ? $dt->format('d M, Y g:i A') : 'N/A';
  }

  /**
   * Sync ticket from Gorgias API response
   */
  public static function syncFromGorgias(array $ticket): self
  {
    // Extract custom fields
    $customFields = [];
    // if (isset($ticket['custom_fields']) && is_array($ticket['custom_fields'])) {
    //   foreach ($ticket['custom_fields'] as $fieldId => $fieldData) {
    //     // Try to find the field definition to get the managed_type
    //     $definition = \App\Models\TicketCustomFieldDefinition::query()
    //       ->where('gorgias_field_id', $fieldId)
    //       ->where('ticket_id', $ticket['id'])
    //       ->first();
    //     if ($definition && $definition->managed_type) {
    //       $customFields[$definition->managed_type] = $fieldData['value'] ?? null;
    //     }
    //   }
    // }

    $ticket = static::updateOrCreate(
      ['gorgias_ticket_id' => $ticket['id']],
      [
        'subject' => $ticket['subject'] ?? null,
        'summary' => $ticket['summary'] ?? null,
        'uri' => $ticket['uri'] ?? null,
        'status' => $ticket['status'] ?? null,
        'priority' => $ticket['priority'] ?? null,
        'requester_id' => $ticket['requester']['id'] ?? null,
        'requester_email' => $ticket['requester']['email'] ?? null,
        'requester_name' => $ticket['requester']['name'] ?? null,
        'requester_firstname' => $ticket['requester']['firstname'] ?? null,
        'requester_lastname' => $ticket['requester']['lastname'] ?? null,
        'assignee_user_id' => $ticket['assignee_user_id'] ?? null,
        'assignee_team_id' => $ticket['assignee_team_id'] ?? null,
        'is_unread' => $ticket['is_unread'] ?? false,
        'created_datetime' => $ticket['created_datetime'] ?? null,
        'opened_datetime' => $ticket['opened_datetime'] ?? null,
        'last_received_message_datetime' => $ticket['last_received_message_datetime'] ?? null,
        'last_message_datetime' => $ticket['last_message_datetime'] ?? null,
        'updated_datetime' => $ticket['updated_datetime'] ?? null,
        'closed_datetime' => $ticket['closed_datetime'] ?? null,
        'trashed_datetime' => $ticket['trashed_datetime'] ?? null,
        'snooze_datetime' => $ticket['snooze_datetime'] ?? null,
        'satisfaction_survey' => $ticket['satisfaction_survey'] ?? null,
        'custom_fields' => !empty($customFields) ? $customFields : null,
      ]
    );

    return $ticket;
  }
}
