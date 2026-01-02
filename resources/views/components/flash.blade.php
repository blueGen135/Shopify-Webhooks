@php
    // Allow include callers to pass $dismissible variable: @include('components.flash', ['dismissible' => false])
    $dismissible = isset($dismissible) ? (bool) $dismissible : true;

    $levels = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        'status' => 'alert-primary',
    ];

    // Collect session flash messages
    $messages = [];
    foreach (array_keys($levels) as $level) {
        if (session()->has($level)) {
            $messages[$level] = session()->get($level);
        }
    }

    // Also support default 'message' key
    if (empty($messages) && session()->has('message')) {
        $messages['info'] = session('message');
    }
@endphp

@foreach($messages as $level => $message)
    <div class="alert {{ $levels[$level] ?? 'alert-info' }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
        {!! $message !!}
        @if($dismissible)
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        @endif
    </div>
@endforeach
