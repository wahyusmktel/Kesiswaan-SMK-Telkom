<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('inventaris-aset.show', $asset['asset_id']) }}"
                class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gray-100 hover:bg-red-50 text-gray-500 hover:text-red-600 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-lg text-gray-800 leading-tight">Ajukan Peminjaman Aset</h2>
                <p class="text-xs text-gray-400 mt-0.5">Isi formulir untuk mengajukan peminjaman aset</p>
            </div>
        </div>
    </x-slot>

    @if($isDisposed)
    <div class="bg-gray-900 text-white rounded-2xl p-4 flex items-center gap-3 mb-4">
        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
        </svg>
        <p class="text-sm font-semibold">Aset ini sudah dimusnahkan dan tidak dapat dipinjam.</p>
    </div>
    @endif

    <div class="max-w-2xl mx-auto space-y-5">

        {{-- Info Aset --}}
        @if($asset)
        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-2xl p-5 text-white shadow-lg shadow-red-500/20">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-red-200 text-[10px] font-black uppercase tracking-widest">{{ $asset['asset_code_ypt'] ?? '-' }}</p>
                    <p class="text-base font-black">{{ $asset['name'] }}</p>
                    @if($asset['category'])
                    <p class="text-red-200 text-xs mt-0.5">{{ $asset['category'] }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Error API --}}
        @if($errors->has('api'))
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-sm font-semibold text-red-700">{{ $errors->first('api') }}</p>
        </div>
        @endif

        {{-- Form --}}
        @if($asset && !$isDisposed)
        <form action="{{ route('inventaris-aset.borrow', $asset['asset_id']) }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            @csrf

            {{-- Tujuan Peminjaman --}}
            <div>
                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                    Tujuan Peminjaman <span class="text-red-500">*</span>
                </label>
                <textarea name="purpose" rows="3" required maxlength="1000"
                    placeholder="Jelaskan tujuan peminjaman aset ini..."
                    class="w-full rounded-xl border border-gray-200 p-3 text-sm text-gray-700 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all resize-none @error('purpose') border-red-400 @enderror">{{ old('purpose') }}</textarea>
                @error('purpose')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Tanggal --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" required
                        min="{{ date('Y-m-d') }}"
                        value="{{ old('start_date', date('Y-m-d')) }}"
                        class="w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm text-gray-700 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all @error('start_date') border-red-400 @enderror">
                    @error('start_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                        Rencana Selesai
                    </label>
                    <input type="date" name="end_date"
                        min="{{ date('Y-m-d') }}"
                        value="{{ old('end_date') }}"
                        class="w-full rounded-xl border border-gray-200 py-2.5 px-3 text-sm text-gray-700 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all @error('end_date') border-red-400 @enderror">
                    @error('end_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Catatan --}}
            <div>
                <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">
                    Catatan Tambahan
                </label>
                <textarea name="notes" rows="2" maxlength="500"
                    placeholder="Catatan lain jika diperlukan (opsional)..."
                    class="w-full rounded-xl border border-gray-200 p-3 text-sm text-gray-700 focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all resize-none">{{ old('notes') }}</textarea>
            </div>

            {{-- Info pemohon --}}
            <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-700">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ session('active_role') }}</p>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3 pt-2">
                <a href="{{ route('inventaris-aset.show', $asset['asset_id']) }}"
                    class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-red-500/20 hover:-translate-y-0.5">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Kirim Permintaan Peminjaman
                    </span>
                </button>
            </div>
        </form>
        @endif

    </div>
</x-app-layout>
