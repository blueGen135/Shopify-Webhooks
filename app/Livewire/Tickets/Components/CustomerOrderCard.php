<?php

namespace App\Livewire\Tickets\Components;

use Livewire\Component;

class CustomerOrderCard extends Component
{
  public $ticket;
  public $customer;
  public $customerOrders;
  public $shopifyCustomer;
  public $gorgiasTicketData;

  public function mount($ticket, $gorgiasTicketData, $customer, $customerOrders, $shopifyCustomer)
  {
    $this->ticket = $ticket;
    $this->gorgiasTicketData = $gorgiasTicketData;
    $this->customer = $customer;
    $this->customerOrders = $customerOrders;
    $this->shopifyCustomer = $shopifyCustomer;
  }

  public function render()
  {
    return view('livewire.tickets.components.customer-order-card');
  }
}
