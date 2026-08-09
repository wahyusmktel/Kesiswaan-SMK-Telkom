<x-app-layout>
    <div class="py-12 w-full flex flex-col bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 w-full">
            
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">{{ isset($skill) ? 'Edit Keahlian' : 'Tambah Keahlian' }}</h2>
                    <p class="text-gray-500 font-medium mt-1">Tunjukkan tingkat keahlianmu kepada publik.</p>
                </div>
                <a href="{{ route('siswa.showcase.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
            </div>

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8 md:p-10">
                <form action="{{ isset($skill) ? route('siswa.showcase.skill.update', $skill->id) : route('siswa.showcase.skill.store') }}" method="POST">
                    @csrf
                    @if(isset($skill)) @method('PUT') @endif
                    
                    <div class="space-y-8">
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Skill / Keahlian <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $skill->name ?? '') }}" required class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Cth: Laravel, UI/UX Design, IoT">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div x-data="{ percentage: {{ old('percentage', $skill->percentage ?? 50) }} }">
                            <label for="percentage" class="block text-sm font-bold text-gray-700 mb-2">Tingkat Kemampuan <span class="text-red-500">*</span></label>
                            
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex-1">
                                    <input type="range" name="percentage" id="percentage" x-model="percentage" min="1" max="100" step="1" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                </div>
                                <div class="w-20 bg-blue-50 border border-blue-100 rounded-xl py-2 px-3 text-center">
                                    <span class="text-xl font-black text-blue-600" x-text="percentage + '%'"></span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between text-xs font-bold text-gray-400">
                                <span>Pemula</span>
                                <span>Menengah</span>
                                <span>Mahir</span>
                            </div>
                            
                            {{-- Visual Preview --}}
                            <div class="mt-6 p-4 border border-gray-100 rounded-2xl bg-gray-50">
                                <p class="text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Preview Tampilan</p>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="font-bold text-gray-700 text-sm" id="preview-name">{{ old('name', $skill->name ?? 'Nama Skill') }}</span>
                                    <span class="font-black text-blue-600 text-sm" x-text="percentage + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2.5 rounded-full transition-all duration-300 ease-out" :style="'width: ' + percentage + '%'"></div>
                                </div>
                            </div>

                            @error('percentage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('siswa.showcase.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-blue-600/30 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Skill
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    
    <script>
        document.getElementById('name').addEventListener('input', function(e) {
            document.getElementById('preview-name').textContent = e.target.value || 'Nama Skill';
        });
    </script>
</x-app-layout>
