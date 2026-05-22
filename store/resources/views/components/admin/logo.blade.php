@props([
    'size' => 'md',
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'h-6 w-6 rounded-lg text-xs',
        'lg' => 'h-10 w-10 rounded-2xl text-base',
        default => 'h-8 w-8 rounded-xl text-sm', // 'md'
    };

    $initial = config('admin.logo_initial', 'F');
@endphp

<span {{ $attributes->class([
    'inline-flex items-center justify-center bg-gradient-to-br from-primary-500 to-secondary-500 text-white font-semibold',
    $sizeClasses,
]) }}>{{ $initial }}</span>
