@props([
    'url' => null,
    'name' => 'File',
    'contentType' => 'application/octet-stream',
    'size' => null,
    'width' => '96px',
    'height' => '96px',
])

@php
  // Determine file type and icon
  $fileInfo = [
      'application/pdf' => ['icon' => 'tabler-file-type-pdf', 'label' => 'PDF'],
      'application/msword' => ['icon' => 'tabler-file-word', 'label' => 'DOC'],
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => [
          'icon' => 'tabler-file-word',
          'label' => 'DOCX',
      ],
      'application/vnd.ms-excel' => ['icon' => 'tabler-file-spreadsheet', 'label' => 'XLS'],
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => [
          'icon' => 'tabler-file-spreadsheet',
          'label' => 'XLSX',
      ],
      'application/vnd.ms-powerpoint' => ['icon' => 'tabler-file-chart', 'label' => 'PPT'],
      'application/vnd.openxmlformats-officedocument.presentationml.presentation' => [
          'icon' => 'tabler-file-chart',
          'label' => 'PPTX',
      ],
      'application/zip' => ['icon' => 'tabler-file-zip', 'label' => 'ZIP'],
      'application/x-rar-compressed' => ['icon' => 'tabler-file-zip', 'label' => 'RAR'],
      'text/plain' => ['icon' => 'tabler-file-text', 'label' => 'TXT'],
      'text/csv' => ['icon' => 'tabler-file-spreadsheet', 'label' => 'CSV'],
      'application/json' => ['icon' => 'tabler-file-code', 'label' => 'JSON'],
      'text/html' => ['icon' => 'tabler-file-code', 'label' => 'HTML'],
      'video/mp4' => ['icon' => 'tabler-video', 'label' => 'MP4'],
      'video/quicktime' => ['icon' => 'tabler-video', 'label' => 'MOV'],
      'audio/mpeg' => ['icon' => 'tabler-music', 'label' => 'MP3'],
      'audio/wav' => ['icon' => 'tabler-music', 'label' => 'WAV'],
  ];

  // Check for generic types
  if (!isset($fileInfo[$contentType])) {
      if (str_starts_with($contentType, 'video/')) {
          $fileInfo[$contentType] = ['icon' => 'tabler-video', 'label' => 'VIDEO'];
      } elseif (str_starts_with($contentType, 'audio/')) {
          $fileInfo[$contentType] = ['icon' => 'tabler-music', 'label' => 'AUDIO'];
      } elseif (str_starts_with($contentType, 'text/')) {
          $fileInfo[$contentType] = ['icon' => 'tabler-file-text', 'label' => 'TEXT'];
      } else {
          $fileInfo[$contentType] = ['icon' => 'tabler-file', 'label' => 'FILE'];
      }
  }

  $icon = $fileInfo[$contentType]['icon'];
  $label = $fileInfo[$contentType]['label'];

  // Format file size
  $formattedSize = null;
  if ($size) {
      if ($size < 1024) {
          $formattedSize = $size . ' B';
      } elseif ($size < 1024 * 1024) {
          $formattedSize = round($size / 1024, 1) . ' KB';
      } else {
          $formattedSize = round($size / (1024 * 1024), 1) . ' MB';
      }
  }
@endphp

<a
  href="{{ $url }}"
  target="_blank"
  {{ $attributes->merge([
      'class' =>
          'position-relative rounded-3 p-3 overflow-hidden bg-secondary-subtle border border-secondary-subtle d-flex flex-column justify-content-between text-decoration-none',
      'style' => "width: {$width}; height: {$height}; cursor: pointer;",
  ]) }}>

  <div class="d-flex flex-column" style="max-width: 100%;">
    <small class="text-dark fw-medium text-truncate" style="font-size: 0.75rem;" title="{{ $name }}">
      {{ $name }}
    </small>
    @if ($formattedSize)
      <small class="text-muted" style="font-size: 0.65rem;">{{ $formattedSize }}</small>
    @endif
  </div>

  <div class="position-absolute bottom-0 end-0 p-2">
    <span class="btn btn-dark btn-icon btn-sm rounded-circle">
      <i class="icon-base ti {{ $icon }} icon-14px"></i>
    </span>
  </div>
</a>
