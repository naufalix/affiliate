@props([
    'variant' => 'info',
    'icon' => null,
])

@php
    $variants = [
        'success' => 'bg-secondary-50 text-secondary-700 border-secondary-100',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-100',
        'info' => 'bg-primary-50 text-primary-700 border-primary-100',
        'danger' => 'bg-rose-50 text-rose-700 border-rose-100',
        'category' => 'bg-secondary-50 text-secondary-700 border-secondary-100',
        'neutral' => 'bg-slate-100 text-slate-700 border-slate-200',
    ];

    $variantClasses = $variants[$variant] ?? $variants['info'];
@endphp

<span
    {{ $attributes->merge([
        'class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border tracking-wide {$variantClasses}",
    ]) }}
>
    @if ($icon)
        <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5"></i>
    @endif
    {{ $slot }}
</span>
