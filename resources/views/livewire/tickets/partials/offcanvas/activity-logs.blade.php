<x-offcanvas id="activityLogsOffcanvas" title="Activity logs">
  <x-slot:body>
    @forelse ($ticket->activityLogs()->with('user')->latest()->get() as $index => $log)
      <div class="d-flex align-items-start mb-4">
        <div class="d-flex flex-column align-items-center me-3">
          <div
            class="{{ $log->getBgClass() }} rounded-circle d-flex align-items-center justify-content-center"
            style="width: 30px; height: 30px">
            <i class="{{ $log->getIconClass() }} text-white icon-base"></i>
          </div>
          @if (!$loop->last)
            <div class="flex-grow-1 border-start mt-2" style="min-height: 40px"></div>
          @endif
        </div>
        <div class="flex-grow-1">
          <div class="d-flex justify-content-between align-items-start mb-1">
            <div>
              <p class="mb-0 fw-semibold">{!! $log->title !!}</p>
              <p class="mb-0 text-muted small">
                by {{ $log->user->name ?? 'System' }} •
                {{ $log->created_at->format('jS M Y, g:i A') }}
              </p>
            </div>
            @if ($badge = $log->getBadgeInfo())
              <span class="badge {{ $badge['class'] }} fw-medium px-2 py-1">{{ $badge['text'] }}</span>
            @endif
          </div>

          @if (!empty($log->metadata))
            <div class="mt-2 p-2 bg-light rounded small">
              @if (isset($log->metadata['task_type']))
                <div><strong>Task:</strong> {{ ucfirst(str_replace('_', ' ', $log->metadata['task_type'])) }}</div>
              @endif
              @if (isset($log->metadata['sku']))
                <div><strong>SKU:</strong> {{ $log->metadata['sku'] }}</div>
              @endif
              @if (isset($log->metadata['old_action']) && isset($log->metadata['new_action']))
                <div>
                  <strong>Change:</strong>
                  <span
                    class="text-muted">{{ ucfirst(str_replace('_', ' ', $log->metadata['old_action'] ?? 'None')) }}</span>
                  <i class="ti ti-arrow-right mx-1"></i>
                  <span
                    class="text-primary fw-medium">{{ ucfirst(str_replace('_', ' ', $log->metadata['new_action'])) }}</span>
                </div>
              @endif
            </div>
          @endif
        </div>
      </div>
    @empty
      <div class="text-center py-5">
        <i class="ti ti-file-text text-muted mb-2" style="font-size: 48px;"></i>
        <p class="text-muted">No activity logs yet</p>
      </div>
    @endforelse
  </x-slot:body>

</x-offcanvas>
