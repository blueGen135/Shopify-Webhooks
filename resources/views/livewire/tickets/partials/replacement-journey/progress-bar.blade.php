@php
  // Map createOrderStep to progress bar steps
  $stepMapping = [
      'create_order' => 1,
      'order_created' => 2,
      'send_email' => 2,
      'email_sent' => 3,
      'save_and_proceed' => 4,
  ];

  $currentStepOrder = $stepMapping[$createOrderStep] ?? 1;

  $progressSteps = [
      ['label' => 'New Order', 'order' => 1],
      ['label' => 'Send Email', 'order' => 2],
      ['label' => 'Shipping Labels', 'order' => 3],
  ];
@endphp

<div class="process-timeline d-flex align-items-center pb-5">
  @foreach ($progressSteps as $step)
    @if (!$loop->first)
      <div
        class="process-line {{ $step['order'] < $currentStepOrder ? 'done' : ($step['order'] == $currentStepOrder ? 'current' : '') }}">
      </div>
    @endif

    <div class="process-step text-center">
      <div
        class="process-dot {{ $step['order'] < $currentStepOrder ? 'done' : ($step['order'] == $currentStepOrder ? 'current' : 'pending') }}">
        @if ($step['order'] < $currentStepOrder)
          <i class="ti tabler-check text-white icon-base"></i>
        @endif
      </div>
      <div class="process-label mt-2 {{ $step['order'] == $currentStepOrder ? 'text-success' : '' }}">
        {{ $step['label'] }}
      </div>
    </div>
  @endforeach
</div>
