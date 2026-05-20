@extends('layouts.admin', ['active' => 'wa-notifications'])

@section('title', 'WA Notifikasi · Admin')

@section('content')
    <x-admin.page-header
        title="WA Notifikasi"
        subtitle="Log antrean notifikasi WhatsApp (M2 stub — gateway sender M3+).">
        <x-slot:actions>
            <span class="text-xs text-slate-500">{{ $stats['total'] }} total</span>
        </x-slot:actions>
    </x-admin.page-header>

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>
                {{ session('status') }}
            </x-admin.alert>
        </div>
    @endif

    {{-- Stat strip per-status --}}
    <section class="grid grid-cols-2 gap-3 mb-6 sm:grid-cols-4">
        <div class="rounded-xl border border-amber-100 bg-amber-50 px-3 py-2.5">
            <div class="text-xs text-amber-700">Queued</div>
            <div class="mt-1 text-lg font-semibold text-amber-900" data-testid="stat-queued">{{ $stats['queued'] }}</div>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2.5">
            <div class="text-xs text-emerald-700">Sent</div>
            <div class="mt-1 text-lg font-semibold text-emerald-900" data-testid="stat-sent">{{ $stats['sent'] }}</div>
        </div>
        <div class="rounded-xl border border-rose-100 bg-rose-50 px-3 py-2.5">
            <div class="text-xs text-rose-700">Failed</div>
            <div class="mt-1 text-lg font-semibold text-rose-900" data-testid="stat-failed">{{ $stats['failed'] }}</div>
        </div>
        <div class="rounded-xl border border-slate-100 bg-white px-3 py-2.5">
            <div class="text-xs text-slate-500">Total</div>
            <div class="mt-1 text-lg font-semibold text-slate-900">{{ $stats['total'] }}</div>
        </div>
    </section>

    {{-- Filter form --}}
    <x-admin.card class="mb-6" :padded="false">
        <form method="GET" action="{{ route('admin.wa-notifications.index') }}" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <label for="search" class="sr-only">Cari</label>
                <input
                    id="search"
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari recipient, order number, atau nama customer..."
                    class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
            </div>

            <div>
                <label for="status" class="sr-only">Status</label>
                <select id="status" name="status" class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
                    <option value="">Semua status</option>
                    @foreach (['queued', 'sent', 'failed'] as $s)
                        <option value="{{ $s }}" @selected($statusFilter === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <select name="template" class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
                    <option value="">Semua template</option>
                    @foreach ($templates as $t)
                        <option value="{{ $t }}" @selected($templateFilter === $t)>{{ $t }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                    Filter
                </button>
            </div>
        </form>
    </x-admin.card>

    {{-- Notification list --}}
    <x-admin.card :padded="false">
        @if ($notifications->isEmpty())
            <div class="px-6 py-12 text-center" data-testid="empty-state">
                <div class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                    <x-admin.icon name="message-square" class="h-5 w-5 text-slate-400" />
                </div>
                <p class="text-sm text-slate-500">Belum ada notifikasi WhatsApp.</p>
                <p class="mt-1 text-xs text-slate-400">Notifikasi akan ke-queue otomatis saat upload bukti, verifikasi, atau input resi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100" data-testid="wa-notifications-table">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-xs font-medium uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Template</th>
                            <th class="px-4 py-3">Recipient</th>
                            <th class="px-4 py-3">Order</th>
                            <th class="px-4 py-3">Queued</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm">
                        @foreach ($notifications as $notif)
                            @php
                                $toneMap = ['queued' => 'amber', 'sent' => 'emerald', 'failed' => 'rose'];
                                $tone = $toneMap[$notif->status] ?? 'slate';
                            @endphp
                            <tr data-testid="wa-notif-row" data-id="{{ $notif->id }}" data-status="{{ $notif->status }}">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-{{ $tone }}-50 text-{{ $tone }}-700">
                                        {{ ucfirst($notif->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ $notif->template }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-slate-700">{{ $notif->recipient }}</td>
                                <td class="px-4 py-3">
                                    @if ($notif->order)
                                        <a href="{{ route('admin.orders.show', $notif->order) }}" class="text-primary-700 hover:text-primary-900">
                                            {{ $notif->order->order_number }}
                                        </a>
                                        <div class="text-xs text-slate-500">{{ $notif->order->customer_name }}</div>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500" title="{{ $notif->created_at }}">
                                    {{ $notif->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-4 py-3">
                {{ $notifications->links() }}
            </div>
        @endif
    </x-admin.card>
@endsection
