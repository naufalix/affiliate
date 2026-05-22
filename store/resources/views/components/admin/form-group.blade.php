@props([
    'label' => null,
    'for' => null,
    'name' => null,
    'hint' => null,
    'required' => false,
    'error' => null,
])

@php
    $errorMsg = $error ?? ($name ? ($errors->first($name) ?? null) : null);
    $controlId = $for ?? $name;
@endphp

<div {{ $attributes->class(['space-y-1.5']) }}>
    @if ($label)
        <label @if ($controlId) for="{{ $controlId }}" @endif class="block text-sm font-medium text-slate-700">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500" aria-hidden="true">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if ($hint && ! $errorMsg)
        <p class="text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @if ($errorMsg)
        <p class="text-xs text-rose-600">{{ $errorMsg }}</p>
    @endif
</div>
