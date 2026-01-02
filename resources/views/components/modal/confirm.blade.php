@props([
    'title' => 'Confirm Action',
    'body' => 'Are you sure you want to proceed?',
    'confirmButtonAction' => null,
    'confirmButtonText' => 'Delete',
    'confirmButtonClass' => 'btn-danger',
    'showCancelButton' => true,
    'cancelButtonText' => 'Cancel',
    'cancelButtonClass' => 'btn-secondary',
    'id' => 'deleteConfirmModal',
    'modalClass' => '',
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

<div class="modal fade {{ $modalClass }}" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Title" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header {{ $headerClass }}">
                <h5 class="modal-title" id="{{ $id }}Title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body {{ $bodyClass }}">
                {{ $body }}
            </div>
            <div class="modal-footer {{ $footerClass }}">
                @if ($showCancelButton)
                    <button type="button" class="btn {{ $cancelButtonClass }}" data-bs-dismiss="modal">
                        {{ $cancelButtonText }}
                    </button>    
                @endif
                <button type="button" class="btn {{ $confirmButtonClass }}" {!! $confirmButtonAction !!} data-bs-dismiss="modal">
                    {{ $confirmButtonText }}
                </button>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Show modal when the 'show' property becomes true
    $wire.on('show-modal-{{ $id }}', () => {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById(@js($id)));
        modal.show();
    });
    
    // Optional: Listen for hide event
    $wire.on('hide-modal-{{ $id }}', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById(@js($id)));
        if (modal) {
            modal.hide();
        }
    });
</script>
@endscript