<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Isi Modul Ajar</h2>
            <p class="text-xs text-gray-500">{{ $module->kode_modul }} · {{ $module->nama_modul }}</p>
        </div>
    </x-slot>

    <div id="module-editor-top" class="w-full bg-white"
        x-data="teachingModuleEditor({{ Js::from($content) }}, {{ Js::from($module->alokasi_waktu) }})"
        x-init="init()">
        <form method="POST" action="{{ route('guru-kelas.teaching-module.content.update', $module) }}"
            @submit="prepareSubmit()">
            @csrf
            @method('PUT')
            <input type="hidden" name="content_json" x-ref="contentInput">

            <div class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 px-4 py-3 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <a href="{{ route('guru-kelas.teaching-module.index') }}"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-md text-gray-500 transition hover:bg-gray-100 hover:text-gray-900"
                            title="Kembali ke daftar">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="sr-only">Kembali</span>
                        </a>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="truncate text-base font-black text-gray-950 sm:text-lg">{{ $module->nama_modul }}</h1>
                                <span class="rounded-full px-2 py-0.5 text-[11px] font-black uppercase ring-1 ring-inset"
                                    :class="dirty ? 'bg-amber-50 text-amber-700 ring-amber-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-200'"
                                    x-text="dirty ? 'Belum disimpan' : 'Tersimpan'"></span>
                            </div>
                            <p class="truncate text-xs text-gray-500">{{ $module->mata_pelajaran }} · {{ $module->tahun_pelajaran }} {{ $module->semester }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="mr-1 hidden min-w-36 sm:block">
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-500">
                                <span>Kelengkapan isi</span>
                                <span x-text="completionPercent + '%'">0%</span>
                            </div>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-gray-200">
                                <div class="h-full rounded-full bg-red-600 transition-all duration-300"
                                    :style="`width: ${completionPercent}%`"></div>
                            </div>
                        </div>
                        <a href="{{ route('guru-kelas.teaching-module.pdf.preview', $module) }}" target="_blank"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-xs font-bold text-gray-700 transition hover:bg-gray-50">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m6 4H9m8 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414A1 1 0 0117 7.414V19a2 2 0 01-2 2z" />
                            </svg>
                            Pratinjau PDF
                        </a>
                        <button type="submit" name="status" value="draft"
                            class="inline-flex h-10 items-center justify-center rounded-md border border-gray-300 bg-white px-3 text-xs font-bold text-gray-700 transition hover:bg-gray-50">
                            Simpan Draft
                        </button>
                        <button type="submit" name="status" value="complete"
                            class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-red-600 px-3 text-xs font-bold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Lengkap
                        </button>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="mx-4 mt-5 border-l-4 border-red-600 bg-red-50 px-4 py-3 text-sm text-red-800 sm:mx-6 lg:mx-8" role="alert">
                    <p class="font-bold">Modul belum dapat disimpan.</p>
                    <ul class="mt-1 list-disc space-y-1 pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid min-h-[calc(100vh-8rem)] grid-cols-1 lg:grid-cols-[15rem_minmax(0,1fr)]">
                <aside class="border-b border-gray-200 bg-gray-50 px-4 py-4 lg:border-b-0 lg:border-r lg:px-5 lg:py-6">
                    <nav class="flex gap-2 overflow-x-auto lg:sticky lg:top-24 lg:block lg:space-y-1" aria-label="Bagian modul">
                        <template x-for="(tab, index) in tabs" :key="tab.key">
                            <button type="button" @click="goTo(tab.key)"
                                class="flex min-w-max items-center gap-3 rounded-md px-3 py-2.5 text-left text-sm font-bold transition lg:w-full"
                                :class="active === tab.key ? 'bg-white text-red-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white hover:text-gray-900'">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-black"
                                    :class="sectionDone(tab.key) ? 'bg-emerald-100 text-emerald-700' : (active === tab.key ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-600')"
                                    x-text="sectionDone(tab.key) ? '✓' : index + 1"></span>
                                <span x-text="tab.label"></span>
                            </button>
                        </template>
                    </nav>
                </aside>

                <main class="min-w-0 px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
                    <div class="mx-auto max-w-5xl">
                        <section x-show="active === 'identification'" x-cloak x-transition.opacity class="space-y-9">
                            <div class="border-b border-gray-200 pb-4">
                                <p class="text-xs font-black uppercase text-red-700">Bagian 1</p>
                                <h2 class="mt-1 text-2xl font-black text-gray-950">Identifikasi</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-500">Kondisi awal peserta didik, karakter materi, dan dimensi profil lulusan.</p>
                            </div>

                            <x-teaching-module.repeatable-text
                                title="Identifikasi Peserta Didik"
                                description="Gambarkan pengetahuan awal, pengalaman, kebutuhan, dan karakter peserta didik."
                                path="content.identification.students"
                                add-label="Tambah identifikasi"
                                item-label="Identifikasi peserta didik"
                                placeholder="Contoh: Peserta didik kelas XI telah memiliki pengalaman..."
                                rows="4" />

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text
                                    title="Identifikasi Materi Pembelajaran"
                                    description="Tuliskan ruang pengetahuan, tingkat kesulitan, relevansi, dan nilai karakter pada materi."
                                    path="content.identification.materials"
                                    add-label="Tambah identifikasi"
                                    item-label="Identifikasi materi"
                                    placeholder="Contoh: Materi mencakup konsep, prosedur, dan penerapan..."
                                    rows="4" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Dimensi Profil Lulusan</h4>
                                    <p class="mt-1 text-xs leading-5 text-gray-500">Pilih dimensi yang dicapai dan tambahkan catatan bila diperlukan.</p>
                                </div>
                                <div class="mt-4 divide-y divide-gray-200 border-y border-gray-200">
                                    <template x-for="dimension in content.identification.graduate_profile" :key="dimension.key">
                                        <div class="grid gap-3 py-3 sm:grid-cols-[minmax(0,18rem)_minmax(0,1fr)] sm:items-center">
                                            <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold text-gray-800">
                                                <input type="checkbox" x-model="dimension.selected"
                                                    class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                <span x-text="dimension.label"></span>
                                            </label>
                                            <input type="text" x-model="dimension.note" :disabled="!dimension.selected"
                                                placeholder="Catatan pencapaian (opsional)"
                                                class="h-10 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500 disabled:bg-gray-100 disabled:text-gray-400">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </section>

                        <section x-show="active === 'design'" x-cloak x-transition.opacity class="space-y-9">
                            <div class="border-b border-gray-200 pb-4">
                                <p class="text-xs font-black uppercase text-red-700">Bagian 2</p>
                                <h2 class="mt-1 text-2xl font-black text-gray-950">Desain Pembelajaran</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-500">Rangkaian capaian, tujuan, strategi, mitra, lingkungan, dan pemanfaatan digital.</p>
                            </div>

                            <x-teaching-module.repeatable-text title="Capaian Pembelajaran"
                                path="content.design.learning_outcomes" add-label="Tambah capaian"
                                item-label="Capaian" placeholder="Tuliskan capaian pembelajaran..." rows="4" />

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Tujuan Pembelajaran"
                                    path="content.design.learning_objectives" add-label="Tambah tujuan"
                                    item-label="Tujuan" placeholder="Peserta didik mampu..." rows="3" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Topik Pembelajaran"
                                    path="content.design.learning_topics" add-label="Tambah topik"
                                    item-label="Topik" placeholder="Tuliskan topik pembelajaran..." rows="2" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Praktik Pedagogi"
                                    path="content.design.pedagogical_practices" add-label="Tambah praktik"
                                    item-label="Praktik pedagogi" placeholder="Model, strategi, dan metode pembelajaran..." rows="3" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Mitra Pembelajaran"
                                    path="content.design.learning_partners" add-label="Tambah mitra"
                                    item-label="Mitra" placeholder="Contoh: guru produktif, praktisi industri, teman sebaya..." rows="2" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Lingkungan Belajar"
                                    path="content.design.learning_environment" add-label="Tambah lingkungan"
                                    item-label="Lingkungan" placeholder="Ruang fisik, ruang virtual, dan budaya belajar..." rows="3" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Pemanfaatan Digital"
                                    path="content.design.digital_use" add-label="Tambah pemanfaatan"
                                    item-label="Pemanfaatan digital" placeholder="Perencanaan, pelaksanaan, dan asesmen digital..." rows="3" />
                            </div>
                        </section>

                        <section x-show="active === 'experience'" x-cloak x-transition.opacity class="space-y-7">
                            <div class="flex flex-col justify-between gap-4 border-b border-gray-200 pb-4 sm:flex-row sm:items-end">
                                <div>
                                    <p class="text-xs font-black uppercase text-red-700">Bagian 3</p>
                                    <h2 class="mt-1 text-2xl font-black text-gray-950">Pengalaman Belajar</h2>
                                    <p class="mt-1 text-sm leading-6 text-gray-500">Pertemuan, kegiatan awal, fase kegiatan inti, dan penutup.</p>
                                </div>
                                <button type="button" @click="addMeeting()"
                                    class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-xs font-bold text-gray-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Pertemuan
                                </button>
                            </div>

                            <div class="flex gap-2 overflow-x-auto pb-1">
                                <template x-for="(meeting, meetingIndex) in content.experiences" :key="meetingIndex">
                                    <button type="button" @click="openMeeting = meetingIndex"
                                        class="min-w-max rounded-md px-3 py-2 text-xs font-bold transition"
                                        :class="openMeeting === meetingIndex ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                        x-text="`Pertemuan ${meetingIndex + 1}`"></button>
                                </template>
                            </div>

                            <template x-for="(meeting, meetingIndex) in content.experiences" :key="meetingIndex">
                                <div x-show="openMeeting === meetingIndex" x-transition.opacity class="space-y-9">
                                    <div class="grid gap-4 border-y border-gray-200 bg-gray-50 px-4 py-4 sm:grid-cols-[minmax(0,1fr)_12rem_auto] sm:items-end">
                                        <div>
                                            <label class="text-xs font-bold text-gray-700">Judul/Konteks Pertemuan</label>
                                            <input type="text" x-model="meeting.title" placeholder="Opsional"
                                                class="mt-1 h-10 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-gray-700">Alokasi Waktu</label>
                                            <input type="text" x-model="meeting.allocation" placeholder="4 JP"
                                                class="mt-1 h-10 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        </div>
                                        <button type="button" @click="removeMeeting(meetingIndex)"
                                            :disabled="content.experiences.length === 1"
                                            class="inline-flex h-10 items-center justify-center gap-2 rounded-md px-3 text-xs font-bold text-red-700 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-30">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus Pertemuan
                                        </button>
                                    </div>

                                    <x-teaching-module.repeatable-text title="Kegiatan Awal Pembelajaran"
                                        description="Kegiatan berkesadaran, bermakna, dan menggembirakan sebelum masuk ke inti."
                                        path="meeting.opening" add-label="Tambah kegiatan"
                                        item-label="Kegiatan awal" placeholder="Tuliskan aktivitas pembuka..." rows="3" />

                                    <div class="border-t border-gray-200 pt-8">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <h3 class="text-base font-black text-gray-950">Kegiatan Inti</h3>
                                                <p class="mt-1 text-xs leading-5 text-gray-500">Susun fase sesuai model pembelajaran yang digunakan.</p>
                                            </div>
                                            <button type="button" @click="addPhase(meeting)"
                                                class="inline-flex h-9 shrink-0 items-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-xs font-bold text-gray-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Tambah Fase
                                            </button>
                                        </div>

                                        <div class="mt-5 divide-y divide-gray-200 border-y border-gray-200">
                                            <template x-for="(phase, phaseIndex) in meeting.core_phases" :key="phaseIndex">
                                                <div class="space-y-6 py-6">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div class="grid min-w-0 flex-1 gap-4 sm:grid-cols-2">
                                                            <div>
                                                                <label class="text-xs font-bold text-gray-700" x-text="`Nama Fase ${phaseIndex + 1}`"></label>
                                                                <input type="text" x-model="phase.title" placeholder="Contoh: Orientasi Masalah"
                                                                    class="mt-1 h-10 w-full rounded-md border-gray-300 text-sm font-semibold shadow-sm focus:border-red-500 focus:ring-red-500">
                                                            </div>
                                                            <div>
                                                                <label class="text-xs font-bold text-gray-700">Prinsip Pembelajaran</label>
                                                                <input type="text" x-model="phase.principles" placeholder="Berkesadaran, bermakna, menggembirakan"
                                                                    class="mt-1 h-10 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                                            </div>
                                                        </div>
                                                        <button type="button" @click="removePhase(meeting, phaseIndex)"
                                                            :disabled="meeting.core_phases.length === 1"
                                                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-gray-400 transition hover:bg-red-50 hover:text-red-700 disabled:cursor-not-allowed disabled:opacity-30"
                                                            title="Hapus fase">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            <span class="sr-only">Hapus fase</span>
                                                        </button>
                                                    </div>

                                                    <div class="border-l-2 border-red-200 pl-4">
                                                        <x-teaching-module.repeatable-text title="Aktivitas Guru"
                                                            path="phase.teacher_activities" add-label="Tambah aktivitas"
                                                            item-label="Aktivitas guru" placeholder="Tuliskan aktivitas guru..." rows="2" />
                                                    </div>
                                                    <div class="border-l-2 border-gray-200 pl-4">
                                                        <x-teaching-module.repeatable-text title="Aktivitas Peserta Didik"
                                                            path="phase.student_activities" add-label="Tambah aktivitas"
                                                            item-label="Aktivitas peserta didik" placeholder="Tuliskan aktivitas peserta didik..." rows="2" />
                                                    </div>
                                                    <div class="border-l-2 border-gray-200 pl-4">
                                                        <x-teaching-module.repeatable-text title="Output"
                                                            path="phase.outputs" add-label="Tambah output"
                                                            item-label="Output" placeholder="Tuliskan produk, bukti belajar, atau hasil fase..." rows="2" />
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 pt-8">
                                        <x-teaching-module.repeatable-text title="Kegiatan Penutup"
                                            path="meeting.closing" add-label="Tambah kegiatan"
                                            item-label="Kegiatan penutup" placeholder="Refleksi, simpulan, umpan balik, dan tindak lanjut..." rows="3" />
                                    </div>
                                </div>
                            </template>
                        </section>

                        <section x-show="active === 'assessment'" x-cloak x-transition.opacity class="space-y-9">
                            <div class="border-b border-gray-200 pb-4">
                                <p class="text-xs font-black uppercase text-red-700">Bagian 4</p>
                                <h2 class="mt-1 text-2xl font-black text-gray-950">Asesmen Pembelajaran</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-500">Bukti ketercapaian pada tahap awal, proses, dan akhir.</p>
                            </div>

                            <x-teaching-module.repeatable-text title="Asesmen Awal Pembelajaran"
                                path="content.assessment.initial" add-label="Tambah asesmen"
                                item-label="Asesmen awal" placeholder="Kuis diagnostik, tanya jawab, observasi awal..." rows="3" />

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Asesmen pada Proses Pembelajaran"
                                    path="content.assessment.process" add-label="Tambah asesmen"
                                    item-label="Asesmen proses" placeholder="Observasi, penilaian LKPD, umpan balik formatif..." rows="3" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Asesmen Akhir Pembelajaran"
                                    path="content.assessment.final" add-label="Tambah asesmen"
                                    item-label="Asesmen akhir" placeholder="Presentasi, produk, tes akhir, refleksi..." rows="3" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Kriteria Ketercapaian Tujuan Pembelajaran"
                                    path="content.assessment.criteria" add-label="Tambah kriteria"
                                    item-label="Kriteria" placeholder="Peserta didik dinyatakan mencapai tujuan apabila..." rows="4" />
                            </div>
                        </section>

                        <section x-show="active === 'supporting'" x-cloak x-transition.opacity class="space-y-9">
                            <div class="border-b border-gray-200 pb-4">
                                <p class="text-xs font-black uppercase text-red-700">Bagian 5</p>
                                <h2 class="mt-1 text-2xl font-black text-gray-950">Pendukung Pembelajaran</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-500">Pertanyaan pemantik, diferensiasi, pengayaan, dan remedial.</p>
                            </div>

                            <x-teaching-module.repeatable-text title="Pertanyaan Pemantik"
                                path="content.supporting.trigger_questions" add-label="Tambah pertanyaan"
                                item-label="Pertanyaan" placeholder="Tuliskan pertanyaan yang menghubungkan materi dengan pengalaman nyata..." rows="2" />

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Diferensiasi Pembelajaran"
                                    path="content.supporting.differentiation" add-label="Tambah diferensiasi"
                                    item-label="Diferensiasi" placeholder="Dukungan, tantangan, pilihan proses, atau variasi produk..." rows="3" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Pengayaan"
                                    path="content.supporting.enrichment" add-label="Tambah pengayaan"
                                    item-label="Pengayaan" placeholder="Aktivitas lanjutan untuk peserta didik yang telah mencapai tujuan..." rows="3" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Remedial"
                                    path="content.supporting.remedial" add-label="Tambah remedial"
                                    item-label="Remedial" placeholder="Dukungan ulang untuk peserta didik yang belum mencapai tujuan..." rows="3" />
                            </div>
                        </section>

                        <section x-show="active === 'attachments'" x-cloak x-transition.opacity class="space-y-9">
                            <div class="border-b border-gray-200 pb-4">
                                <p class="text-xs font-black uppercase text-red-700">Bagian 6</p>
                                <h2 class="mt-1 text-2xl font-black text-gray-950">Lampiran & Pengesahan</h2>
                                <p class="mt-1 text-sm leading-6 text-gray-500">Daftar bahan pendukung dan identitas pengesahan pada bagian akhir dokumen.</p>
                            </div>

                            <x-teaching-module.repeatable-text title="Bahan Ajar"
                                path="content.attachments.teaching_materials" add-label="Tambah bahan ajar"
                                item-label="Bahan ajar" placeholder="Contoh: Ringkasan konsep dan materi presentasi..." rows="2" />

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Lembar Kerja"
                                    path="content.attachments.worksheets" add-label="Tambah lembar kerja"
                                    item-label="Lembar kerja" placeholder="Contoh: LKPD MA-1.1..." rows="2" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <x-teaching-module.repeatable-text title="Lampiran Asesmen"
                                    path="content.attachments.assessments" add-label="Tambah lampiran asesmen"
                                    item-label="Lampiran asesmen" placeholder="Kuis, rubrik, lembar observasi, atau instrumen penilaian..." rows="2" />
                            </div>

                            <div class="border-t border-gray-200 pt-8">
                                <div>
                                    <h3 class="text-base font-black text-gray-950">Pengesahan Dokumen</h3>
                                    <p class="mt-1 text-xs leading-5 text-gray-500">Data ini dicetak pada blok tanda tangan akhir modul.</p>
                                </div>

                                <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                                    <div>
                                        <label class="text-sm font-bold text-gray-800">Tempat</label>
                                        <input type="text" x-model="content.approval.location"
                                            class="mt-1 h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                    </div>
                                    <div>
                                        <label class="text-sm font-bold text-gray-800">Tanggal</label>
                                        <input type="date" x-model="content.approval.date"
                                            class="mt-1 h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-5 border-t border-gray-200 pt-6 lg:grid-cols-2">
                                    <div class="space-y-4">
                                        <p class="text-xs font-black uppercase text-gray-500">Validator</p>
                                        <div>
                                            <label class="text-sm font-bold text-gray-800">Jabatan</label>
                                            <input type="text" x-model="content.approval.validator_title"
                                                class="mt-1 h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        </div>
                                        <div>
                                            <label class="text-sm font-bold text-gray-800">Nama Validator</label>
                                            <input type="text" x-model="content.approval.validator_name"
                                                class="mt-1 h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        </div>
                                        <div>
                                            <label class="text-sm font-bold text-gray-800">NIP/NIK Validator</label>
                                            <input type="text" x-model="content.approval.validator_nip"
                                                class="mt-1 h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <p class="text-xs font-black uppercase text-gray-500">Penyusun</p>
                                        <div>
                                            <label class="text-sm font-bold text-gray-800">Jabatan</label>
                                            <input type="text" x-model="content.approval.teacher_title"
                                                class="mt-1 h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        </div>
                                        <div>
                                            <label class="text-sm font-bold text-gray-800">Nama Penyusun</label>
                                            <input type="text" x-model="content.approval.teacher_name"
                                                class="mt-1 h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        </div>
                                        <div>
                                            <label class="text-sm font-bold text-gray-800">NIP/NIK Penyusun</label>
                                            <input type="text" x-model="content.approval.teacher_nip"
                                                class="mt-1 h-11 w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <div class="mt-10 flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-between">
                            <button type="button" @click="previousTab()" :disabled="activeIndex === 0"
                                class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 text-xs font-bold text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-30">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Bagian Sebelumnya
                            </button>
                            <button type="button" @click="nextTab()" :disabled="activeIndex === tabs.length - 1"
                                class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-gray-900 px-4 text-xs font-bold text-white transition hover:bg-black disabled:cursor-not-allowed disabled:opacity-30">
                                Bagian Berikutnya
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </main>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            window.teachingModuleEditor = function (initialContent, defaultAllocation) {
                return {
                    content: JSON.parse(JSON.stringify(initialContent)),
                    defaultAllocation,
                    active: 'identification',
                    openMeeting: 0,
                    dirty: false,
                    baseline: '',
                    tabs: [
                        { key: 'identification', label: 'Identifikasi' },
                        { key: 'design', label: 'Desain Pembelajaran' },
                        { key: 'experience', label: 'Pengalaman Belajar' },
                        { key: 'assessment', label: 'Asesmen' },
                        { key: 'supporting', label: 'Pendukung' },
                        { key: 'attachments', label: 'Lampiran & Pengesahan' },
                    ],

                    init() {
                        this.baseline = JSON.stringify(this.content);
                        this.$refs.contentInput.value = this.baseline;
                        this.$watch('content', value => {
                            this.dirty = JSON.stringify(value) !== this.baseline;
                        });

                        window.addEventListener('beforeunload', event => {
                            if (!this.dirty) return;
                            event.preventDefault();
                            event.returnValue = '';
                        });
                    },

                    get activeIndex() {
                        return this.tabs.findIndex(tab => tab.key === this.active);
                    },

                    get completionPercent() {
                        const completed = this.tabs.filter(tab => this.sectionDone(tab.key)).length;
                        return Math.round((completed / this.tabs.length) * 100);
                    },

                    prepareSubmit() {
                        this.$refs.contentInput.value = JSON.stringify(this.content);
                        this.dirty = false;
                    },

                    goTo(key) {
                        this.active = key;
                        document.getElementById('module-editor-top')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    },

                    previousTab() {
                        if (this.activeIndex > 0) this.goTo(this.tabs[this.activeIndex - 1].key);
                    },

                    nextTab() {
                        if (this.activeIndex < this.tabs.length - 1) this.goTo(this.tabs[this.activeIndex + 1].key);
                    },

                    removeItem(items, index) {
                        if (items.length > 1) items.splice(index, 1);
                    },

                    addMeeting() {
                        this.content.experiences.push(this.newMeeting());
                        this.openMeeting = this.content.experiences.length - 1;
                    },

                    removeMeeting(index) {
                        if (this.content.experiences.length === 1) return;
                        this.confirmRemoval('Hapus pertemuan ini?', 'Seluruh aktivitas pada pertemuan tersebut akan dihapus.', () => {
                            this.content.experiences.splice(index, 1);
                            this.openMeeting = Math.max(0, Math.min(this.openMeeting, this.content.experiences.length - 1));
                        });
                    },

                    addPhase(meeting) {
                        meeting.core_phases.push(this.newPhase());
                    },

                    removePhase(meeting, index) {
                        if (meeting.core_phases.length === 1) return;
                        this.confirmRemoval('Hapus fase kegiatan?', 'Aktivitas guru, peserta didik, dan output pada fase ini akan dihapus.', () => {
                            meeting.core_phases.splice(index, 1);
                        });
                    },

                    confirmRemoval(title, text, callback) {
                        if (window.Swal) {
                            Swal.fire({
                                title,
                                text,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc2626',
                                confirmButtonText: 'Ya, hapus',
                                cancelButtonText: 'Batal',
                            }).then(result => {
                                if (result.isConfirmed) callback();
                            });
                            return;
                        }

                        if (window.confirm(title)) callback();
                    },

                    newMeeting() {
                        return {
                            number: this.content.experiences.length + 1,
                            title: '',
                            allocation: this.defaultAllocation,
                            opening: [''],
                            core_phases: [
                                this.newPhase('Orientasi Masalah', 'Bermakna dan berkesadaran'),
                                this.newPhase('Mengorganisasi Peserta Didik untuk Belajar', 'Berkesadaran, bermakna, dan menggembirakan'),
                                this.newPhase('Penyelidikan', 'Berkesadaran dan menggembirakan'),
                                this.newPhase('Mengembangkan dan Menyajikan Hasil Karya', 'Berkesadaran dan menggembirakan'),
                                this.newPhase('Analisis dan Evaluasi', 'Bermakna dan berkesadaran'),
                            ],
                            closing: [''],
                        };
                    },

                    newPhase(title = 'Kegiatan Inti', principles = 'Berkesadaran, bermakna, dan menggembirakan') {
                        return {
                            title,
                            principles,
                            teacher_activities: [''],
                            student_activities: [''],
                            outputs: [''],
                        };
                    },

                    sectionDone(key) {
                        if (key === 'identification') {
                            return this.hasText(this.content.identification.students)
                                && this.hasText(this.content.identification.materials);
                        }
                        if (key === 'design') {
                            return this.hasText(this.content.design.learning_outcomes)
                                && this.hasText(this.content.design.learning_objectives);
                        }
                        if (key === 'experience') {
                            return this.content.experiences.some(meeting =>
                                this.hasText(meeting.opening)
                                || this.hasText(meeting.closing)
                                || meeting.core_phases.some(phase =>
                                    this.hasText(phase.teacher_activities)
                                    || this.hasText(phase.student_activities)
                                    || this.hasText(phase.outputs)
                                )
                            );
                        }
                        if (key === 'assessment') {
                            return this.hasText(this.content.assessment.initial)
                                || this.hasText(this.content.assessment.process)
                                || this.hasText(this.content.assessment.final);
                        }
                        if (key === 'supporting') {
                            return this.hasText(this.content.supporting.trigger_questions)
                                || this.hasText(this.content.supporting.differentiation)
                                || this.hasText(this.content.supporting.enrichment)
                                || this.hasText(this.content.supporting.remedial);
                        }
                        if (key === 'attachments') {
                            return this.hasText(this.content.attachments.teaching_materials)
                                || this.hasText(this.content.attachments.worksheets)
                                || this.hasText(this.content.attachments.assessments);
                        }
                        return false;
                    },

                    hasText(value) {
                        if (typeof value === 'string') return value.trim().length > 0;
                        if (Array.isArray(value)) return value.some(item => this.hasText(item));
                        if (value && typeof value === 'object') return Object.values(value).some(item => this.hasText(item));
                        return false;
                    },
                };
            };
        </script>
    @endpush
</x-app-layout>
