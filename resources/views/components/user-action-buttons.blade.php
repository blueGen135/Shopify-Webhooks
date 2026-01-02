@props(['editUrl', 'deleteId'])

<div class="d-flex gap-2">
    <a href="{{ $editUrl }}" class="btn btn-sm btn-info" title="Edit">
        <i class="bi bi-pencil"></i>
        <span class="d-none d-md-inline"> Edit</span>
    </a>

    <button type="button" class="btn btn-sm btn-danger" wire:click="delete({{ $deleteId }})" onclick="return confirm('Are you sure you want to delete this user?')">
        <i class="bi bi-trash"></i>
        <span class="d-none d-md-inline"> Delete</span>
    </button>
</div>
