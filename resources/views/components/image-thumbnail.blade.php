@props([
    'url',
    'name' => 'Image',
    'index' => null,
    'wireKey' => null,
    'width' => '96px',
    'height' => '96px',
    'showBadge' => false,
    'badgeText' => null,
    'swipebox' => false,
    'swipeboxRel' => null,
])

<div class="position-relative rounded-3 overflow-hidden"
  style="width: {{ $width }}; height: {{ $height }}; background: #222; cursor: pointer;"
  @if ($wireKey) wire:key="{{ $wireKey }}" @endif>

  @if ($swipebox)
    <a href="{{ $url }}" class="swipebox"
      @if ($swipeboxRel) rel="{{ $swipeboxRel }}" @endif
      title="{{ $name }}">
      <img src="{{ $url }}" class="img-fluid w-100 h-100 object-fit-cover"
        alt="{{ $name }}" loading="lazy" />
    </a>
  @else
    <a href="{{ $url }}" target="_blank">
      <img src="{{ $url }}" class="img-fluid w-100 h-100 object-fit-cover"
        alt="{{ $name }}" loading="lazy" />
    </a>
  @endif

  @if ($showBadge)
    <div class="position-absolute top-0 start-0 w-100 px-2 pt-1 d-flex justify-content-between">
      <span class="badge bg-dark bg-opacity-75 text-white border-0 px-2 py-1 d-inline-flex align-items-center">
        <i class="icon-base ti tabler-photo icon-16px me-1"></i>
        {{ $badgeText ?? 'IMG_' . ($index + 1) }}
      </span>
    </div>
  @endif

  <div class="position-absolute bottom-0 end-0 p-2" onclick="event.stopPropagation();">
    <a href="{{ $url }}" download="{{ $name }}"
      class="btn btn-dark btn-icon btn-sm rounded-circle" target="_blank"
      onclick="event.preventDefault(); event.stopPropagation(); window.open(this.href, '_blank'); return false;">
      <i class="icon-base ti tabler-download icon-14px"></i>
    </a>
  </div>
</div>
