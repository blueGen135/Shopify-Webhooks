<?php

namespace App\Livewire\Tickets\Components;

use Livewire\Component;
use App\Models\ProductTaskStatus;
use App\Models\TicketActivityLog;
use App\Traits\MessageHelper;
use App\Services\OdooService;

class TicketTasks extends Component
{
    use MessageHelper;

    public $ticket;
    public $messages = [];
    public $inventoryTask = null;
    public $verificationTask = null;
    public $inventoryTaskInitialized = false;
    public $verificationTaskInitialized = false;
    public $selectedProductForWarehouse = null;
    public $customerAgreed = false;

    public function mount($ticket, $messages = [])
    {
        $this->ticket = $ticket;
        $this->messages = $messages;

        // Check if verification task already has product statuses
        $this->verificationTask = $this->ticket->tasks()->with('productStatuses')->where('type', 'verification')->first();
        if ($this->verificationTask) {
            $this->initializeVerificationTask();
        }

        // Check if inventory task already has product statuses
        $this->inventoryTask = $this->ticket->tasks()->with('productStatuses')->where('type', 'inventory_check_resolution')->first();
        if ($this->inventoryTask) {
            $this->initializeInventoryTask();
            $this->customerAgreed = $this->inventoryTask->sub_tasks['customer_agreed'] ?? false;
        }
    }

