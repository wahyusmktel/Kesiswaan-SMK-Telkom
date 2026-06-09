<div class="max-h-[72vh] overflow-y-auto p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <label class="block lg:col-span-2">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Siswa</span>
            <select name="master_siswa_id" required class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Pilih siswa</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}" @selected(old('master_siswa_id', $record?->master_siswa_id) == $student->id)>
                        {{ $student->nama_lengkap }} - {{ $student->nis }} - {{ $student->rombels->first()?->kelas?->nama_kelas ?? '-' }}
                    </option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Waktu Kunjungan</span>
            <input type="datetime-local" name="visited_at" required value="{{ old('visited_at', $record?->visited_at?->format('Y-m-d\\TH:i') ?? now()->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
        </label>
        <label class="block lg:col-span-2">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Keluhan Utama</span>
            <input name="complaint" required value="{{ old('complaint', $record?->complaint) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Demam, pusing, nyeri perut">
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Kondisi</span>
            <select name="condition" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                @foreach(['ringan' => 'Ringan', 'sedang' => 'Sedang', 'berat' => 'Berat'] as $key => $label)
                    <option value="{{ $key }}" @selected(old('condition', $record?->condition ?? 'ringan') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Suhu</span>
            <input type="number" step="0.1" name="temperature" value="{{ old('temperature', $record?->temperature) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="36.8">
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tekanan Darah</span>
            <input name="blood_pressure" value="{{ old('blood_pressure', $record?->blood_pressure) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="110/70">
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nadi</span>
            <input type="number" name="pulse" value="{{ old('pulse', $record?->pulse) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="88">
        </label>
        <label class="block lg:col-span-3">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Gejala</span>
            @php $symptoms = old('symptoms', $record?->symptoms ?? []); @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach(['Demam','Pusing','Mual','Muntah','Batuk','Pilek','Nyeri perut','Lemas','Sesak','Cedera ringan','Pingsan','Diare'] as $symptom)
                    <label class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-bold text-gray-700">
                        <input type="checkbox" name="symptoms[]" value="{{ $symptom }}" @checked(in_array($symptom, $symptoms)) class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        {{ $symptom }}
                    </label>
                @endforeach
            </div>
        </label>
        <label class="block lg:col-span-3">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Anamnesis / Riwayat Singkat</span>
            <textarea name="anamnesis" rows="3" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">{{ old('anamnesis', $record?->anamnesis) }}</textarea>
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Diagnosis / Analisa</span>
            <input name="diagnosis" value="{{ old('diagnosis', $record?->diagnosis) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Observasi awal UKS">
        </label>
        <label class="block lg:col-span-2">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tindakan</span>
            <input name="treatment" value="{{ old('treatment', $record?->treatment) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Istirahat, kompres, pemeriksaan ringan">
        </label>
        <label class="block lg:col-span-3">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Obat / Perawatan</span>
            <textarea name="medicine" rows="2" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">{{ old('medicine', $record?->medicine) }}</textarea>
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tindak Lanjut</span>
            <select name="disposition" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                @foreach(['kembali_kelas' => 'Kembali kelas', 'istirahat_uks' => 'Istirahat di UKS', 'pulang' => 'Dipulangkan', 'rujukan' => 'Rujukan'] as $key => $label)
                    <option value="{{ $key }}" @selected(old('disposition', $record?->disposition ?? 'kembali_kelas') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Istirahat Sampai</span>
            <input type="datetime-local" name="rest_until" value="{{ old('rest_until', $record?->rest_until?->format('Y-m-d\\TH:i')) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Saturasi O2</span>
            <input type="number" name="oxygen_saturation" value="{{ old('oxygen_saturation', $record?->oxygen_saturation) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="98">
        </label>
        <label class="block">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Jenis Faskes Rujukan</span>
            <select name="referral_facility_type" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Tidak dirujuk</option>
                <option value="Puskesmas" @selected(old('referral_facility_type', $record?->referral_facility_type) === 'Puskesmas')>Puskesmas</option>
                <option value="Rumah Sakit" @selected(old('referral_facility_type', $record?->referral_facility_type) === 'Rumah Sakit')>Rumah Sakit</option>
                <option value="Klinik" @selected(old('referral_facility_type', $record?->referral_facility_type) === 'Klinik')>Klinik</option>
            </select>
        </label>
        <label class="block lg:col-span-2">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nama Faskes Rujukan</span>
            <input name="referral_facility_name" value="{{ old('referral_facility_name', $record?->referral_facility_name) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Puskesmas / RS tujuan">
        </label>
        <label class="block lg:col-span-3">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Alasan Rujukan / Catatan Orang Tua</span>
            <textarea name="referral_reason" rows="2" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">{{ old('referral_reason', $record?->referral_reason) }}</textarea>
        </label>
        <label class="block lg:col-span-3">
            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Catatan Tambahan</span>
            <textarea name="notes" rows="2" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">{{ old('notes', $record?->notes) }}</textarea>
        </label>
    </div>
</div>
<div class="flex justify-end gap-3 bg-gray-50 px-6 py-4">
    <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-black text-white hover:bg-red-700">Simpan Rekam Medis</button>
</div>
