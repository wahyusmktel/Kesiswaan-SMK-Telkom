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
                        <form method="POST" action="{{ route('penilaian.questions.store', $instrument) }}" class="space-y-3">
                            @csrf
                            <textarea name="question_text" required rows="3" placeholder="Pertanyaan" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                            <div class="grid sm:grid-cols-3 gap-3">
                                <select name="answer_type" class="rounded-lg border-gray-300 text-sm">
                                    @foreach($answerTypes as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                                </select>
                                <input name="max_score" type="number" value="5" min="1" max="100" class="rounded-lg border-gray-300 text-sm">
                                <input name="options_text" placeholder="Pilihan pisah | ; ," class="rounded-lg border-gray-300 text-sm">
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
</x-app-layout>
