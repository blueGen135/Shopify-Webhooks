<?php

namespace App\Livewire\Tickets\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class QuickActions extends Component
{
  public $ticket;
  public $gorgiasTicketData;
  public $chatExpanded = false;

  public function mount($ticket, $gorgiasTicketData)
  {
    $this->ticket = $ticket;
    $this->gorgiasTicketData = $gorgiasTicketData;
  }

  #[On('chatExpandToggled')]
  public function updateChatExpandState($expanded)
  {
    $this->chatExpanded = $expanded;
  }

  public function render()
  {
    return view('livewire.tickets.components.quick-actions');
  }
}
