<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Buat Survei Baru</h2>
    </x-slot>

    <div class="p-6" x-data="surveyBuilder()">
        <div class="max-w-4xl mx-auto">
            <form action="{{ route('surveys.store') }}" method="POST">
                @csrf

                <!-- Header Section -->
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Buat Survei Baru</h1>
                        <p class="text-slate-500 mt-1">Rancang kuesioner Anda dengan mudah dan cepat.</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('surveys.index') }}"
                            class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">Batal</a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-200 transition-all duration-200 transform hover:-translate-y-0.5">
                            Simpan Survei
                        </button>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Survey Identity -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 mb-8">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Judul Survei</label>
                            <input type="text" name="title" required value="{{ old('title') }}"
                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-400 text-lg font-semibold"
                                placeholder="Contoh: Survei Kepuasan Pembelajaran Semester Ganjil">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi (Opsional)</label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all placeholder:text-slate-400"
                                placeholder="Berikan penjelasan singkat tentang tujuan survei ini...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-800">Daftar Pertanyaan</h2>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"
                            x-text="questions.length + ' Pertanyaan'"></span>
                    </div>

                    <template x-for="(question, index) in questions" :key="question.id">
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 relative group hover:border-blue-200 transition-colors">
                            <!-- Question Header -->
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center">
                                    <span
                                        class="flex items-center justify-center w-8 h-8 bg-blue-50 text-blue-600 rounded-lg font-bold text-sm mr-4"
                                        x-text="index + 1"></span>
                                    <select x-model="question.type" :name="'questions['+index+'][type]'"
                                        class="bg-transparent border-none font-bold text-slate-800 focus:ring-0 cursor-pointer hover:text-blue-600 transition-colors uppercase text-xs tracking-wider">
                                        <option value="multiple_choice">Pilihan Ganda</option>
                                        <option value="essay">Esai / Jawaban Terbuka</option>
                                    </select>
                                </div>
                                <button type="button" @click="removeQuestion(index)"
                                    class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Question Input -->
                            <div class="mb-6">
                                <input type="text" x-model="question.question_text"
                                    :name="'questions['+index+'][question_text]'" required
                                    class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-100 focus:border-blue-500 focus:ring-0 transition-all font-medium text-slate-800 placeholder:text-slate-300"
                                    placeholder="Ketik pertanyaan di sini...">
                            </div>

                            <!-- Options Section (If MC) -->
                            <div x-show="question.type === 'multiple_choice'" x-transition>
                                <div class="space-y-3">
                                    <template x-for="(option, optIndex) in question.options" :key="optIndex">
                                        <div class="flex items-center group/option">
                                            <div class="w-2 h-2 rounded-full bg-slate-200 mr-4"></div>
                                            <input type="text" x-model="question.options[optIndex]"
                                                :name="'questions['+index+'][options][]'"
                                                class="flex-1 px-0 py-1 bg-transparent border-b border-transparent group-hover/option:border-slate-100 focus:border-blue-300 focus:ring-0 text-sm text-slate-600 placeholder:text-slate-300"
                                                placeholder="Ketik pilihan jawaban...">
                                            <button type="button" @click="removeOption(index, optIndex)"
                                                x-show="question.options.length > 1"
                                                class="ml-2 text-slate-300 hover:text-red-400 opacity-0 group-hover/option:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <button type="button" @click="addOption(index)" x-show="question.options.length < 5"
                                        class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center mt-4">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Pilihan Jawaban
                                    </button>
                                </div>
                            </div>

                            <!-- Essay Hint -->
                            <div x-show="question.type === 'essay'" x-transition
                                class="p-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                <p class="text-xs text-slate-400 font-medium italic">Responden akan diberikan area teks
                                    untuk menjawab pertanyaan ini secara bebas.</p>
                            </div>
                        </div>
                    </template>

                    <!-- Add Question Button -->
                    <button type="button" @click="addQuestion"
                        class="w-full py-4 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all font-bold flex items-center justify-center group">
                        <svg class="w-6 h-6 mr-2 transform group-hover:rotate-90 transition-transform duration-300"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Pertanyaan Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function surveyBuilder() {
            return {
                questions: [
                    { id: Date.now(), type: 'multiple_choice', question_text: '', options: ['', ''] }
                ],
                addQuestion() {
                    this.questions.push({
                        id: Date.now(),
                        type: 'multiple_choice',
                        question_text: '',
                        options: ['', '']
                    });
                },
                removeQuestion(index) {
                    this.questions.splice(index, 1);
                },
                addOption(qIndex) {
                    if (this.questions[qIndex].options.length < 5) {
                        this.questions[qIndex].options.push('');
                    }
                },
                removeOption(qIndex, optIndex) {
                    this.questions[qIndex].options.splice(optIndex, 1);
                }
            };
        }
    </script>
</x-app-layout>