    /**
     * Initialize product task status entries when verification task is opened
     */
    public function initializeVerificationTask()
    {
        $task = $this->verificationTask ?? 'null';

        if (!$task || !$this->ticket->order || empty($this->ticket->order->line_items)) {
            return;
        }

        // Check if already initialized
        $existingCount = $this->verificationTask->productStatuses()->count();
        if ($existingCount > 0) {
            $this->verificationTaskInitialized = true;
            return;
        }

        // Create ProductTaskStatus for each line item
        foreach ($this->ticket->order->line_items as $index => $item) {
            ProductTaskStatus::create([
                'task_id' => $task->id,
                'product_id' => $item['id'] ?? null,
                'action' => null,
                'details' => [
                    'product_name' => $item['name'] ?? $item['title'] ?? 'Unknown Product',
                    'sku' => $item['sku'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'line_item_index' => $index,
                ],
            ]);
        }

        $this->verificationTaskInitialized = true;
    }

    /**
     * Initialize inventory task with product statuses and inventory data
     */
    public function initializeInventoryTask()
    {
        $task = $this->inventoryTask ?? null;

        if (!$task || !$this->ticket->order || empty($this->ticket->order->line_items)) {
            return;
        }

        // Check if already initialized
        $existingCount = $this->inventoryTask->productStatuses()->count();
        if ($existingCount > 0) {
            $this->inventoryTaskInitialized = true;
            return;
        }

        $odooService = new OdooService();

        // Create ProductTaskStatus for each line item with inventory data
        foreach ($this->ticket->order->line_items as $index => $item) {
            $sku = $item['sku'] ?? 'DUMMY-' . $index;
            $inventoryData = $odooService->getInventorySummary($sku);

            ProductTaskStatus::create([
                'task_id' => $task->id,
                'product_id' => $item['id'] ?? null,
                'action' => null,
                'details' => [
                    'product_name' => $item['name'] ?? $item['title'] ?? 'Unknown Product',
                    'sku' => $sku,
                    'quantity' => $item['quantity'] ?? 1,
                    'line_item_index' => $index,
                    'inventory_data' => $inventoryData,
                ],
            ]);
        }

        $this->inventoryTaskInitialized = true;
    }

    /**
     * Update product action status
     */
    public function updateProductAction($type, $lineItemIndex, $action)
    {
        if ($type === 'verification') {
            $task = $this->verificationTask ?? null;
        } elseif ($type === 'inventory') {
            $task = $this->inventoryTask ?? null;
        } else {
            return;
        }

        if (!$task) {
            return;
        }

        // Find or create ProductTaskStatus
        $lineItem = $this->ticket->order->line_items[$lineItemIndex] ?? null;

        if (!$lineItem) {
            return;
        }

        $productStatus = $task->productStatuses()
            ->where('details->line_item_index', $lineItemIndex)
            ->first();

        if ($productStatus) {
            $oldAction = $productStatus->action;
            $productStatus->update(['action' => $action]);

            TicketActivityLog::logActivity(
                ticketId: $this->ticket->id,
                action: 'product_status_updated',
                title: "{$task->name}<br>Updated '{$productStatus->details['product_name']}' from '" . ucfirst(str_replace('_', ' ', $oldAction ?? 'None')) . "' to '" . ucfirst(str_replace('_', ' ', $action)) . "'",
                status: 'ongoing',
                metaData: [
                    'task_id' => $task->id,
                    'task_type' => $task->type,
                    'product_id' => $productStatus->product_id,
                    'sku' => $productStatus->details['sku'] ?? null,
                    'old_action' => $oldAction,
                    'new_action' => $action,
                ]
            );
        }

        // manually update the task updated_at timestamp so that child component can detect changes
        $task->updated_at = now();
        $task->save();

        // Refresh the task relationship to get updated product statuses
        $task->refresh()->load('productStatuses');

        // Update task status based on product completion
        if ($task->getCompletedProductsCount() === $task->productStatuses->count()) {
            $status = 'incomplete';
        } else if ($task->getIncompleteProductsCount() > 0 && $task->getCompletedProductsCount() > 0) {
            $status = 'ongoing';
        } else {
            $status = 'pending';
        }

        $task->update(['status' => $status]);

        session()->flash('success', 'Product status updated successfully.');
    }

    /**
     * Mark verification task as completed
     */
    public function markVerificationCompleted()
    {
        $task = $this->verificationTask;

        if (!$task) {
            session()->flash('error', 'Task not found.');
            return;
        }

        // Check if all products have an action selected
        if (!$task->allProductsCompleted()) {
            session()->flash('error', 'Please select status for all products before marking as verified.');
            return;
        }

        // if task is already completed, do nothing
        if ($task->status === 'completed') {
            session()->flash('info', 'Verification task is already marked as completed.');
            return;
        }

        // Update task status to completed
        $task->update(['status' => 'completed']);

        // Log activity
        TicketActivityLog::logActivity(
            ticketId: $this->ticket->id,
            action: 'task_completed',
            title: "{$task->name}<br>Verification task marked as completed",
            status: 'completed',
            metaData: [
                'task_id' => $task->id,
                'task_type' => $task->type,
            ]
        );

        session()->flash('success', 'Verification completed successfully.');
    }

    /**
     * Show warehouse details for a specific product
     */
    public function showWarehouseDetails($lineItemIndex)
    {
        if (!$this->inventoryTask) {
            return;
        }

        $productStatus = $this->inventoryTask->productStatuses()
            ->where('details->line_item_index', $lineItemIndex)
            ->first();

        if ($productStatus) {
            $this->selectedProductForWarehouse = $productStatus->details;
            $this->js('$("#warehouseDetailsModal").modal("show");');
        }
    }

    /**
     * Check if any product has wait_for_restock action
     */
    public function hasWaitForRestockAction()
    {
        if (!$this->inventoryTask) {
            return false;
        }

        return $this->inventoryTask->productStatuses()
            ->where('action', 'wait_for_restock')
            ->exists();
    }

    /**
     * Check if start resolution button should be enabled
     */
    public function canStartResolution()
    {
        if (!$this->inventoryTask) {
            return false;
        }

        // Check if all products have actions selected
        if (!$this->inventoryTask->allProductsCompleted()) {
            return false;
        }

        // If any product has wait_for_restock, customer must agree
        if ($this->hasWaitForRestockAction() && !$this->customerAgreed) {
            return false;
        }

        return true;
    }

    /**
     * Toggle customer agreed status
     */
    public function toggleCustomerAgreed()
    {
        $this->inventoryTask->update(['sub_tasks' => ['customer_agreed' => $this->customerAgreed]]);

        TicketActivityLog::logActivity(
            ticketId: $this->ticket->id,
            action: 'customer_agreement_updated',
            title: "{$this->inventoryTask->name}<br>Customer agreement status updated",
            status: 'ongoing',
            metaData: [
                'task_id' => $this->inventoryTask->id,
                'task_type' => $this->inventoryTask->type,
            ]
        );

        $this->inventoryTask->refresh();
    }

    /**
     * Compose email based on selected product actions
     */
    public function composeEmail()
    {
        if (!$this->inventoryTask || !$this->ticket->order) {
            return;
        }

        $productStatuses = $this->inventoryTask->productStatuses;
        $lineItems = $this->ticket->order->line_items;

        $readyToReplace = [];
        $waitForRestock = [];
        $fullRefund = [];
        $partialRefund = [];

        foreach ($productStatuses as $status) {
            $lineItemIndex = $status->details['line_item_index'] ?? null;
            if ($lineItemIndex !== null && isset($lineItems[$lineItemIndex])) {
                $product = [
                    'name' => $status->details['product_name'] ?? 'Unknown Product',
                    'sku' => $status->details['sku'] ?? 'N/A',
                    'quantity' => $status->details['quantity'] ?? 1,
                ];

                switch ($status->action) {
                    case 'ready_to_replace':
                        $readyToReplace[] = $product;
                        break;
                    case 'wait_for_restock':
                        $waitForRestock[] = $product;
                        break;
                    case 'full_refund':
                        $fullRefund[] = $product;
                        break;
                    case 'partial_refund':
                        $partialRefund[] = $product;
                        break;
                }
            }
        }

        // Build email content
        $email = "Dear Customer,\n\n";
        $email .= "Thank you for contacting us regarding your order. After reviewing your request and checking our inventory, here's the status of your items:\n\n";

        if (!empty($readyToReplace)) {
            $email .= "**Ready for Replacement:**\n";
            foreach ($readyToReplace as $product) {
                $email .= "- {$product['name']} (SKU: {$product['sku']}) - Quantity: {$product['quantity']}\n";
            }
            $email .= "These items are in stock and we can process a replacement immediately.\n\n";
        }

        if (!empty($waitForRestock)) {
            $email .= "**Awaiting Restock:**\n";
            foreach ($waitForRestock as $product) {
                $email .= "- {$product['name']} (SKU: {$product['sku']}) - Quantity: {$product['quantity']}\n";
            }
            $email .= "These items are currently out of stock. We expect to receive new inventory soon. Would you like to wait for the restock, or would you prefer a refund?\n\n";
        }

        if (!empty($fullRefund)) {
            $email .= "**Full Refund:**\n";
            foreach ($fullRefund as $product) {
                $email .= "- {$product['name']} (SKU: {$product['sku']}) - Quantity: {$product['quantity']}\n";
            }
            $email .= "We will process a full refund for these items.\n\n";
        }

        if (!empty($partialRefund)) {
            $email .= "**Partial Refund:**\n";
            foreach ($partialRefund as $product) {
                $email .= "- {$product['name']} (SKU: {$product['sku']}) - Quantity: {$product['quantity']}\n";
            }
            $email .= "We will process a partial refund for these items.\n\n";
        }

        $email .= "Please let us know how you would like to proceed, and we'll be happy to assist you further.\n\n";
        $email .= "Best regards,\n";
        $email .= "Customer Support Team";

        // Dispatch event to chat box component
        $this->dispatch('setMessageInput', message: $email);
    }

    public function render()
    {
        return view('livewire.tickets.components.ticket-tasks');
    }
}
