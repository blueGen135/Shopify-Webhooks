<?php

namespace App\Traits;

trait HasTableDeleteRow
{
    public ?int $selectedDelete = null;

    /**
     * Set the ID of the item to be deleted and show confirmation modal
     */
    public function setSelectedDelete(int $id): void
    {
        $this->selectedDelete = $id;
        $this->dispatch('show-modal-deleteConfirmModal');
    }

    /**
     * Handle the deletion confirmation
     * Calls validateDelete() and performDelete() which should be implemented in the component
     */
    public function confirmDelete(): void
    {
        // Run validation (implemented in component)
        if (method_exists($this, 'validateDelete')) {
            $validationError = $this->validateDelete();

            if ($validationError) {
                $this->dispatch('notify', type: 'error', message: $validationError);
                return;
            }
        }

        // Perform the actual deletion (implemented in component)
        $success = $this->performDelete();

        if ($success) {
            $this->afterDeleteSuccess();
        } else {
            $this->dispatch('notify', type: 'error', message: 'Failed to delete item.');
        }
    }

    /**
     * Actions to perform after successful deletion
     */
    protected function afterDeleteSuccess(): void
    {
        $this->selectedDelete = null;
        $this->dispatch('hide-modal-deleteConfirmModal');
        $this->dispatch('notify', type: 'success', message: $this->getDeleteSuccessMessage());
        $this->dispatch('refreshTable');
    }

    /**
     * Override this in your component to customize success message
     */
    protected function getDeleteSuccessMessage(): string
    {
        return 'Item deleted successfully.';
    }

    /**
     * Validate if deletion is allowed
     * Return null if valid, or error message string if invalid
     * 
     * MUST be implemented in your component
     */
    abstract protected function validateDelete(): ?string;

    /**
     * Perform the actual deletion
     * Return true if successful, false otherwise
     * 
     * MUST be implemented in your component
     */
    abstract protected function performDelete(): bool;
}
