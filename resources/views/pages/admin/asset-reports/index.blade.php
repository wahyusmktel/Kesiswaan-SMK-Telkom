<x-app-layout>
    <div class="mx-auto max-w-[1600px] space-y-6">
        <section class="overflow-hidden rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 p-6 text-white shadow-xl sm:p-8">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[.22em] text-red-300">Sarana & Prasarana</p>
                    <h1 class="mt-2 text-2xl font-black sm:text-3xl">{{ $section === 'qrs' ? 'QR Laporan Aset' : 'Pengelolaan Laporan Aset' }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">{{ $section === 'qrs' ? 'Kelola gedung dan ruangan, lalu cetak poster QR untuk ditempel pada setiap area sekolah.' : 'Verifikasi aduan fasilitas, catat tindak lanjut, dan pantau penyelesaian laporan dari warga sekolah.' }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('super-admin.asset-report-qrs.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-extrabold {{ $section === 'qrs' ? 'bg-white text-slate-950' : 'bg-white/10 text-white hover:bg-white/20' }}">Manajemen QR</a>
                    <a href="{{ route('super-admin.asset-reports.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-extrabold {{ $section === 'reports' ? 'bg-white text-slate-950' : 'bg-white/10 text-white hover:bg-white/20' }}">Daftar Laporan</a>
                </div>
            </div>
        </section>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-bold text-red-700">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700"><p class="font-black">Data belum dapat disimpan:</p><ul class="mt-2 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach([
                ['label' => 'Ruangan Terdaftar', 'value' => $stats['total_locations'], 'class' => 'text-blue-600'],
                ['label' => 'Laporan Baru', 'value' => $stats['new_reports'], 'class' => 'text-red-600'],
                ['label' => 'Dalam Proses', 'value' => $stats['in_progress'], 'class' => 'text-amber-600'],
                ['label' => 'Selesai', 'value' => $stats['completed'], 'class' => 'text-emerald-600'],
            ] as $stat)
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wider text-slate-400">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-3xl font-black {{ $stat['class'] }}">{{ number_format($stat['value']) }}</p>
                </div>
            @endforeach
        </section>

        @if($section === 'qrs')
            <section class="grid gap-5 xl:grid-cols-[1fr_1.6fr]">
                <div class="space-y-5">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between gap-3">
                            <div><h2 class="font-black text-slate-900">Tambah Gedung</h2><p class="mt-1 text-xs text-slate-500">Kelompok utama lokasi sekolah.</p></div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-500">{{ $buildings->count() }} gedung</span>
                        </div>
                        <form method="POST" action="{{ route('super-admin.asset-report-buildings.store') }}" class="mt-4 grid gap-3 sm:grid-cols-2">
                            @csrf
                            <input name="name" required maxlength="100" class="rounded-xl border-slate-300 text-sm" placeholder="Nama gedung">
                            <input name="code" required maxlength="20" class="rounded-xl border-slate-300 text-sm uppercase" placeholder="Kode, mis. GDG-5">
                            <input name="description" maxlength="255" class="rounded-xl border-slate-300 text-sm sm:col-span-2" placeholder="Keterangan gedung (opsional)">
                            <input name="sort_order" type="number" min="0" value="0" class="rounded-xl border-slate-300 text-sm" placeholder="Urutan">
                            <label class="flex items-center gap-2 rounded-xl bg-slate-50 px-4 text-sm font-bold text-slate-700"><input name="is_active" value="1" type="checkbox" checked class="rounded border-slate-300 text-red-600"> Aktif</label>
                            <button class="h-11 rounded-xl bg-slate-900 text-sm font-black text-white sm:col-span-2">Tambah Gedung</button>
                        </form>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="font-black text-slate-900">Daftar Gedung</h2>
                        <div class="mt-4 space-y-3">
                            @foreach($buildings as $building)
                                <details class="group rounded-xl border border-slate-200 p-4">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                                        <div><p class="font-extrabold text-slate-900">{{ $building->name }}</p><p class="text-xs text-slate-500">{{ $building->code }} · {{ $building->locations_count }} ruangan</p></div>
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-black {{ $building->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $building->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </summary>
                                    <form method="POST" action="{{ route('super-admin.asset-report-buildings.update', $building) }}" class="mt-4 grid gap-3 border-t border-slate-100 pt-4">
                                        @csrf @method('PUT')
                                        <input name="name" value="{{ $building->name }}" required class="rounded-lg border-slate-300 text-sm">
                                        <input name="code" value="{{ $building->code }}" required class="rounded-lg border-slate-300 text-sm">
                                        <input name="description" value="{{ $building->description }}" class="rounded-lg border-slate-300 text-sm">
                                        <div class="grid grid-cols-2 gap-3"><input name="sort_order" type="number" value="{{ $building->sort_order }}" class="rounded-lg border-slate-300 text-sm"><label class="flex items-center gap-2 text-sm font-bold"><input name="is_active" value="1" type="checkbox" @checked($building->is_active) class="rounded text-red-600"> Aktif</label></div>
                                        <button class="h-10 rounded-lg bg-amber-500 text-sm font-black text-white">Simpan Gedung</button>
                                    </form>
                                    <form method="POST" action="{{ route('super-admin.asset-report-buildings.destroy', $building) }}" onsubmit="return confirm('Hapus gedung ini?')" class="mt-2">@csrf @method('DELETE')<button class="text-xs font-bold text-red-600">Hapus gedung</button></form>
                                </details>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div><h2 class="font-black text-slate-900">Tambah Ruangan & QR</h2><p class="mt-1 text-xs text-slate-500">QR publik dibuat otomatis dan tetap sama saat data diedit.</p></div>
                        <a href="{{ route('super-admin.asset-report-qrs.print', request()->only('building_id')) }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-black text-white">Cetak PDF A4</a>
                    </div>
                    <form method="POST" action="{{ route('super-admin.asset-report-locations.store') }}" class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @csrf
                        <select name="asset_report_building_id" required class="rounded-xl border-slate-300 text-sm"><option value="">Pilih gedung</option>@foreach($buildings as $building)<option value="{{ $building->id }}">{{ $building->name }}</option>@endforeach</select>
                        <input name="name" required maxlength="120" class="rounded-xl border-slate-300 text-sm" placeholder="Nama ruangan/lokasi">
                        <input name="code" required maxlength="40" class="rounded-xl border-slate-300 text-sm uppercase" placeholder="Kode unik">
                        <select name="type" required class="rounded-xl border-slate-300 text-sm">
                            @foreach(['kelas'=>'Ruang Kelas','toilet'=>'Toilet','laboratorium'=>'Laboratorium','ruang_kerja'=>'Ruang Kerja','perpustakaan'=>'Perpustakaan','uks'=>'UKS','aula'=>'Aula','tempat_ibadah'=>'Tempat Ibadah','kantin'=>'Kantin','gudang'=>'Gudang','pos_keamanan'=>'Pos Keamanan','area_umum'=>'Area Umum','lainnya'=>'Lainnya'] as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                        </select>
                        <input name="floor" maxlength="40" class="rounded-xl border-slate-300 text-sm" placeholder="Lantai/area">
                        <input name="sort_order" type="number" min="0" value="0" class="rounded-xl border-slate-300 text-sm" placeholder="Urutan">
                        <input name="description" maxlength="255" class="rounded-xl border-slate-300 text-sm sm:col-span-2" placeholder="Keterangan lokasi">
                        <label class="flex items-center gap-2 rounded-xl bg-slate-50 px-4 text-sm font-bold"><input name="is_active" value="1" type="checkbox" checked class="rounded text-red-600"> QR aktif</label>
                        <button class="h-11 rounded-xl bg-slate-900 text-sm font-black text-white sm:col-span-2 xl:col-span-3">Buat Ruangan dan QR Code</button>
                    </form>

                    <form method="GET" class="mt-6 flex gap-2 border-t border-slate-200 pt-5">
                        <select name="building_id" class="h-10 flex-1 rounded-xl border-slate-300 text-sm"><option value="">Semua gedung</option>@foreach($buildings as $building)<option value="{{ $building->id }}" @selected(request('building_id') == $building->id)>{{ $building->name }}</option>@endforeach</select>
                        <button class="rounded-xl border border-slate-300 px-4 text-sm font-black text-slate-700">Filter</button>
                    </form>

                    <div class="mt-5 grid gap-4 lg:grid-cols-2">
                        @forelse($locations as $location)
                            <article class="rounded-2xl border border-slate-200 p-4">
                                <div class="flex gap-4">
                                    <div class="shrink-0 rounded-xl border border-slate-200 bg-white p-1">{!! QrCode::size(84)->margin(1)->generate($location->public_url) !!}</div>
                                    <div class="min-w-0 flex-1"><p class="truncate font-black text-slate-900">{{ $location->name }}</p><p class="mt-1 text-xs text-slate-500">{{ $location->building->name }}{{ $location->floor ? ' · '.$location->floor : '' }}</p><p class="mt-2 font-mono text-[11px] text-slate-400">{{ $location->code }}</p><span class="mt-2 inline-block rounded-full px-2 py-1 text-[10px] font-black {{ $location->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $location->is_active ? 'QR Aktif' : 'Nonaktif' }}</span></div>
                                </div>
                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <a target="_blank" href="{{ $location->public_url }}" class="rounded-lg bg-slate-100 px-2 py-2 text-center text-xs font-bold text-slate-700">Buka</a>
                                    <button type="button" onclick="navigator.clipboard.writeText(@js($location->public_url)); this.textContent='Tersalin'" class="rounded-lg bg-slate-100 px-2 py-2 text-xs font-bold text-slate-700">Salin URL</button>
                                    <a href="{{ route('super-admin.asset-report-qrs.print', ['location_id' => $location->id]) }}" class="rounded-lg bg-red-50 px-2 py-2 text-center text-xs font-bold text-red-700">PDF</a>
                                </div>
                                <details class="mt-3"><summary class="cursor-pointer text-xs font-black text-amber-600">Edit ruangan</summary>
                                    <form method="POST" action="{{ route('super-admin.asset-report-locations.update', $location) }}" class="mt-3 grid gap-2">@csrf @method('PUT')
                                        <select name="asset_report_building_id" class="rounded-lg border-slate-300 text-xs">@foreach($buildings as $building)<option value="{{ $building->id }}" @selected($location->asset_report_building_id === $building->id)>{{ $building->name }}</option>@endforeach</select>
                                        <div class="grid grid-cols-2 gap-2"><input name="name" value="{{ $location->name }}" required class="rounded-lg border-slate-300 text-xs"><input name="code" value="{{ $location->code }}" required class="rounded-lg border-slate-300 text-xs"></div>
                                        <div class="grid grid-cols-2 gap-2"><input name="floor" value="{{ $location->floor }}" class="rounded-lg border-slate-300 text-xs"><input name="sort_order" type="number" value="{{ $location->sort_order }}" class="rounded-lg border-slate-300 text-xs"></div>
                                        <select name="type" class="rounded-lg border-slate-300 text-xs">
                                            @foreach(['kelas'=>'Ruang Kelas','toilet'=>'Toilet','laboratorium'=>'Laboratorium','ruang_kerja'=>'Ruang Kerja','perpustakaan'=>'Perpustakaan','uks'=>'UKS','aula'=>'Aula','tempat_ibadah'=>'Tempat Ibadah','kantin'=>'Kantin','gudang'=>'Gudang','pos_keamanan'=>'Pos Keamanan','area_umum'=>'Area Umum','lainnya'=>'Lainnya'] as $value => $label)<option value="{{ $value }}" @selected($location->type === $value)>{{ $label }}</option>@endforeach
                                        </select>
                                        <input name="description" value="{{ $location->description }}" class="rounded-lg border-slate-300 text-xs">
                                        <label class="text-xs font-bold"><input name="is_active" value="1" type="checkbox" @checked($location->is_active) class="rounded text-red-600"> QR aktif</label>
                                        <button class="h-9 rounded-lg bg-amber-500 text-xs font-black text-white">Simpan</button>
                                    </form>
                                    <form method="POST" action="{{ route('super-admin.asset-report-locations.destroy', $location) }}" onsubmit="return confirm('Hapus ruangan ini?')" class="mt-2">@csrf @method('DELETE')<button class="text-xs font-bold text-red-600">Hapus ruangan</button></form>
                                </details>
                            </article>
                        @empty
                            <div class="py-12 text-center text-sm text-slate-500 lg:col-span-2">Belum ada ruangan pada filter ini.</div>
                        @endforelse
                    </div>
                </div>
            </section>
        @else
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" class="grid gap-3 md:grid-cols-5">
                    <input name="search" value="{{ request('search') }}" class="rounded-xl border-slate-300 text-sm md:col-span-2" placeholder="Cari tiket, pelapor, atau aset...">
                    <select name="status" class="rounded-xl border-slate-300 text-sm"><option value="">Semua status</option>@foreach(\App\Models\AssetReport::STATUSES as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select>
                    <select name="urgency" class="rounded-xl border-slate-300 text-sm"><option value="">Semua urgensi</option>@foreach(['rendah'=>'Rendah','normal'=>'Normal','tinggi'=>'Tinggi','darurat'=>'Darurat'] as $value => $label)<option value="{{ $value }}" @selected(request('urgency') === $value)>{{ $label }}</option>@endforeach</select>
                    <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-black text-white">Terapkan Filter</button>
                </form>
            </section>

            <section class="space-y-4">
                @forelse($reports as $report)
                    @php
                        $urgencyClass = match($report->urgency) {'darurat'=>'bg-red-100 text-red-700','tinggi'=>'bg-orange-100 text-orange-700','rendah'=>'bg-slate-100 text-slate-600',default=>'bg-blue-100 text-blue-700'};
                        $statusClass = match($report->status) {'selesai'=>'bg-emerald-100 text-emerald-700','ditolak'=>'bg-slate-200 text-slate-600','diproses'=>'bg-amber-100 text-amber-700','diverifikasi'=>'bg-cyan-100 text-cyan-700',default=>'bg-red-100 text-red-700'};
                    @endphp
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="grid gap-5 p-5 lg:grid-cols-[1fr_280px]">
                            <div>
                                <div class="flex flex-wrap items-center gap-2"><span class="font-mono text-xs font-black text-slate-500">{{ $report->ticket_number }}</span><span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase {{ $urgencyClass }}">{{ $report->urgency }}</span><span class="rounded-full px-2.5 py-1 text-[10px] font-black {{ $statusClass }}">{{ \App\Models\AssetReport::STATUSES[$report->status] ?? $report->status }}</span></div>
                                <h2 class="mt-3 text-lg font-black text-slate-900">{{ $report->asset_name }}</h2>
                                <p class="mt-1 text-xs font-bold text-red-600">{{ $report->location->name }} · {{ $report->location->building->name }}{{ $report->location->floor ? ' · '.$report->location->floor : '' }}</p>
                                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $report->description }}</p>
                                <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-500"><span><b class="text-slate-700">Pelapor:</b> {{ $report->reporter_name }} ({{ str_replace('_', '/', ucfirst($report->reporter_type)) }})</span>@if($report->reporter_identifier)<span><b>ID:</b> {{ $report->reporter_identifier }}</span>@endif @if($report->contact)<span><b>Kontak:</b> {{ $report->contact }}</span>@endif<span>{{ $report->created_at->format('d M Y, H:i') }}</span></div>
                                @if($report->photo_path)<a target="_blank" href="{{ route('super-admin.asset-reports.photo', $report) }}" class="mt-4 inline-flex rounded-lg bg-blue-50 px-3 py-2 text-xs font-black text-blue-700">Lihat foto kondisi</a>@endif
                            </div>
                            <form method="POST" action="{{ route('super-admin.asset-reports.update', $report) }}" class="rounded-xl bg-slate-50 p-4">@csrf @method('PATCH')
                                <label class="text-xs font-black uppercase tracking-wider text-slate-500">Status penanganan<select name="status" class="mt-2 w-full rounded-lg border-slate-300 bg-white text-sm font-bold">@foreach(\App\Models\AssetReport::STATUSES as $value => $label)<option value="{{ $value }}" @selected($report->status === $value)>{{ $label }}</option>@endforeach</select></label>
                                <label class="mt-3 block text-xs font-black uppercase tracking-wider text-slate-500">Catatan petugas<textarea name="admin_notes" rows="4" maxlength="3000" class="mt-2 w-full rounded-lg border-slate-300 bg-white text-sm normal-case" placeholder="Tindakan atau alasan...">{{ $report->admin_notes }}</textarea></label>
                                <button class="mt-3 h-10 w-full rounded-lg bg-red-600 text-sm font-black text-white">Simpan Tindak Lanjut</button>
                                @if($report->handler)<p class="mt-2 text-center text-[10px] text-slate-400">Terakhir oleh {{ $report->handler->name }}</p>@endif
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center"><p class="font-black text-slate-700">Belum ada laporan</p><p class="mt-1 text-sm text-slate-500">Laporan dari QR publik akan muncul di sini.</p></div>
                @endforelse
                <div>{{ $reports->links() }}</div>
            </section>
        @endif
    </div>
</x-app-layout>
