<div
    x-data="weeklyPlanPicker(@js($availablePlanOptions), @js((string) ($selectedPlanId ?? '')))"
    class="space-y-3"
    data-weekly-okr-picker
>
    <label class="block">
        <span class="mb-1 block text-xs font-bold">Terkait target OKR mingguan</span>
        <input type="hidden" name="{{ $fieldName }}" :value="selected">
        <div class="relative">
            <input
                type="search"
                x-model="search"
                @focus="open = true"
                @input="open = true; selected = selectedPlan?.label === search ? selected : ''"
                @keydown.escape="open = false"
                placeholder="Cari kode KR atau nama target mingguan..."
                autocomplete="off"
                class="w-full rounded-md border-gray-300 pr-20 text-sm focus:border-blue-500 focus:ring-blue-500"
            >
            <button x-show="search" type="button" @click="clear()" class="absolute inset-y-0 right-2 px-2 text-[10px] font-black uppercase text-gray-400 hover:text-red-600">Hapus</button>
            <div
                x-cloak
                x-show="open"
                @click.outside="open = false"
                class="absolute z-30 mt-1 max-h-64 w-full overflow-y-auto rounded-md border border-gray-200 bg-white p-1 shadow-xl"
            >
                <button type="button" @mousedown.prevent="clear()" class="block w-full rounded px-3 py-2 text-left text-xs font-semibold text-gray-500 hover:bg-gray-50">Belum dikaitkan</button>
                <template x-for="plan in filteredPlans" :key="plan.id">
                    <button type="button" @mousedown.prevent="choose(plan)" class="block w-full rounded px-3 py-2.5 text-left hover:bg-blue-50">
                        <span class="block text-[10px] font-black uppercase text-emerald-600">Target Mingguan</span>
                        <span class="mt-0.5 block text-xs font-bold text-gray-900" x-text="plan.label"></span>
                    </button>
                </template>
                <p x-show="filteredPlans.length === 0" class="px-3 py-5 text-center text-xs text-gray-500">Target mingguan tidak ditemukan.</p>
            </div>
        </div>
        <span class="mt-1 block text-[10px] text-gray-500">Ketik kata kunci untuk mencari. Target bulanan dan tahunan ditampilkan sebagai informasi setelah target mingguan dipilih.</span>
    </label>

    <template x-if="selectedPlan">
        <div class="rounded-md border border-indigo-100 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <p class="text-[10px] font-black uppercase tracking-wider text-indigo-600">Alur Target OKR · Readonly</p>
                <span class="rounded-full bg-gray-100 px-2 py-1 text-[9px] font-black text-gray-500">INFORMASI</span>
            </div>
            <div class="mt-3 grid gap-2 lg:grid-cols-3">
                <template x-for="level in hierarchyLevels" :key="level.key">
                    <div class="rounded border p-3" :class="level.classes">
                        <p class="text-[9px] font-black uppercase" x-text="level.label"></p>
                        <p class="mt-1 text-xs font-bold leading-5 text-gray-900" x-text="level.data?.title || 'Belum memiliki induk target'"></p>
                        <p x-show="level.data?.period" class="mt-1 text-[10px] text-gray-500" x-text="level.data?.period"></p>
                        <p x-show="level.data" class="mt-2 text-[10px] font-black text-gray-600"><span x-text="level.data?.progress ?? 0"></span>% progres</p>
                        <p x-show="level.data?.indicator" class="mt-2 border-t border-current/10 pt-2 text-[10px] leading-4 text-gray-600"><span class="font-black">Indikator:</span> <span x-text="level.data?.indicator"></span></p>
                    </div>
                </template>
            </div>
            <div class="mt-2 grid gap-2 lg:grid-cols-2">
                <div class="rounded bg-gray-50 p-3"><p class="text-[9px] font-black uppercase text-gray-400">Key Result Sekolah</p><p class="mt-1 text-xs font-semibold text-gray-800"><span x-text="selectedPlan.key_result?.code"></span><span x-show="selectedPlan.key_result?.code"> · </span><span x-text="selectedPlan.key_result?.title || '-' "></span></p><p x-show="selectedPlan.key_result?.target" class="mt-1 text-[10px] font-black text-indigo-600">Target: <span x-text="selectedPlan.key_result?.target"></span></p></div>
                <div class="rounded bg-gray-50 p-3"><p class="text-[9px] font-black uppercase text-gray-400">Objektif Sekolah</p><p class="mt-1 text-xs font-semibold text-gray-800"><span x-text="selectedPlan.objective?.code"></span><span x-show="selectedPlan.objective?.code"> · </span><span x-text="selectedPlan.objective?.title || '-' "></span></p></div>
            </div>
        </div>
    </template>
</div>
