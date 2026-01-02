<div class="card">
  <div class="card-header">
    <div {{ $attributes->merge(['class' => 'table-actions-header d-flex justify-content-between gap-2']) }}>
      <div class="">
        {{ $left }}
      </div>

      <div class="">
        {{ $right }}
      </div>
    </div>
  </div>
  <div class="card-body">
    {{ $body }}
  </div>
</div>
