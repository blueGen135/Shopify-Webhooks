<?php

namespace App\Livewire\Tickets;

use App\Models\ShopifyCustomer;
use App\Models\Ticket;
use App\Models\TicketTask;
use App\Models\Order;
use App\Services\GorgiasService;
use App\Traits\MessageHelper;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Tickets Details')]
class Details extends Component
{
    use MessageHelper;

    public $ticket;
    public $ticketId;
    public $customer;
    public $tags = [];
    public $tasks = [];
    public $messages = [];
    public $loading = true;
    public $newMessage = '';
    public $gorgiasTicketData;
    public $isMisship = false;
    public $tagSearchTerm = '';
    public $availableTags = [];
    public $customerOrders = [];
    public $selectedTagIds = [];
    public $messagesUpdated = 0;
    public $chatExpanded = false;
    public $messageType = 'email';
    public $shopifyCustomer = null;

    public function mount($ticket)
    {
        $this->ticketId = $ticket;
    }

    public function loadTicketData($clearCache = false)
    {
        $this->loading = true;

        try {
            $this->loadDBTags();

            $this->ticket = Ticket::with('order')->with('tasks')->where('gorgias_ticket_id', $this->ticketId)->first();

            // Check cache first (5 minutes TTL)
            $cacheKey = "gorgias_ticket_{$this->ticketId}";

            if ($clearCache) {
                Cache::forget($cacheKey);
                Cache::forget("{$cacheKey}_messages");
            }

            $this->gorgiasTicketData = Cache::remember($cacheKey, 300, function () {
                return (new GorgiasService())->ticket($this->ticketId);
            });

            $this->tags = $this->gorgiasTicketData['tags'] ?? [];
            $this->selectedTagIds = array_column($this->tags, 'id');

            if (isset($this->gorgiasTicketData['messages'])) {
                $this->messages = $this->gorgiasTicketData['messages'];
            } else {
                $messagesResponse = Cache::remember("{$cacheKey}_messages", 300, function () {
                    return (new GorgiasService())->ticketMessages($this->ticketId);
                });
                $this->messages = $messagesResponse['data'] ?? [];
            }

            // Sort messages by created_datetime (oldest first)
            usort($this->messages, function ($a, $b) {
                return strtotime($a['created_datetime']) - strtotime($b['created_datetime']);
            });

            $this->messagesUpdated++;

            // Extract customer details from ticket data (no need for separate API call)
            if (isset($this->gorgiasTicketData['customer'])) {
                $this->customer = $this->gorgiasTicketData['customer'];

                if (empty($this->customer['name'])) {
                    $this->customer['name'] = 'Unknown Customer';
                }

                if (empty($this->customer['email'])) {
                    $this->customer['email'] = '';
                }

                // fetch customer orders+details from the database
                // $this->customer['email'] = 'dasdsa@dsad.dsa';
                $this->customerOrders = Order::where('customer_email', $this->customer['email'])
                    ->orderBy('shopify_created_at', 'desc')
                    ->get();

                $this->shopifyCustomer = ShopifyCustomer::where('email', $this->customer['email'])->first();
            }

            $this->setupTicketActions();
            $this->extractAndSetOrderFromMessages();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to load ticket details: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    #[On('refreshTicketMessages')]
    public function refreshTicketMessages()
    {
        // Only refresh messages, not the entire ticket data
        // Clear cache for messages to force fresh data
        try {
            $cacheKey = "gorgias_ticket_{$this->ticketId}";
            Cache::forget($cacheKey);
            Cache::forget("{$cacheKey}_messages");

            $gorgiasService = new GorgiasService();

            if (isset($this->gorgiasTicketData['messages'])) {
                $this->gorgiasTicketData = $gorgiasService->ticket($this->ticketId);
                $this->messages = $this->gorgiasTicketData['messages'];
                Cache::put($cacheKey, $this->gorgiasTicketData, 300);
            } else {
                $messagesResponse = $gorgiasService->ticketMessages($this->ticketId);
                $this->messages = $messagesResponse['data'] ?? [];
                Cache::put("{$cacheKey}_messages", $messagesResponse, 300);
            }

            usort($this->messages, function ($a, $b) {
                return strtotime($a['created_datetime']) - strtotime($b['created_datetime']);
            });

            $this->messagesUpdated++;
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to refresh messages: ' . $e->getMessage());
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|min:1',
        ]);

        try {
            $this->sendGorgiasMessage(
                ticketId: $this->ticketId,
                messageBody: $this->newMessage,
                options: [
                    'messageType' => $this->messageType,
                    'customerId' => $this->gorgiasTicketData['customer']['id'] ?? null,
                    'customerEmail' => $this->customer['email'] ?? '',
                    'customerName' => $this->customer['name'] ?? 'Customer',
                ]
            );

            // Refresh only messages
            $this->refreshTicketMessages();

            // Clear input
            $this->reset(['newMessage', 'messageType']);

            session()->flash('success', 'Message sent successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send message: ' . $e->getMessage());
        }
    }

    #[On('sendNewMessage')]
    public function sendNewMessage(string $message)
    {

    }

    public function getFilteredAvailableTagsProperty()
    {
        // Filter cached tags by search term
        if (strlen($this->tagSearchTerm) < 1) {
            return $this->availableTags;
        }

        return array_values(array_filter(
            $this->availableTags,
            fn($tag) => stripos($tag['name'], $this->tagSearchTerm) !== false
        ));
    }

    public function loadDBTags()
    {
        $this->availableTags = Cache::remember('all_tags', 3600, function () {
            return \App\Models\Tag::whereNull('gorgias_deleted_datetime')
                ->orderBy('name')
                ->get()
                ->map(fn($tag) => [
                    'id' => $tag->gorgias_tag_id,
                    'name' => $tag->name,
                    'decoration' => ['color' => $tag->color ?? '#ff6900']
                ])
                ->toArray();
        });
    }

    public function toggleTag($tagId)
    {
        if (in_array($tagId, $this->selectedTagIds)) {
            $this->selectedTagIds = array_values(array_filter(
                $this->selectedTagIds,
                fn($id) => $id !== $tagId
            ));
        } else {
            $this->selectedTagIds[] = $tagId;
        }
    }

    public function saveTicketTags()
    {
        try {
            $gorgiasService = new GorgiasService();

            // If no tags selected, delete all current tags
            if (empty($this->selectedTagIds)) {
                $currentTagIds = array_column($this->tags, 'id');
                if (!empty($currentTagIds)) {
                    $gorgiasService->deleteTicketTags($this->ticketId, ['ids' => $currentTagIds]);
                }
            } else {
                $gorgiasService->updateTicketTags($this->ticketId, ['ids' => $this->selectedTagIds]);
            }

            // Reload with fresh data
            $this->loadTicketData(clearCache: true);

            session()->flash('success', 'Tags updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update tags: ' . $e->getMessage());
        }
    }

    public function setupTicketActions()
    {
        $this->isMisship = collect($this->tags)->contains(function ($tag) {
            return isset($tag['name']) && strtolower($tag['name']) === 'misship';
        });

        if ($this->isMisship) {
            $existingTasks = $this->ticket->tasks()->count();

            if ($existingTasks === 0) {
                $this->ticket->tasks()->createMany([
                    ['type' => TicketTask::TYPE_VERIFICATION, 'status' => TicketTask::STATUS_PENDING, 'order' => 1],
                    ['type' => TicketTask::TYPE_INVENTORY_CHECK_RESOLUTION, 'status' => TicketTask::STATUS_PENDING, 'order' => 2],
                    ['type' => TicketTask::TYPE_CLOSE_TICKET, 'status' => TicketTask::STATUS_PENDING, 'order' => 3],
                ]);
            }

            $this->tasks = $this->ticket->tasks()->get()->toArray();
        }
    }

    /**
     * Extract order number from messages based on Q&A pattern.
     * Looks for "Please confirm the Order Number" followed by customer's answer.
     */
    public function extractAndSetOrderFromMessages()
    {
        // Skip if order already set
        if ($this->ticket->order_id) {
            return;
        }

        // Look for order number in messages
        $orderNumber = null;

        foreach ($this->messages as $message) {
            $bodyText = $message['body_text'] ?? $message['body_html'] ?? '';
            $messageLines = array_filter(explode("\n", $bodyText));

            foreach ($messageLines as $i => $line) {
                $line = trim($line);
                // Check if this message asks for order number confirmation
                if (stripos($line, 'Please confirm the Order Number') !== false) {
                    // Check next message for the answer (customer's response)
                    if (isset($messageLines[$i + 1])) {
                        $nextMessage = $messageLines[$i + 1];
                        preg_match('/\b[A-Z0-9\-]+\b/', $nextMessage, $matches);
                        if (!empty($matches)) {
                            $orderNumber = $matches[0];
                            break 2; // Exit both loops
                        }
                        break;
                    }
                }
            }
        }

        // If found, try to match with an order in the database
        if ($orderNumber) {
            $order = Order::query()
                ->where(function ($q) use ($orderNumber) {
                    $q->where('order_number', $orderNumber)
                        ->orWhere('shopify_order_id', $orderNumber);
                })
                ->where('customer_email', $this->shopifyCustomer ? $this->shopifyCustomer->email : $this->customer['email'])
                ->first();

            if ($order) {
                $this->ticket->update(['order_id' => $order->id]);
                $this->ticket->refresh();
            }
        }
    }

    public function selectOrder($orderId)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                session()->flash('error', 'Order not found.');
                return;
            }

            // Associate order with ticket
            $this->ticket->update(['order_id' => $orderId]);

            // Log activity
            $this->ticket->logActivity(
                'order_associated',
                "Order #{$order->order_number} associated with ticket",
                'completed',
                ['order_id' => $orderId, 'order_number' => $order->order_number]
            );

            // Reload the page to reflect changes
            return $this->redirect(route('tickets.details', ['ticket' => $this->ticket->gorgias_ticket_id]), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to associate order: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tickets.details');
    }
}