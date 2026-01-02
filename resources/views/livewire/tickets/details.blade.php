<div wire:init="loadTicketData">

  @include('components.flash')

  @if ($loading)
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading ticket details...</span>
      </div>
      <p class="mt-3 text-muted">Loading ticket #{{ $ticketId }}...</p>
    </div>
  @elseif(!$ticket)
    <div class="alert alert-warning">
      <i class="ti ti-alert-triangle me-2"></i>
      Unable to load ticket details. Please try again.
    </div>
  @else
    <livewire:tickets.components.ticket-header :$ticket :$gorgiasTicketData :$tags :key="'ticket-header-' . $ticket->id" />
    <div class="row g-4">
      <div class="col-md-8">
        <livewire:tickets.components.customer-order-card :$ticket :$gorgiasTicketData :$customer :$customerOrders
          :$shopifyCustomer :key="'customer-card-' . $ticket->id" />
        <livewire:tickets.components.process-timeline :$ticket :$gorgiasTicketData :$isMisship :$messages
          :key="'timeline-' . $ticket->id" />
      </div>
      <div class="col-md-4">
        <div class="row py-4 g-4 chat-expand-row">
          <livewire:tickets.components.quick-actions :$ticket :$gorgiasTicketData :key="'quick-actions-' . $ticket->id" />
          <livewire:tickets.components.chat-box :$ticketId :$ticket :$customer :$messages :$messageType :$chatExpanded
            :$gorgiasTicketData :key="'chat-' . $ticket->id" />
        </div>
      </div>
    </div>

    @include('livewire.tickets.partials.offcanvas.customer-details')
    @include('livewire.tickets.partials.offcanvas.activity-logs')
    @include('livewire.tickets.partials.offcanvas.manage-tags')
    @include('livewire.tickets.partials.offcanvas.partial-refund')
    @include('livewire.tickets.partials.offcanvas.duplicate-order')
    @if (!$ticket->order)
      @include('livewire.tickets.partials.offcanvas.select-order')
    @else
      @include('livewire.tickets.partials.offcanvas.order-detail')
    @endif
  @endif
</div>
