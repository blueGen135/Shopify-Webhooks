<?php

namespace App\Livewire\Tickets\Components;

use App\Services\SmartAssistService;
use App\Traits\MessageHelper;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class ChatBox extends Component
{
  use MessageHelper;

  public $ticketId;
  public $ticket;
  public $customer;
  public $gorgiasTicketData;

  #[Reactive]
  public $messages = [];

  public $chatExpanded = false;

  #[Reactive]
  public $messageType = 'email';

  public $isInternalNote = false;
  public $searchVisible = false;
  public $searchTerm = '';

  public function mount($ticketId, $ticket, $customer, $messages, $messageType = 'email', $chatExpanded = false, $gorgiasTicketData = null)
  {
    $this->ticketId = $ticketId;
    $this->ticket = $ticket;
    $this->customer = $customer;
    $this->messages = $messages;
    $this->messageType = $messageType;
    $this->chatExpanded = $chatExpanded;
    $this->gorgiasTicketData = $gorgiasTicketData ?? [];
  }

  public function toggleExpand()
  {
    $this->chatExpanded = !$this->chatExpanded;
    $this->dispatch('chatExpandToggled', expanded: $this->chatExpanded)->to('tickets.details');
  }

  public function toggleSearch()
  {
    $this->searchVisible = !$this->searchVisible;
    if (!$this->searchVisible) {
      $this->searchTerm = '';
    }
  }

  public function clearSearch()
  {
    $this->searchTerm = '';
  }

  public function getFilteredMessagesProperty()
  {
    if (strlen($this->searchTerm) < 3) {
      return $this->messages;
    }

    return array_filter($this->messages, function ($message) {
      $bodyText = strip_tags($message['body_html'] ?? $message['body_text'] ?? '');
      return stripos($bodyText, $this->searchTerm) !== false;
    });
  }

  public function highlightText($text)
  {
    if (strlen($this->searchTerm) < 3) {
      return $text;
    }

    return preg_replace(
      '/(' . preg_quote($this->searchTerm, '/') . ')/i',
      '<mark class="bg-warning">$1</mark>',
      $text
    );
  }

  #[On('setMessageInput')]
  public function setMessageInput($message)
  {
    $this->dispatch('messageInputReceived', message: $message);
  }

  public function generateResponse()
  {
    try {
      $smartAssist = new SmartAssistService();

      // Generate response based on conversation history
      $response = $smartAssist->generateResponse($this->messages, [
        'temperature' => 0.7,
      ]);

      $this->setMessageInput($response);
    } catch (\Exception $e) {
      session()->flash('error', 'Failed to generate response: ' . $e->getMessage());
    }
  }

  public function render()
  {
    return view('livewire.tickets.components.chat-box');
  }
}
