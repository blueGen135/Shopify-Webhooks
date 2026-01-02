<?php

namespace App\Livewire\Tickets\Components;

use Livewire\Component;

class ProcessTimeline extends Component
{
  public $ticket;
  public $gorgiasTicketData;
  public $isMisship = false;
  public $messages = [];

  public function mount($ticket, $gorgiasTicketData, $isMisship, $messages = [])
  {
    $this->ticket = $ticket;
    $this->gorgiasTicketData = $gorgiasTicketData;
    $this->isMisship = $isMisship;
    $this->messages = $messages;
  }

  public function render()
  {
    return view('livewire.tickets.components.process-timeline');
  }
}
