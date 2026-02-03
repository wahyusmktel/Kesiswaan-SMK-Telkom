<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Buat Survei Baru</h2>
    </x-slot>

    <div class="p-6"
        x-data="surveyBuilder({{ json_encode($isStudent) }}, {{ json_encode($roles) }}, {{ json_encode($rombels) }}, {{ json_encode($guruKelas) }}, {{ json_encode($nonStudentUsers) }})">
        <div class="max-w-5xl mx-auto">
            <form action="{{ route('surveys.store') }}" method="POST">
                @csrf

                <!-- Header Section -->
                <div class="mb-8 flex justify-between items-end">
                    <div>
                        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Buat Survei Baru</h1>
                        <p class="text-slate-500 mt-1">Rancang kuesioner Anda dengan target responden yang tepat.</p>
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

                <!-- Section: Identitas Survei -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 mb-8">
                    <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                        <span
                            class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mr-3 text-sm">1</span>
                        Identitas Survei
                    </h2>
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

                <!-- Section: Waktu Survei -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 mb-8">
                    <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                        <span
                            class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mr-3 text-sm">2</span>
                        Waktu Survei (Opsional)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Waktu Mulai</label>
                            <input type="datetime-local" name="start_at" value="{{ old('start_at') }}"
                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-slate-600">
                            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-widest font-bold">Kosongkan
                                jika ingin segera dimulai</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Waktu Selesai</label>
                            <input type="datetime-local" name="end_at" value="{{ old('end_at') }}"
                                class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 transition-all text-slate-600">
                            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-widest font-bold">Kosongkan
                                jika tidak ada batas waktu</p>
                        </div>
                    </div>
                </div>

                <!-- Section: Target Responden -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 mb-8">
                    <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                        <span
                            class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mr-3 text-sm">3</span>
                        Target Responden
                    </h2>

                    <div class="space-y-8">
                        @if(!$isStudent)
                            <!-- Option 1: Guru Kelas -->
                            <div class="p-6 rounded-2xl border border-slate-100 bg-slate-50/50">
                                <div
                                    class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="targetCategories.guruKelas"
                                            class="w-5 h-5 text-blue-600 rounded-lg border-slate-300 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="font-bold text-slate-700">Role Guru Kelas</p>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-widest">Pilih dari
                                                daftar guru kelas</p>
                                        </div>
                                    </label>
                                    <div class="relative w-full md:w-64" x-show="targetCategories.guruKelas">
                                        <input type="text" x-model="search.guruKelas" placeholder="Cari Guru..."
                                            class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-blue-500">
                                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div x-show="targetCategories.guruKelas" x-transition
                                    class="mt-4 border-t border-slate-100 pt-4">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <button type="button" @click="toggleAllGuruKelas(true)"
                                            class="text-[10px] font-black text-blue-600 uppercase tracking-tighter hover:underline">Pilih
                                            Semua</button>
                                        <button type="button" @click="toggleAllGuruKelas(false)"
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-tighter hover:underline">Hapus
                                            Semua</button>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                        <template x-for="guru in filteredGuruKelas" :key="guru.id">
                                            <label
                                                class="flex items-center p-3 bg-white rounded-xl border border-slate-100 hover:border-blue-200 transition-colors cursor-pointer group">
                                                <input type="checkbox" :value="guru.id" name="target_users[]"
                                                    x-model="selectedUsers"
                                                    class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                <span class="ml-3 text-xs font-medium text-slate-600 truncate"
                                                    x-text="guru.name"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Option 2: Semua Role Kecuali Siswa (Detail Selection) -->
                            <div class="p-6 rounded-2xl border border-slate-100 bg-slate-50/50">
                                <div
                                    class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="targetCategories.nonStudent"
                                            class="w-5 h-5 text-blue-600 rounded-lg border-slate-300 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <p class="font-bold text-slate-700">Semua Role Pengguna Selain Siswa</p>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-widest">Manajemen, SDM,
                                                Kurikulum, Piket, dll.</p>
                                        </div>
                                    </label>
                                    <div class="relative w-full md:w-64" x-show="targetCategories.nonStudent">
                                        <input type="text" x-model="search.nonStudent" placeholder="Cari Staf/Pengajar..."
                                            class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-blue-500">
                                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div x-show="targetCategories.nonStudent" x-transition
                                    class="mt-4 border-t border-slate-100 pt-4">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <button type="button" @click="toggleAllNonStudent(true)"
                                            class="text-[10px] font-black text-blue-600 uppercase tracking-tighter hover:underline">Check
                                            All</button>
                                        <button type="button" @click="toggleAllNonStudent(false)"
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-tighter hover:underline">Uncheck
                                            All</button>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                        <template x-for="user in filteredNonStudent" :key="user.id">
                                            <label
                                                class="flex items-center p-3 bg-white rounded-xl border border-slate-100 hover:border-blue-200 transition-colors cursor-pointer group">
                                                <input type="checkbox" :value="user.id" name="target_users[]"
                                                    x-model="selectedUsers"
                                                    class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                <span class="ml-3 text-xs font-medium text-slate-600 truncate"
                                                    x-text="user.name"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Option 3: Role Siswa -->
                        <div class="p-6 rounded-2xl border border-slate-100 bg-slate-50/50">
                            <div class="flex justify-between items-center mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="targetCategories.siswa"
                                        class="w-5 h-5 text-blue-600 rounded-lg border-slate-300 focus:ring-blue-500">
                                    <div class="ml-3">
                                        <p class="font-bold text-slate-700">Role Siswa</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-widest">Pilih
                                            berdasarkan kelas atau individu siswa</p>
                                    </div>
                                </label>
                            </div>

                            <div x-show="targetCategories.siswa" x-transition
                                class="mt-4 border-t border-slate-100 pt-4 space-y-6">
                                <!-- Search & Selection Control -->
                                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                                    <div class="relative w-full md:w-64">
                                        <input type="text" x-model="search.rombels" placeholder="Cari Kelas..."
                                            class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-blue-500">
                                        <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <button type="button" @click="toggleAllRombels(true)"
                                            class="text-[10px] font-black text-blue-600 uppercase tracking-tighter hover:underline">Pilih
                                            Semua Kelas</button>
                                        <button type="button" @click="toggleAllRombels(false)"
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-tighter hover:underline">Hapus
                                            Semua</button>
                                    </div>
                                </div>

                                <!-- Classes Grid -->
                                <div
                                    class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                                    <template x-for="rombel in filteredRombels" :key="rombel.id">
                                        <div class="space-y-1">
                                            <div class="flex items-center p-3 bg-white rounded-xl border transition-all cursor-pointer group"
                                                :class="activeRombel === rombel.id ? 'border-blue-500 bg-blue-50/50 shadow-sm' : 'border-slate-100 hover:border-blue-200'"
                                                @click="activeRombel = rombel.id">
                                                <input type="checkbox" :checked="isRombelSelected(rombel)"
                                                    @change="toggleRombel(rombel, $event.target.checked)"
                                                    class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500"
                                                    @click.stop>
                                                <div class="ml-3 overflow-hidden">
                                                    <p class="text-xs font-bold text-slate-700 truncate"
                                                        x-text="rombel.kelas.nama_kelas"></p>
                                                    <p class="text-[9px] text-slate-400 uppercase tracking-tighter"
                                                        x-text="rombel.siswa.length + ' Siswa'"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Individual Students (When a class is active) -->
                                <div x-show="activeRombel" x-transition
                                    class="p-6 bg-white rounded-2xl border border-blue-100 shadow-sm shadow-blue-50">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <h3 class="font-bold text-slate-800"
                                                x-text="'Daftar Siswa: ' + rombels.find(r => r.id === activeRombel)?.kelas.nama_kelas">
                                            </h3>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-widest mt-1">Pilih
                                                siswa secara individu</p>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <button type="button" @click="toggleActiveRombelSiswa(true)"
                                                class="text-[10px] font-black text-blue-600 uppercase tracking-tighter hover:underline">Check
                                                All</button>
                                            <button type="button" @click="toggleActiveRombelSiswa(false)"
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-tighter hover:underline">Uncheck
                                                All</button>
                                            <button type="button" @click="activeRombel = null"
                                                class="text-slate-300 hover:text-slate-600 ml-2"><svg class="w-4 h-4"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg></button>
                                        </div>
                                    </div>
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                        <template x-for="item in currentStudents" :key="item.user.id">
                                            <label
                                                class="flex items-center p-2 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer group">
                                                <input type="checkbox" :value="item.user.id" name="target_users[]"
                                                    x-model="selectedUsers"
                                                    class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                                <span
                                                    class="ml-3 text-xs text-slate-500 group-hover:text-blue-700 truncate"
                                                    x-text="item.user.name"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Pertanyaan -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-800 border-l-4 border-blue-500 pl-4">Daftar Pertanyaan
                        </h2>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest"
                            x-text="questions.length + ' Pertanyaan Ditambahkan'"></span>
                    </div>

                    <template x-for="(question, index) in questions" :key="question.idx">
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
                                    class="p-2 text-slate-200 hover:text-red-500 transition-colors">
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
                                    class="w-full px-0 py-2 bg-transparent border-b-2 border-slate-100 focus:border-blue-500 focus:ring-0 transition-all font-medium text-slate-800 placeholder:text-slate-300 text-lg"
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
                        </div>
                    </template>

                    <!-- Add Question Button -->
                    <button type="button" @click="addQuestion"
                        class="w-full py-6 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 transition-all font-bold flex items-center justify-center group">
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

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>

    <script>
        function surveyBuilder(isStudent, roles, rombels, guruKelas, nonStudentUsers) {
            return {
                isStudent: isStudent,
                roles: roles,
                rombels: rombels,
                guruKelas: guruKelas,
                nonStudentUsers: nonStudentUsers,

                targetCategories: {
                    guruKelas: false,
                    nonStudent: false,
                    siswa: isStudent
                },

                search: {
                    guruKelas: '',
                    nonStudent: '',
                    rombels: ''
                },

                selectedUsers: [],
                activeRombel: null,

                questions: [
                    { idx: Date.now(), type: 'multiple_choice', question_text: '', options: ['', ''] }
                ],

                // Respondent Logic: Guru Kelas
                get filteredGuruKelas() {
                    if (!this.search.guruKelas) return this.guruKelas;
                    return this.guruKelas.filter(g => g.name.toLowerCase().includes(this.search.guruKelas.toLowerCase()));
                },

                toggleAllGuruKelas(checked) {
                    const ids = this.filteredGuruKelas.map(g => g.id);
                    if (checked) {
                        this.selectedUsers = [...new Set([...this.selectedUsers, ...ids])];
                    } else {
                        this.selectedUsers = this.selectedUsers.filter(id => !ids.includes(id));
                    }
                },

                // Respondent Logic: Non-Student
                get filteredNonStudent() {
                    if (!this.search.nonStudent) return this.nonStudentUsers;
                    return this.nonStudentUsers.filter(u => u.name.toLowerCase().includes(this.search.nonStudent.toLowerCase()));
                },

                toggleAllNonStudent(checked) {
                    const ids = this.filteredNonStudent.map(u => u.id);
                    if (checked) {
                        this.selectedUsers = [...new Set([...this.selectedUsers, ...ids])];
                    } else {
                        this.selectedUsers = this.selectedUsers.filter(id => !ids.includes(id));
                    }
                },

                // Respondent Logic: Students & Classes
                get filteredRombels() {
                    if (!this.search.rombels) return this.rombels;
                    return this.rombels.filter(r => r.kelas.nama_kelas.toLowerCase().includes(this.search.rombels.toLowerCase()));
                },

                get currentStudents() {
                    if (!this.activeRombel) return [];
                    return this.rombels.find(r => r.id === this.activeRombel)?.siswa || [];
                },

                isRombelSelected(rombel) {
                    const ids = rombel.siswa.map(s => s.user.id);
                    return ids.length > 0 && ids.every(id => this.selectedUsers.includes(id));
                },

                toggleRombel(rombel, checked) {
                    const ids = rombel.siswa.map(s => s.user.id);
                    if (checked) {
                        this.selectedUsers = [...new Set([...this.selectedUsers, ...ids])];
                    } else {
                        this.selectedUsers = this.selectedUsers.filter(id => !ids.includes(id));
                    }
                },

                toggleAllRombels(checked) {
                    this.rombels.forEach(r => this.toggleRombel(r, checked));
                },

                toggleActiveRombelSiswa(checked) {
                    const rombel = this.rombels.find(r => r.id === this.activeRombel);
                    if (rombel) this.toggleRombel(rombel, checked);
                },

                // Question Builder Logic
                addQuestion() {
                    this.questions.push({
                        idx: Date.now(),
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