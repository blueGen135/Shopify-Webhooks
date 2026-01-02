<x-offcanvas id="manageTagsOffcanvas" title="Manage Tags" wire:ignore.self>
  <x-slot:body>
    <div class="mb-3">
      <input type="text" class="form-control" placeholder="Search tags..." wire:model.live="tagSearchTerm">
    </div>

    <div class="d-flex flex-wrap gap-2">
      @forelse($this->filteredAvailableTags as $tag)
        @php
          $isSelected = in_array($tag['id'], $selectedTagIds);
          $color = $tag['decoration']['color'];
        @endphp
        <span wire:key="tag-{{ $tag['id'] }}"
          class="badge px-3 py-2 d-flex align-items-center gap-1 cursor-pointer bg-transparent text-dark border-secondary border-1 rounded-pill {{ $isSelected ? 'bg-success-subtle' : '' }}"
          style="{{ $isSelected ? 'border-color: ' . $color . '!important' : '' }}"
          wire:click="toggleTag({{ $tag['id'] }})">
          @if ($isSelected)
            <img src="{{ asset('assets/img/customizer/check-filled.svg') }}" alt="Selected" />
          @endif
          <span>{{ $tag['name'] }}</span>
        </span>
      @empty
        <div class="text-center text-muted py-3 w-100">
          @if ($tagSearchTerm)
            No tags found matching "{{ $tagSearchTerm }}"
          @else
            No tags available
          @endif
        </div>
      @endforelse
    </div>

  </x-slot:body>

  <x-slot:footer>
    <button type="button" class="btn btn-primary w-100" wire:click="saveTicketTags" data-bs-dismiss="offcanvas">
      <i class="ti tabler-check me-2"></i>Update Tags
    </button>
  </x-slot:footer>
</x-offcanvas>
