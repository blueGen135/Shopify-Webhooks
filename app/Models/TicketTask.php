<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketTask extends Model
{
    // Task type constants
    const TYPE_VERIFICATION = 'verification';
    const TYPE_INVENTORY_CHECK_RESOLUTION = 'inventory_check_resolution';
    const TYPE_CLOSE_TICKET = 'close_ticket';

    // Task status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'ticket_id',
        'type',
        'status',
        'sub_tasks',
        'order',
        'completed_at',
    ];

    protected $casts = [
        'sub_tasks' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the task that owns this task.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the product statuses for this task.
     */
    public function productStatuses(): HasMany
    {
        return $this->hasMany(ProductTaskStatus::class, 'task_id');
    }

    /**
     * Get the human-readable name of the task.
     */
    public function getNameAttribute(): string
    {
        return self::getTaskTypes()[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Get available task types.
     */
    public static function getTaskTypes(): array
    {
        return [
            self::TYPE_VERIFICATION => 'Verification',
            self::TYPE_INVENTORY_CHECK_RESOLUTION => 'Inventory Check & Resolution',
            self::TYPE_CLOSE_TICKET => 'Close Ticket',
        ];
    }

    /**
     * Get available statuses.
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    /**
     * Get badge class based on status.
     */
    public function getBadgeClass(): string
    {
        return match ($this->status) {
            'completed' => 'bg-label-success',
            'ongoing', 'in_progress' => 'bg-label-ongoing',
            'incomplete' => 'bg-label-warning',
            default => 'pending-badge',
        };
    }

    /**
     * Get formatted status label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'in_progress' => 'In Progress',
            'incomplete' => 'Incomplete',
            'ongoing' => 'Ongoing',
            default => ucfirst($this->status ?? 'pending'),
        };
    }

    /**
     * Get count of products with completed status.
     */
    public function getCompletedProductsCount(): int
    {
        return $this->productStatuses->filter(fn($s) => !empty($s->action))->count();
    }

    /**
     * Get count of products with incomplete status.
     */
    public function getIncompleteProductsCount(): int
    {
        return $this->productStatuses->filter(fn($s) => empty($s->action))->count();
    }

    /**
     * Check if all products have status selected.
     */
    public function allProductsCompleted(): bool
    {
        $totalProducts = $this->productStatuses->count();
        return $totalProducts > 0 && $this->getIncompleteProductsCount() === 0;
    }

    /**
     * Check if task is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Mark task as completed.
     */
    public function markAsCompleted(): self
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Update a specific sub-task.
     */
    public function updateSubTask(string $key, mixed $value): self
    {
        $subTasks = $this->sub_tasks ?? [];
        $subTasks[$key] = $value;

        $this->update(['sub_tasks' => $subTasks]);

        return $this;
    }
}
