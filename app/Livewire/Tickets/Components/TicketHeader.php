<?php

namespace App\Livewire\Tickets\Components;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class TicketHeader extends Component
{
  #[Reactive]
  public $tags;
  public $ticket;
  public $gorgiasTicketData;

  public function mount($ticket, $tags, $gorgiasTicketData)
  {
    $this->ticket = $ticket;
    $this->tags = $tags;
    $this->gorgiasTicketData = $gorgiasTicketData;
  }

  public function render()
  {
    return view('livewire.tickets.components.ticket-header');
  }
}
