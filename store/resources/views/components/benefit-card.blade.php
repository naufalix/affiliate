@props([
    'icon' => 'sparkles',
    'title',
    'iconColor' => 'primary',
])

@php
    $iconStyles = [
        'primary' => 'bg-primary-50 text-primary-600 group-hover:bg-primary-600 group-hover:text-white',
        'secondary' => 'bg-secondary-50 text-secondary-600 group-hover:bg-secondary-600 group-hover:text-white',
        'accent' => 'bg-accent-50 text-accent-600 group-hover:bg-accent-600 group-hover:text-white',
        'rose' => 'bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white',
        'amber' => 'bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white',
    ];

    $iconClasses = $iconStyles[$iconColor] ?? $iconStyles['primary'];
@endphp

<div
    {{ $attributes->merge([
        'class' => 'group relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover-lift',
    ]) }}
>
    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 transition-colors duration-300 {{ $iconClasses }}">
        <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
    </div>
    <h3 class="text-lg font-bold text-slate-900 mb-2 leading-snug">{{ $title }}</h3>
    <p class="text-sm text-slate-600 leading-relaxed">{{ $slot }}</p>
</div>
