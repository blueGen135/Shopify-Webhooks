@props(['id', 'title', 'position' => 'end', 'onClose' => null])

<div>
  <div {{ $attributes->merge(['class' => "offcanvas offcanvas-{$position} m-0", 'tabindex' => '-1', 'id' => $id]) }}>
    <div class="offcanvas-header border-bottom">
      @if (isset($header))
        {{ $header }}
      @else
        <h5 class="offcanvas-title fw-semibold text-dark">{{ $title }}</h5>
      @endif
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      {{ $body }}
    </div>
    @if (isset($footer) && !empty($footer))
      <div class="offcanvas-footer border-top p-3">
        {{ $footer }}
      </div>
    @endif
  </div>
  <div wire:ignore class="custom-backdrop"></div>
</div>

@if ($onClose)
  @script
    <script>
      const offcanvasEl = document.getElementById('{{ $id }}');
      offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
        $wire.call('{{ $onClose }}');
      });
    </script>
  @endscript
@endif
