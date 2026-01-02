@props([
    'message' => '',
])

<div class="col-12 my-2 error-message">
    <p class="text-danger small">{{ $message }}</p>
</div>