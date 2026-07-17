@php
    $periodKey = $period?->id ?? 'new';
    $initialGrades = $period?->grades ?: [['subject' => '', 'score' => '']];
    $initialExtracurriculars = $period?->extracurriculars ?: [['name' => '', 'predicate' => '', 'description' => '']];
@endphp
<form method="POST" action="{{ route('wali-kelas.buku-induk.periods.store', $student) }}"
    x-data='{
        grades: @json($initialGrades),
        extracurriculars: @json($initialExtracurriculars)
    }' class="space-y-6">
    @csrf
    @if($period)<input type="hidden" name="period_id" value="{{ $period->id }}">@endif
    <div class="grid gap-4 md:grid-cols-3">
        <label><span class="mb-1 block text-sm font-bold text-gray-700">Tahun Pelajaran</span><input name="school_year" required value="{{ $period?->school_year }}" placeholder="2025/2026" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"></label>
        <label><span class="mb-1 block text-sm font-bold text-gray-700">Semester</span><select name="semester" required class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"><option value="Ganjil" @selected($period?->semester === 'Ganjil')>Ganjil</option><option value="Genap" @selected($period?->semester === 'Genap')>Genap</option></select></label>
        <label><span class="mb-1 block text-sm font-bold text-gray-700">Referensi Tahun Aktif</span><select name="tahun_pelajaran_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500"><option value="">Tanpa referensi</option>@foreach($academicYears as $year)<option value="{{ $year->id }}" @selected($period?->tahun_pelajaran_id === $year->id)>{{ $year->tahun }} · {{ $year->semester }}</option>@endforeach</select></label>
    </div>

    <div>
        <div class="mb-2 flex items-center justify-between"><h4 class="text-sm font-bold text-gray-900">Nilai Mata Pelajaran</h4><button type="button" @click="grades.push({subject:'',score:''})" class="text-sm font-bold text-red-600">+ Tambah Mapel</button></div>
        <div class="space-y-2">
            <template x-for="(grade, index) in grades" :key="index">
                <div class="grid grid-cols-[1fr_110px_36px] gap-2">
                    <input :name="`grades[${index}][subject]`" x-model="grade.subject" required placeholder="Nama mata pelajaran" class="rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <input :name="`grades[${index}][score]`" x-model="grade.score" required type="number" min="0" max="100" step="0.01" placeholder="Nilai" class="rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <button type="button" @click="grades.splice(index, 1)" :disabled="grades.length === 1" title="Hapus baris" class="rounded-lg border border-gray-300 text-gray-500 disabled:opacity-30">×</button>
                </div>
            </template>
        </div>
    </div>

    <div>
        <div class="mb-2 flex items-center justify-between"><h4 class="text-sm font-bold text-gray-900">Ekstrakurikuler</h4><button type="button" @click="extracurriculars.push({name:'',predicate:'',description:''})" class="text-sm font-bold text-red-600">+ Tambah Kegiatan</button></div>
        <div class="space-y-2">
            <template x-for="(activity, index) in extracurriculars" :key="index">
                <div class="grid gap-2 md:grid-cols-[1fr_140px_1.4fr_36px]">
                    <input :name="`extracurriculars[${index}][name]`" x-model="activity.name" required placeholder="Nama kegiatan" class="rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <input :name="`extracurriculars[${index}][predicate]`" x-model="activity.predicate" placeholder="Predikat" class="rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <input :name="`extracurriculars[${index}][description]`" x-model="activity.description" placeholder="Keterangan" class="rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <button type="button" @click="extracurriculars.splice(index, 1)" :disabled="extracurriculars.length === 1" title="Hapus baris" class="rounded-lg border border-gray-300 text-gray-500 disabled:opacity-30">×</button>
                </div>
            </template>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-buku-induk.input name="sick_days" label="Sakit (hari)" type="number" :value="$period?->sick_days ?? 0" />
        <x-buku-induk.input name="permitted_days" label="Izin (hari)" type="number" :value="$period?->permitted_days ?? 0" />
        <x-buku-induk.input name="absent_days" label="Tanpa Keterangan" type="number" :value="$period?->absent_days ?? 0" />
        <x-buku-induk.input name="conduct" label="Sikap/Perilaku" :value="$period?->conduct" placeholder="Sangat Baik" />
    </div>
    <x-buku-induk.textarea name="development_notes" label="Catatan Perkembangan Semester" :value="$period?->development_notes" />
    <div class="text-right"><button class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-600">{{ $period ? 'Perbarui Semester' : 'Simpan Semester' }}</button></div>
</form>
