{{-- Resolution --}}
<x-offcanvas id="resolutionOffcanvas" title="Resolution">
  <x-slot:body>

    @php
      $grouped = [
          'ready_to_replace' => [],
          'full_refund' => [],
          'partial_refund' => [],
          'wait_for_restock' => [],
      ];

      foreach ($this->inventoryTask->productStatuses as $status) {
          if (!empty($status->action) && isset($grouped[$status->action])) {
              $grouped[$status->action][] = $status;
          }
      }

      $actionLabels = [
          'ready_to_replace' => 'Replacement',
          'full_refund' => 'Full Refund',
          'partial_refund' => 'Partial Refund',
          'wait_for_restock' => 'Wait for restock',
      ];
    @endphp

    @foreach ($grouped as $action => $statuses)
      @if (count($statuses))
        <div class="card px-5 py-0 shadow-sm mb-5">
          <div class="card-header border-0 px-0  pb-4 border-bottom border-dashed">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="m-0 fw-semibold text-dark">{{ $actionLabels[$action] }}:
                <span class="text-success-dark"> {{ count($statuses) }}
                  SKU{{ count($statuses) > 1 ? 's' : '' }}</span>
              </h5>
            </div>
          </div>
          <div class="card-body px-0 py-5">
            <div class="replacement-items-wrapper d-flex flex-column gap-4">

              @foreach ($statuses as $status)
                <div class="replacement-items">
                  <h6 class="mb-0 grey-g1">{{ $status->details['product_name'] ?? 'Unknown Product' }}</h6>
                  <span class="grey-g2 text-muted small">SKU {{ $status->details['sku'] ?? '' }}</span>
                </div>
              @endforeach

              @if ($action !== 'wait_for_restock')
                <button type="button"
                  class="btn btn-outline-dark w-auto d-flex align-items-center text-dark justify-content-center"
                  onclick="switchOffcanvas('resolutionOffcanvas', 'replacementJourneyOffcanvas')">
                  Start {{ strtolower($actionLabels[$action]) }}
                </button>
              @endif

              @if ($action === 'wait_for_restock')
                <div class="note-highight-bar d-flex align-items-center rounded-2 gap-4 p-4 mt-4 bg-grey-g5">
                  <div class="small text-dark"><span class="fw-semibold me-1">Note: </span>We will inform you once the
                    stock is available.</div>
                </div>
              @endif

            </div>
          </div>
        </div>
      @endif
    @endforeach
  </x-slot:body>
</x-offcanvas>
