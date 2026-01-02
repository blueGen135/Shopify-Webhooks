<div>
  {{-- Order summary offcanvas --}}
  <x-offcanvas id="replacementJourneyOffcanvas" title="New order summary" wire:ignore.self>
    <x-slot:body>
      @include('livewire.tickets.partials.replacement-journey.summary')
    </x-slot:body>

    <x-slot:footer>
      {{-- Add return labels --}}
      <div class="return-label-wrapper pb-2">
        <div class="return-label-field d-flex align-items-center p-1 bg-f4 rounded mb-3">
          <span type="text" class="small form-control grey-g1 border-0 me-3">Add return
            labels</span>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" id="addReturnLabel" wire:model.live="addReturnLabel">
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between gap-2 w-100">
        <button type="button"
          class="btn btn-outline-dark w-50 d-flex align-items-center justify-content-center py-2 rounded-3"
          wire:click="closeReplacement"
          data-bs-dismiss="offcanvas">
          Close
        </button>

        <button type="button"
          class="btn btn-dark w-50 d-flex align-items-center justify-content-center py-2 rounded-3 text-success"
          wire:click="proceedToCreateOrder"
          wire:target="proceedToCreateOrder"
          wire:loading.attr="disabled">
          <span
            wire:loading.class="spinner-border spinner-border-sm me-2"
            wire:target="proceedToCreateOrder"
            role="status" aria-hidden="true"></span>
          Confirm Order
        </button>

      </div>
    </x-slot:footer>
  </x-offcanvas>

  {{-- Create order steps --}}
  <x-offcanvas id="createOrderOffcanvas"
    title="{{ match ($createOrderStep) {
        'create_order' => 'Create new order',
        'order_created' => 'New order created',
        'send_email' => 'Send email',
        'email_sent' => 'Shipping labels generated',
        default => 'Proceed',
    } }}"
    {{-- data-bs-backdrop="static" --}}
    onClose="closeReplacementJourney"
    wire:ignore.self>
    <x-slot:body>
      @include('livewire.tickets.partials.replacement-journey.progress-bar')
      @include('livewire.tickets.partials.replacement-journey.create-order')
    </x-slot:body>

    <x-slot:footer>
      @if ($createOrderStep === 'order_created')
        <div class="return-label-wrapper pb-2">
          <div class="return-label-field d-flex align-items-center p-3 bg-f4 rounded mb-3">
            <div class="form-check form-switch m-0">
              <input
                class="form-check-input"
                type="checkbox"
                id="discountApplicable"
                wire:model.live="discountApplicable">
            </div>
            <span type="text" class="small form-control grey-g1 border-0 me-3">30% Discount</span>
            <a href="#"
              class="text-dark fw-semibold d-inline-flex justify-content-end w-100 align-items-center">
              Discount chart
              <i class="ti tabler-chevron-right ms-1 text-success"></i>
            </a>
          </div>
        </div>
      @endif

      <div class="d-flex justify-content-between gap-2 w-100">
        <button type="button"
          class="btn btn-outline-dark w-50 d-flex align-items-center justify-content-center py-2 rounded-3"
          wire:click="closeReplacementJourney"
          data-bs-dismiss="offcanvas">
          Close
        </button>

        <button type="button"
          class="btn btn-dark w-50 d-flex align-items-center justify-content-center py-2 rounded-3 text-success"
          wire:click="handleNextStep"
          wire:target="handleNextStep"
          wire:loading.attr="disabled">
          <span
            wire:loading.class="spinner-border spinner-border-sm me-2"
            wire:target="handleNextStep"
            role="status" aria-hidden="true"></span>
          {{ match ($createOrderStep) {
              'create_order' => 'Create new order',
              'order_created' => 'Send as email',
              'send_email' => 'Send email',
              'email_sent' => 'Save and proceed',
              default => 'Proceed',
          } }}
        </button>
      </div>
    </x-slot:footer>
  </x-offcanvas>

</div>
