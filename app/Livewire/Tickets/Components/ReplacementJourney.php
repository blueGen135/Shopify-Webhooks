<?php

namespace App\Livewire\Tickets\Components;

use App\Models\TicketActivityLog;
use App\Services\FedexService;
use App\Traits\MessageHelper;
use Livewire\Component;

class ReplacementJourney extends Component
{
    use MessageHelper;

    public $ticket;
    public $task;

    public $addReturnLabel = true;
    public $discountApplicable = true;

    public $shippingRates = [];
    public $actionProducts = null;
    public $selectedWarehouseId = null;

    public $emailBody = '';

    /**
     * !NOTE: Don't remove or modify this documentation without proper review.
     * Return Order Workflow – CSA Assisted Flow
     *
     * This class documents and manages the step-by-step return order process
     * to ensure consistency, clarity, and easy extensibility.
     *
     * Workflow Steps:
     *
     * 1. create_order
     *    - CSA selects the warehouse where the customer wants to receive the tires back.
     *
     * 2. order_created
     *    - Displays order confirmation with:
     *        • Generated order number
     *        • Selected warehouse
     *        • Generated return labels (only if "Add Return Label" is enabled)
     *
     * 3. send_email
     *    - Shows email preview to CSA before sending.
     *    - Email may include:
     *        • Return shipping labels
     *        • Order details
     *        • Discount coupon (if applicable)
     *
     * 4. email_sent
     *    - Final confirmation step.
     *    - Confirms email has been sent successfully.
     *    - Displays shipping labels for download.
     *
     * 5. proceed
     *    - Shows next steps to CSA/customer.
     *    - Triggers shipping label downloads where applicable.
     */

    public $createOrderStep = 'create_order'; // create_order, order_created, send_email, email_sent, proceed

    public function mount($ticket, $task)
    {
        $this->ticket = $ticket;
        $this->task = $task;
        $this->actionProducts = $task->productStatuses->where('action', 'ready_to_replace');
    }

    public function loadShippingRates()
    {
        $fedexService = new FedexService();

        // Prepare customer address
        $customerAddress = [
            'address1' => $this->ticket->order->shipping_address['address1'] ?? '',
            'city' => $this->ticket->order->shipping_address['city'] ?? '',
            'province_code' => $this->ticket->order->shipping_address['province_code'] ?? '',
            'zip' => $this->ticket->order->shipping_address['zip'] ?? '',
            'country_code' => $this->ticket->order->shipping_address['country_code'] ?? 'US',
            'latitude' => 37.4419, // Default for demo
            'longitude' => -122.1430,
        ];

        // Prepare items from action products
        $items = [];
        foreach ($this->actionProducts as $product) {
            $items[] = [
                'weight' => $product->details['weight'] ?? 25,
                'quantity' => $product->details['quantity'] ?? 1,
                'length' => 24,
                'width' => 24,
                'height' => 12,
            ];
        }

        $this->shippingRates = $fedexService->getShippingRates($customerAddress, $items);

        // Set the recommended warehouse as selected by default
        if (!empty($this->shippingRates)) {
            $this->selectedWarehouseId = $this->shippingRates[0]['warehouse_id'];
        }
    }

    public function proceedToCreateOrder()
    {
        if ($this->addReturnLabel) {
            $this->loadShippingRates();
        }

        $this->js("switchOffcanvas('replacementJourneyOffcanvas', 'createOrderOffcanvas');");
    }

    public function handleNextStep()
    {
        // Route to the appropriate method based on current step
        match ($this->createOrderStep) {
            'create_order' => $this->createOrder(),
            'order_created' => $this->orderCreated(),
            'send_email' => $this->sendEmail(),
            'email_sent' => $this->emailSent(),
            'save_and_proceed' => $this->saveAndProceed(),
            default => null
        };
    }

    public function createOrder()
    {
        // create the reverse order in Shopify
        // order created successfully, and now we will ask csa to send the email to customer

        // calculate the discount coupon based on provided excel sheet logic

        // show order created success message with order number
        // generated return labels (if any, depends on checkbox toggled)
        // show selected warehouse for return
        sleep(1);
        $this->createOrderStep = 'order_created';
    }

    public function orderCreated()
    {
        // show email preview to the CSA before sending
        // display generated tracking lables for the new order
        // save and proceed
        sleep(1);

        $this->emailBody = $this->generateReplacementEmailBody();

        $this->createOrderStep = 'send_email';
    }

    public function sendEmail()
    {
        try {
            // Prepare attachments (return labels, shipping labels, etc.)
            $attachmentPaths = [];

            if ($this->addReturnLabel) {
                // Add return label PDFs
                $attachmentPaths[] = storage_path('app/labels/dummy.pdf');
            }

            // Add tracking label PDFs
            $attachmentPaths[] = storage_path('app/labels/dummy.pdf');

            // Send email with attachments
            $this->sendGorgiasMessage(
                ticketId: $this->ticket->gorgias_ticket_id,
                messageBody: $this->emailBody,
                options: [
                    'messageType' => 'email',
                    'customerId' => $this->ticket->gorgiasTicketData['customer']['id'] ?? null,
                    'customerEmail' => $this->ticket->order->customer_email ?? '',
                    'customerName' => $this->ticket->order->customer_name ?? 'Customer',
                    'attachments' => $attachmentPaths,
                ]
            );

            $this->createOrderStep = 'email_sent';

            $this->dispatch('refreshTicketMessages');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Generate email body for replacement order
     */
    private function generateReplacementEmailBody(): string
    {
        $body = "Dear Customer,\n\n";
        $body .= "Your replacement order has been created successfully.\n\n";

        if ($this->addReturnLabel) {
            $body .= "Please find attached the return shipping labels for your original items.\n\n";
        }

        $body .= "Order Details:\n";
        foreach ($this->actionProducts as $product) {
            $body .= "- {$product->details['product_name']} (SKU: {$product->details['sku']})\n";
        }

        if ($this->discountApplicable) {
            $body .= "\nA 30% discount has been applied to your replacement order.\n";
        }

        $body .= "\nThank you for your patience.\n\n";
        $body .= "Best regards,\nCustomer Support Team";

        return $body;
    }

    public function emailSent()
    {
        // download the shipping labels
        sleep(1);
        $this->createOrderStep = 'save_and_proceed';
    }

    public function saveAndProceed()
    {
        $this->js('$("#createOrderOffcanvas").offcanvas("hide");');
        $this->closeReplacementJourney();
    }

    public function closeReplacementJourney()
    {
        $this->reset('createOrderStep', 'shippingRates', 'selectedWarehouseId');
    }

    public function completeReplacement()
    {
        // TODO: Implement the actual replacement logic here
        // This will create orders, refunds, etc. based on the action

        // Log the activity
        TicketActivityLog::logActivity(
            ticketId: $this->ticket->id,
            action: 'resolution_completed',
            metaData: [
                'resolution_action' => 'ready_to_replace',
                'notes' => $this->resolutionNotes,
                'timestamp' => now(),
            ],
        );

        $this->dispatch('resolution-completed');
        $this->closeResolution();

        session()->flash('success', 'Resolution completed successfully!');
    }

    public function render()
    {
        return view('livewire.tickets.components.replacement-journey');
    }
}
