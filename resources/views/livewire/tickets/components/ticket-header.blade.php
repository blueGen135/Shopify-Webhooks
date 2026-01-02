<div class="d-flex align-items-start mb-4 gap-4">
  <div class="flex-shrink-0">
    <h4 class="mb-1 fw-bold text-nowrap"><span class="text-muted fw-bold">TKT-</span>{{ $ticket->gorgias_ticket_id }}</h4>
    <p class="text-muted mb-0 text-nowrap">Created on: {{ $ticket->createdDateForHumans }}</p>
  </div>
  <div class="d-flex align-items-center gap-1 my-2 flex-wrap flex-grow-1">
    <span class="badge {{ $ticket->statusBadge }} px-3 py-2">{{ $ticket->statusForDisplay }}</span>
    @foreach ($tags as $tag)
      <span class="badge px-3 py-2"
        style="background-color:{{ $tag['decoration']['color'] ?? '#ff6900' }};">{{ $tag['name'] }}</span>
    @endforeach
    <button class="btn btn-sm btn-icon rounded bg-success-subtle" type="button" data-bs-toggle="offcanvas"
      data-bs-target="#manageTagsOffcanvas">
      <img src="{{ asset('assets/img/customizer/plus.svg') }}" alt="Plus Icon">
    </button>
  </div>
</div>
