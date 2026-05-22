@props([
    'tone' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $tones = [
        'info' => ['border' => 'border-primary-200', 'bg' => 'bg-primary-50', 'text' => 'text-primary-900', 'icon' => 'info', 'iconClass' => 'text-primary-500'],
        'success' => ['border' => 'border-secondary-200', 'bg' => 'bg-secondary-50', 'text' => 'text-secondary-900', 'icon' => 'check', 'iconClass' => 'text-secondary-600'],
        'warning' => ['border' => 'border-accent-200', 'bg' => 'bg-accent-50', 'text' => 'text-accent-900', 'icon' => 'alert-triangle', 'iconClass' => 'text-accent-600'],
        'error' => ['border' => 'border-rose-200', 'bg' => 'bg-rose-50', 'text' => 'text-rose-900', 'icon' => 'alert-triangle', 'iconClass' => 'text-rose-600'],
    ];
    $cfg = $tones[$tone] ?? $tones['info'];
@endphp

<div
    @if ($dismissible) x-data="{ shown: true }" x-show="shown" x-transition.opacity @endif
    {{ $attributes->class([
        'flex items-start gap-3 rounded-xl border px-4 py-3 text-sm',
        $cfg['border'],
        $cfg['bg'],
        $cfg['text'],
    ]) }}
    role="alert">
    <x-admin.icon :name="$cfg['icon']" :class="'h-4 w-4 mt-0.5 shrink-0 '.$cfg['iconClass']" />
    <div class="flex-1 min-w-0">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div @class(['mt-0.5' => $title])>{{ $slot }}</div>
    </div>
    @if ($dismissible)
        <button type="button" @click="shown = false" class="opacity-60 hover:opacity-100 transition" aria-label="Tutup">
            <x-admin.icon name="x" class="h-4 w-4" />
        </button>
    @endif
</div>
