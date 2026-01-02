<div class="bg-grey-g5 file-item d-flex align-items-center justify-content-between mb-2 p-4 rounded">
  <div class="d-flex align-items-center gap-3">
    <div class="file-icon">
      <img src="{{ asset('assets/img/customizer/file.svg') }}" class="w-7 h-7" alt="">
    </div>
    <div>
      <div class="fw-medium">{{ $name }}</div>
      <div class="text-muted small">{{ $meta }}</div>
    </div>
  </div>

  <div class="d-flex gap-3 pe-3">
    <a href="{{ $viewUrl }}" target="_blank">
      <img src="{{ asset('assets/img/customizer/eye.svg') }}" class="w-7 h-7" alt="">
    </a>
    <a href="{{ $downloadUrl }}" target="_blank" download>
      <img src="{{ asset('assets/img/customizer/download.svg') }}" class="w-7 h-7" alt="">
    </a>
  </div>
</div>
