@php
    $errorBag = ($errors ?? null) instanceof \Illuminate\Support\ViewErrorBag
        ? $errors
        : new \Illuminate\Support\ViewErrorBag();
    $alerts = [
        ['key' => 'success', 'class' => 'alert-success', 'icon' => 'fas fa-check-circle'],
        ['key' => 'error', 'class' => 'alert-danger', 'icon' => 'fas fa-circle-exclamation'],
        ['key' => 'warning', 'class' => 'alert-warning', 'icon' => 'fas fa-triangle-exclamation'],
        ['key' => 'info', 'class' => 'alert-info', 'icon' => 'fas fa-circle-info'],
    ];
@endphp

@if($errorBag->any() || collect($alerts)->contains(fn (array $alert) => session($alert['key'])))
    <div class="surface-frame">
        <div class="surface-alert-stack">
            @if($errorBag->any())
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <i class="fas fa-circle-exclamation mt-1"></i>
                        <div>
                            <strong>Please review the highlighted fields.</strong>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach($errorBag->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @foreach($alerts as $alert)
                @if(session($alert['key']))
                    <div class="alert {{ $alert['class'] }} alert-dismissible fade show mb-0" role="alert">
                        <div class="d-flex align-items-center gap-3">
                            <i class="{{ $alert['icon'] }}"></i>
                            <span>{{ session($alert['key']) }}</span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endif
