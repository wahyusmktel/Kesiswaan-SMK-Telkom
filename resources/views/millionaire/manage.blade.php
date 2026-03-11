@extends('notted.app')

@section('content')
<div class="col-span-1 lg:col-span-9 flex flex-col gap-6" x-data="millionaireManage()">
    <div class="bg-white rounded-[32px] p-8 border border-slate-200 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Manajemen Soal <span class="text-indigo-600">Millionaire</span></h1>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Buat dan kelola kategori serta pertanyaan game.</p>
            </div>
            <button @click="showSetModal = true" class="px-6 py-3 notted-gradient text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:scale-[1.02] transition-all">
                Tambah Kategori
            </button>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl text-sm font-bold animate-in fade-in slide-in-from-top-2">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid gap-4">
            @forelse($sets as $set)
            <div class="bg-slate-50 rounded-3xl border border-slate-100 overflow-hidden">
                <div class="p-6 flex justify-between items-center bg-white border-b border-slate-100">
                    <div>
                        <h3 class="font-black text-slate-800 uppercase tracking-tight">{{ $set->name }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase">{{ $set->questions->count() }} Pertanyaan Terdaftar</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editSet({{ $set }})" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-white rounded-xl transition-all shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('notted.millionaire.manage.set.destroy', $set->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini beserta semua soalnya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-white rounded-xl transition-all shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($set->questions->sortBy('level') as $q)
                        <div class="flex items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-100 group shadow-sm transition-all hover:bg-indigo-50/30">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs font-black">
                                    {{ $q->level }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700 line-clamp-1">{{ $q->question }}</p>
                                    <p class="text-[10px] font-bold text-emerald-600 uppercase mt-0.5 tracking-widest">Jawaban: {{ $q->correct_answer }}) {{ $q->{'option_'.strtolower($q->correct_answer)} }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="editQuestion({{ $q }})" class="p-1.5 text-slate-400 hover:text-indigo-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('notted.millionaire.manage.question.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus pertanyaan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button @click="addQuestion({{ $set->id }})" class="w-full mt-4 py-3 border-2 border-dashed border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:border-indigo-400 hover:text-indigo-600 hover:bg-white transition-all">
                        + Tambah Pertanyaan Level {{ $set->questions->count() + 1 }}
                    </button>
                </div>
            </div>
            @empty
            <div class="text-center py-12 px-4 border-2 border-dashed border-slate-200 rounded-[40px] bg-slate-50/50">
                <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Belum ada kategori yang Anda buat.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Set Soal -->
    <div x-show="showSetModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div @click.away="showSetModal = false" class="bg-white rounded-[40px] p-8 w-full max-w-lg shadow-2xl relative">
            <h2 class="text-2xl font-black text-slate-900 mb-6 uppercase tracking-tight" x-text="editModeSet ? 'Update Kategori' : 'Kategori Baru'"></h2>
            <form :action="editModeSet ? `/notted/millionaire/manage/set/${formDataSet.id}` : '{{ route('notted.millionaire.manage.set.store') }}'" method="POST">
                @csrf
                <template x-if="editModeSet">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Nama Kategori</label>
                        <input type="text" name="name" x-model="formDataSet.name" required placeholder="Contoh: Pengetahuan Umum IT" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Deskripsi</label>
                        <textarea name="description" x-model="formDataSet.description" placeholder="Jelaskan tentang apa soal-soal ini..." class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-indigo-600 h-32"></textarea>
                    </div>
                </div>
                <div class="flex gap-4 mt-8">
                    <button type="button" @click="showSetModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 notted-gradient text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-100 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pertanyaan -->
    <div x-show="showQuestionModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div @click.away="showQuestionModal = false" class="bg-white rounded-[40px] p-8 w-full max-w-2xl shadow-2xl overflow-y-auto max-h-[90vh] scrollbar-hide">
            <h2 class="text-2xl font-black text-slate-900 mb-6 uppercase tracking-tight" x-text="editModeQuestion ? 'Update Pertanyaan' : 'Tambah Pertanyaan'"></h2>
            <form :action="editModeQuestion ? `/notted/millionaire/manage/question/${formDataQuestion.id}` : '{{ route('notted.millionaire.manage.question.store') }}'" method="POST">
                @csrf
                <template x-if="editModeQuestion">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <input type="hidden" name="set_id" :value="formDataQuestion.set_id">
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Level (1-15)</label>
                            <input type="number" name="level" x-model="formDataQuestion.level" required min="1" max="15" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <div class="flex-1">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Jawaban Benar</label>
                            <select name="correct_answer" x-model="formDataQuestion.correct_answer" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-indigo-600">
                                <option value="A">Opsi A</option>
                                <option value="B">Opsi B</option>
                                <option value="C">Opsi C</option>
                                <option value="D">Opsi D</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Pertanyaan</label>
                        <textarea name="question" x-model="formDataQuestion.question" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-indigo-600 h-24"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Opsi A</label>
                            <input type="text" name="option_a" x-model="formDataQuestion.option_a" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs focus:ring-1 focus:ring-indigo-600">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Opsi B</label>
                            <input type="text" name="option_b" x-model="formDataQuestion.option_b" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs focus:ring-1 focus:ring-indigo-600">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Opsi C</label>
                            <input type="text" name="option_c" x-model="formDataQuestion.option_c" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs focus:ring-1 focus:ring-indigo-600">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Opsi D</label>
                            <input type="text" name="option_d" x-model="formDataQuestion.option_d" required class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs focus:ring-1 focus:ring-indigo-600">
                        </div>
                    </div>
                </div>
                <div class="flex gap-4 mt-8">
                    <button type="button" @click="showQuestionModal = false" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Batal</button>
                    <button type="submit" class="flex-1 py-4 notted-gradient text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-indigo-100 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function millionaireManage() {
    return {
        showSetModal: false,
        showQuestionModal: false,
        editModeSet: false,
        editModeQuestion: false,
        formDataSet: { id: '', name: '', description: '' },
        formDataQuestion: { id: '', set_id: '', question: '', option_a: '', option_b: '', option_c: '', option_d: '', correct_answer: 'A', level: 1 },

        editSet(set) {
            this.editModeSet = true;
            this.formDataSet = { ...set };
            this.showSetModal = true;
        },

        addQuestion(setId) {
            this.editModeQuestion = false;
            this.formDataQuestion = { set_id: setId, question: '', option_a: '', option_b: '', option_c: '', option_d: '', correct_answer: 'A', level: 1 };
            this.showQuestionModal = true;
        },

        editQuestion(q) {
            this.editModeQuestion = true;
            this.formDataQuestion = { ...q };
            this.showQuestionModal = true;
        }
    }
}
</script>
@endpush

@endsection
