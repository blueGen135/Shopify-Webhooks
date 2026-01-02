@php
  $width = $width ?? '32';
  $height = $height ?? '22';
@endphp

<span class="text-primary m-auto d-block text-center">
  <img src="{{ asset('assets/img/customizer/logo.png') }}" @class(['w-50' => !auth()->check()]) />
</span>
