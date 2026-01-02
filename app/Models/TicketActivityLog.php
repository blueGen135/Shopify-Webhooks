<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketActivityLog extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'action',
        'title',
        'status',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    /**
     * Get the ticket that owns this activity log.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log a new activity.
     */
    public static function logActivity(int $ticketId, string $action, ?string $title = null, ?string $status = null, ?array $metaData = null): self
    {
        return self::create([
            'ticket_id' => $ticketId,
            'user_id' => auth()->id(),
            'action' => $action,
            'title' => $title ?? ucwords(str_replace('_', ' ', $action)),
            'status' => $status,
            'meta_data' => $metaData,
        ]);
    }

    /**
     * Get the icon class for this action.
     */
    public function getIconClass(): string
    {
        return match ($this->action) {
            'task_completed' => 'ti tabler-check',
            'product_status_updated' => 'ti tabler-edit',
            default => 'ti tabler-info-circle',
        };
    }

    /**
     * Get the background color class for this action.
     */
    public function getBgClass(): string
    {
        return match ($this->action) {
            'task_completed' => 'bg-success',
            'product_status_updated' => 'bg-primary',
            default => 'bg-info',
        };
    }

    /**
     * Get the badge information for this action.
     */
    public function getBadgeInfo(): ?array
    {
        return match ($this->action) {
            'task_completed' => [
                'text' => 'Completed',
                'class' => 'bg-success-subtle text-success',
            ],
            'product_status_updated' => [
                'text' => 'Updated',
                'class' => 'bg-primary-subtle text-primary',
            ],
            default => null,
        };
    }
}
