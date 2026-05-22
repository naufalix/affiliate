<x-admin.card>
    <form method="POST" action="{{ route('admin.settings.bank-accounts.update') }}"
        x-data="{
            accounts: {{ json_encode(empty($bankAccounts) ? [] : $bankAccounts) }},
            addRow() {
                this.accounts.push({ bank: '', number: '', holder: '', logo_color: 'slate', primary: false });
            },
            removeRow(idx) {
                this.accounts.splice(idx, 1);
            },
        }">
        @csrf
        @method('PUT')

        <div class="space-y-3">
            <template x-for="(acc, idx) in accounts" :key="idx">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 rounded-xl border border-slate-100 bg-slate-50/50 p-3">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Bank</label>
                        <input type="text" :name="`bank_accounts[${idx}][bank]`" x-model="acc.bank" required
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Nomor</label>
                        <input type="text" :name="`bank_accounts[${idx}][number]`" x-model="acc.number" required
                            class="block w-full font-mono rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Atas nama</label>
                        <input type="text" :name="`bank_accounts[${idx}][holder]`" x-model="acc.holder"
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1">Warna logo</label>
                        <select :name="`bank_accounts[${idx}][logo_color]`" x-model="acc.logo_color"
                            class="block w-full rounded-xl border-slate-200 text-sm focus:border-primary-500 focus:ring-primary-500/40">
                            <option value="slate">Slate</option>
                            <option value="sky">Sky (BCA)</option>
                            <option value="amber">Amber (Mandiri)</option>
                            <option value="emerald">Emerald (BSI)</option>
                            <option value="red">Red (BRI)</option>
                            <option value="blue">Blue (BNI)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end gap-2">
                        <label class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 mb-2">
                            <input type="checkbox" :name="`bank_accounts[${idx}][primary]`" :value="1"
                                :checked="acc.primary" @change="acc.primary = $event.target.checked"
                                class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            Primary
                        </label>
                        <button type="button" @click="removeRow(idx)"
                            class="ml-auto inline-flex items-center gap-1 rounded-full border border-rose-200 bg-white px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50 transition">
                            <x-admin.icon name="trash" class="h-3 w-3" />
                            Hapus
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="accounts.length === 0" x-cloak class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">
                Belum ada rekening. Klik <span class="font-medium text-slate-700">Tambah Rekening</span> untuk mulai.
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between gap-3 pt-4 border-t border-slate-100">
            <button type="button" @click="addRow()"
                class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                <x-admin.icon name="plus" class="h-3.5 w-3.5" />
                Tambah Rekening
            </button>

            <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-full bg-primary-600 px-5 py-2 text-sm font-medium text-white shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition">
                Simpan semua rekening
            </button>
        </div>
    </form>
</x-admin.card>
