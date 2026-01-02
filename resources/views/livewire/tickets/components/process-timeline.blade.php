{{-- Left Side: Process Timeline & Tasks --}}
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h5 class="mb-0">Process timeline</h5>
          <a href="javascript:void(0);" class="text-dark fw-semibold d-inline-flex align-items-center"
            data-bs-toggle="offcanvas" data-bs-target="#activityLogsOffcanvas">
            View activity
            <i class="ti tabler-chevron-right ms-1 text-success"></i>
          </a>
        </div>
        <div class="process-timeline d-flex align-items-center">
          <div class="process-step text-center">
            <div class="process-dot done">
              <i class="ti tabler-check text-white icon-base"></i>
            </div>
            <div class="process-label mt-2">Reported</div>
          </div>
          <div class="process-line done"></div>
          <div class="process-step text-center">
            <div class="process-dot current"></div>
            <div class="process-label mt-2 text-success">Verified</div>
          </div>
          <div class="process-line pending"></div>
          <div class="process-step text-center">
            <div class="process-dot pending"></div>
            <div class="process-label mt-2">Processing</div>
          </div>
          <div class="process-line pending"></div>
          <div class="process-step text-center">
            <div class="process-dot pending"></div>
            <div class="process-label mt-2">Resolved</div>
          </div>
        </div>
      </div>
    </div>

    <livewire:tickets.components.ticket-tasks :$ticket :$messages :key="'ticket-tasks-' . $ticket->id" />
  </div>
</div>
