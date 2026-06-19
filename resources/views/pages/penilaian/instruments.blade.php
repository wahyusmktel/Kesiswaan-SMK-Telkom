<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-xl text-gray-800">Seting Instrumen Penilaian</h2></x-slot>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
            <form method="GET" class="flex gap-2">
                <select name="period_id" class="rounded-lg border-gray-300 text-sm">
                    @foreach($periods as $item)
                        <option value="{{ $item->id }}" @selected($period?->id === $item->id)>{{ $item->title }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-bold">Pilih</button>
            </form>
            <a href="{{ route('penilaian.settings') }}" class="text-sm font-bold text-red-600">Seting Periode</a>
        </div>

        @if($period)
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-4">Tambah / Update Instrumen</h3>
                <form method="POST" action="{{ route('penilaian.instruments.store') }}" class="grid md:grid-cols-5 gap-3">
                    @csrf
                    <input type="hidden" name="assessment_period_id" value="{{ $period->id }}">
                    <select name="type" required class="rounded-lg border-gray-300 text-sm">
                        @foreach($types as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                    </select>
                    <input name="title" required placeholder="Judul instrumen" class="md:col-span-2 rounded-lg border-gray-300 text-sm">
                    <input name="description" placeholder="Deskripsi" class="rounded-lg border-gray-300 text-sm">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked class="rounded text-red-600"> Aktif</label>
                    <button class="md:col-span-5 rounded-lg bg-red-600 text-white font-bold py-2">Simpan Instrumen</button>
                </form>
            </div>

            @foreach($instruments as $instrument)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <p class="text-xs font-black text-red-600 uppercase tracking-widest">{{ $instrument->type_label }}</p>
                        <h3 class="font-bold text-gray-900">{{ $instrument->title }} <span class="text-gray-400">({{ $instrument->questions_count }} soal)</span></h3>
                    </div>
                    <div class="p-6 grid lg:grid-cols-2 gap-6">
                        <form method="POST" action="{{ route('penilaian.questions.store', $instrument) }}"
                            class="space-y-4"
                            x-data="assessmentQuestionBuilder()"
                            x-init="syncOptions()">
                            @csrf
                            <textarea name="question_text" required rows="3" placeholder="Pertanyaan" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                            <div class="grid sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Jenis Jawaban</label>
                                    <select name="answer_type" x-model="answerType" @change="resetForType()" class="w-full rounded-lg border-gray-300 text-sm">
                                    @foreach($answerTypes as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Skor Maksimal</label>
                                    <input name="max_score" type="number" value="5" min="1" max="100" class="w-full rounded-lg border-gray-300 text-sm">
                                </div>
                            </div>

                            <input type="hidden" name="options_text" :value="optionsText">

                            <div x-show="answerType === 'single_choice'" x-cloak class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-sm font-bold text-gray-800">Pilihan Jawaban Tunggal</p>
                                    <button type="button" @click="addOption()" class="w-8 h-8 rounded-lg bg-red-600 text-white font-bold hover:bg-red-700" title="Tambah pilihan">+</button>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(option, index) in options" :key="index">
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="options[index]" @input="syncOptions()" :required="answerType === 'single_choice'"
                                                :placeholder="'Pilihan ' + (index + 1)"
                                                class="flex-1 rounded-lg border-gray-300 text-sm">
                                            <button type="button" @click="removeOption(index)" x-show="options.length > 2"
                                                class="w-8 h-8 rounded-lg border border-gray-300 text-gray-600 hover:bg-white" title="Hapus pilihan">-</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div x-show="answerType === 'yes_no'" x-cloak class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <p class="text-sm font-bold text-gray-800 mb-3">Label Jawaban Ya / Tidak</p>
                                <div class="grid sm:grid-cols-2 gap-3">
                                    <input type="text" x-model="options[0]" @input="syncOptions()" :required="answerType === 'yes_no'" placeholder="Jawaban Ya"
                                        class="rounded-lg border-gray-300 text-sm">
                                    <input type="text" x-model="options[1]" @input="syncOptions()" :required="answerType === 'yes_no'" placeholder="Jawaban Tidak"
                                        class="rounded-lg border-gray-300 text-sm">
                                </div>
                            </div>

                            <div x-show="answerType === 'multiple_choice'" x-cloak class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="text-sm font-bold text-gray-800">Pilihan Jawaban Multi</p>
                                    <button type="button" @click="addOption()" class="w-8 h-8 rounded-lg bg-red-600 text-white font-bold hover:bg-red-700" title="Tambah pilihan">+</button>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="(option, index) in options" :key="index">
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="options[index]" @input="syncOptions()" :required="answerType === 'multiple_choice'"
                                                :placeholder="'Pilihan ' + (index + 1)"
                                                class="flex-1 rounded-lg border-gray-300 text-sm">
                                            <button type="button" @click="removeOption(index)" x-show="options.length > 2"
                                                class="w-8 h-8 rounded-lg border border-gray-300 text-gray-600 hover:bg-white" title="Hapus pilihan">-</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div x-show="answerType === 'text'" x-cloak class="rounded-lg border border-gray-200 p-4 bg-gray-50 text-sm text-gray-500">
                                Tipe teks/saran tidak membutuhkan pilihan jawaban. Penilai akan mengisi jawaban bebas saat melakukan penilaian.
                            </div>

                            <button class="rounded-lg bg-gray-900 text-white text-sm font-bold px-4 py-2">Tambah Soal</button>
                        </form>
                        <form method="POST" action="{{ route('penilaian.questions.import', $instrument) }}" enctype="multipart/form-data" class="rounded-lg bg-gray-50 border border-gray-200 p-4 space-y-3">
                            @csrf
                            <p class="text-sm font-bold text-gray-900">Import Excel</p>
                            <p class="text-xs text-gray-500">Kolom: pertanyaan, tipe, pilihan, skor_maksimal. Pilihan dipisah dengan tanda | atau ;.</p>
                            <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="w-full text-sm">
                            <button class="rounded-lg bg-green-600 text-white text-sm font-bold px-4 py-2">Import</button>
                        </form>
                    </div>
                    <div class="px-6 pb-6 space-y-2">
                        @foreach($instrument->questions as $question)
                            <div class="flex items-start justify-between gap-3 rounded-lg border border-gray-100 p-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $question->order + 1 }}. {{ $question->question_text }}</p>
                                    <p class="text-xs text-gray-500">{{ $answerTypes[$question->answer_type] ?? $question->answer_type }} | Skor maks {{ $question->max_score }}</p>
                                </div>
                                <form method="POST" action="{{ route('penilaian.questions.destroy', $question) }}">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-bold text-red-600">Hapus</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    @push('scripts')
        <script>
            function assessmentQuestionBuilder() {
                return {
                    answerType: 'yes_no',
                    options: ['Ya', 'Tidak'],
                    optionsText: 'Ya|Tidak',
                    resetForType() {
                        if (this.answerType === 'yes_no') {
                            this.options = ['Ya', 'Tidak'];
                        } else if (this.answerType === 'single_choice') {
                            this.options = ['Pilihan 1', 'Pilihan 2'];
                        } else if (this.answerType === 'multiple_choice') {
                            this.options = ['Pilihan 1', 'Pilihan 2'];
                        } else {
                            this.options = [];
                        }
                        this.syncOptions();
                    },
                    addOption() {
                        this.options.push('');
                        this.syncOptions();
                    },
                    removeOption(index) {
                        if (this.options.length <= 2) return;
                        this.options.splice(index, 1);
                        this.syncOptions();
                    },
                    syncOptions() {
                        this.optionsText = this.answerType === 'text'
                            ? ''
                            : this.options.map((option) => String(option).trim()).filter(Boolean).join('|');
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
