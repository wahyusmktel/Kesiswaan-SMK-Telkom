@extends('pages.docs.layout')

@section('title', 'Panduan Guru Piket')

@section('content')
<div class="space-y-12">
    <!-- Header -->
    <div class="space-y-4">
        <a href="{{ route('docs.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-red-500 uppercase tracking-widest hover:gap-3 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Pusat Panduan
        </a>
        <h1 class="text-4xl md:text-5xl font-black text-gradient">Panduan Role: <span class="text-gradient-red">Guru Piket</span></h1>
        <p class="text-slate-400 max-w-3xl font-medium leading-relaxed">Selamat datang Bapak/Ibu Guru Piket. Panduan ini akan membantu Anda memahami alur kerja dan fitur-fitur yang ada pada sistem SISFO khusus untuk tugas Guru Piket.</p>
    </div>

    <!-- Alur Login -->
    <section class="space-y-8 pt-8">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center border border-white/10">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <h2 class="text-2xl font-bold">1. Cara Masuk ke Sistem (Login)</h2>
        </div>
        
        <div class="space-y-6">
            <div class="step-card">
                <div class="step-number">1</div>
                <p class="text-slate-300 font-medium">Buka halaman utama SISFO dan klik tombol <span class="text-white font-bold">"Masuk ke Sistem"</span> di bagian navigasi atau hero section.</p>
                <div class="mt-4 glass rounded-2xl overflow-hidden border border-white/10 aspect-video relative group">
                    @php
                        $path1 = 'assets/docs/piket/login_step_1.png';
                        $image1 = file_exists(public_path($path1)) ? asset($path1) : 'https://placehold.co/1200x675/0f172a/71717a?text=Screenshot:+Halaman+Welcome';
                    @endphp
                    <img src="{{ $image1 }}" alt="Step 1" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                         <p class="text-[10px] text-white font-bold uppercase tracking-widest">Screenshot: Halaman Welcome / Login Utama</p>
                    </div>
                    <p class="absolute bottom-4 left-4 text-[8px] text-slate-400 bg-black/50 px-2 py-1 rounded">Lokasi: public/assets/docs/piket/login_step_1.png</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <p class="text-slate-300 font-medium">Masukkan <span class="text-white font-bold">Email</span> dan <span class="text-white font-bold">Password</span> akun Guru Piket Anda yang telah didaftarkan oleh admin.</p>
                <div class="mt-4 glass rounded-2xl overflow-hidden border border-white/10 aspect-video relative group">
                    @php
                        $path2 = 'assets/docs/piket/login_step_2.png';
                        $image2 = file_exists(public_path($path2)) ? asset($path2) : 'https://placehold.co/1200x675/0f172a/71717a?text=Screenshot:+Form+Login';
                    @endphp
                    <img src="{{ $image2 }}" alt="Step 2" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest">Screenshot: Form Login</p>
                    </div>
                    <p class="absolute bottom-4 left-4 text-[8px] text-slate-400 bg-black/50 px-2 py-1 rounded">Lokasi: public/assets/docs/piket/login_step_2.png</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Penanganan Terlambat -->
    <section class="space-y-8 pt-8 border-t border-white/5">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center border border-white/10">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h2 class="text-2xl font-bold">2. Penanganan Siswa Terlambat</h2>
        </div>
        
        <div class="space-y-6">
            <div class="step-card">
                <div class="step-number">1</div>
                <p class="text-slate-300 font-medium">Pilih menu <span class="text-white font-bold">"Penanganan Terlambat"</span> pada sidebar (menu samping).</p>
                <div class="mt-4 glass rounded-2xl overflow-hidden border border-white/10 aspect-video relative group">
                    @php
                        $path3 = 'assets/docs/piket/terlambat_step_1.png';
                        $image3 = file_exists(public_path($path3)) ? asset($path3) : 'https://placehold.co/1200x675/0f172a/71717a?text=Screenshot:+Sidebar+Menu';
                    @endphp
                    <img src="{{ $image3 }}" alt="Step 1" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest">Screenshot: Sidebar Menu Penanganan Terlambat</p>
                    </div>
                    <p class="absolute bottom-4 left-4 text-[8px] text-slate-400 bg-black/50 px-2 py-1 rounded">Lokasi: public/assets/docs/piket/terlambat_step_1.png</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <p class="text-slate-300 font-medium">Gunakan kolom pencarian untuk mencari <span class="text-white font-bold">Nama</span> atau <span class="text-white font-bold">NIS</span> siswa yang terlambat.</p>
                <div class="mt-4 glass rounded-2xl overflow-hidden border border-white/10 aspect-video relative group">
                    @php
                        $path4 = 'assets/docs/piket/terlambat_step_2.png';
                        $image4 = file_exists(public_path($path4)) ? asset($path4) : 'https://placehold.co/1200x675/0f172a/71717a?text=Screenshot:+Form+Pencarian';
                    @endphp
                    <img src="{{ $image4 }}" alt="Step 2" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest">Screenshot: Form Pencarian Siswa</p>
                    </div>
                    <p class="absolute bottom-4 left-4 text-[8px] text-slate-400 bg-black/50 px-2 py-1 rounded">Lokasi: public/assets/docs/piket/terlambat_step_2.png</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <p class="text-slate-300 font-medium">Klik tombol <span class="text-white font-bold">"Pilih"</span> pada siswa tersebut, kemudian lengkapi data alasan keterlambatan dan catatan tambahan.</p>
            </div>

            <div class="step-card">
                <div class="step-number">4</div>
                <p class="text-slate-300 font-medium">Setelah disimpan, Anda dapat mencetak <span class="text-white font-bold">Surat Keterangan Terlambat</span> untuk diberikan kepada siswa sebagai bukti masuk kelas.</p>
                <div class="mt-4 glass rounded-2xl overflow-hidden border border-white/10 aspect-video relative group">
                    @php
                        $path5 = 'assets/docs/piket/terlambat_step_4.png';
                        $image5 = file_exists(public_path($path5)) ? asset($path5) : 'https://placehold.co/1200x675/0f172a/71717a?text=Screenshot:+Surat+Terlambat';
                    @endphp
                    <img src="{{ $image5 }}" alt="Step 4" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest">Screenshot: Tampilan Surat PDF Terlambat</p>
                    </div>
                    <p class="absolute bottom-4 left-4 text-[8px] text-slate-400 bg-black/50 px-2 py-1 rounded">Lokasi: public/assets/docs/piket/terlambat_step_4.png</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Persetujuan Izin -->
    <section class="space-y-8 pt-8 border-t border-white/5">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center border border-white/10">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h2 class="text-2xl font-bold">3. Persetujuan Izin Keluar Sekolah</h2>
        </div>
        
        <div class="space-y-6">
            <div class="step-card">
                <div class="step-number">1</div>
                <p class="text-slate-300 font-medium">Menu ini digunakan untuk menyetujui permintaan izin keluar sekolah siswa yang <span class="text-white font-bold">SEBELUMNYA SUDAH DISETUJUI</span> oleh Guru Mata Pelajaran.</p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <p class="text-slate-300 font-medium">Buka menu <span class="text-white font-bold">"Persetujuan Keluar"</span>. Periksa daftar pengajuan yang masuk.</p>
                <div class="mt-4 glass rounded-2xl overflow-hidden border border-white/10 aspect-video relative group">
                    @php
                        $path6 = 'assets/docs/piket/izin_step_2.png';
                        $image6 = file_exists(public_path($path6)) ? asset($path6) : 'https://placehold.co/1200x675/0f172a/71717a?text=Screenshot:+Antrean+Izin';
                    @endphp
                    <img src="{{ $image6 }}" alt="Step 2" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest">Screenshot: Daftar Antrean Persetujuan Izin</p>
                    </div>
                    <p class="absolute bottom-4 left-4 text-[8px] text-slate-400 bg-black/50 px-2 py-1 rounded">Lokasi: public/assets/docs/piket/izin_step_2.png</p>
                </div>
            </div>

            <div class="step-card">
                <div class="step-number">3</div>
                <p class="text-slate-300 font-medium">Klik ikon <span class="text-emerald-500 font-bold">Ceklis</span> untuk menyetujui, atau <span class="text-red-500 font-bold">Silang</span> untuk menolak pengajuan.</p>
            </div>
        </div>
    </section>

    <!-- Absensi Guru -->
    <section class="space-y-8 pt-8 border-t border-white/5">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center border border-white/10">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            </div>
            <h2 class="text-2xl font-bold">4. Pencatatan Absensi Guru</h2>
        </div>
        
        <div class="space-y-6">
            <p class="text-slate-400 font-medium">Guru Piket bertugas mencatat kehadiran Guru di setiap jam pelajaran apabila guru tersebut terlambat masuk kelas atau tidak hadir.</p>
            
            <div class="step-card">
                <div class="step-number">1</div>
                <p class="text-slate-300 font-medium">Buka menu <span class="text-white font-bold">"Absensi Guru"</span>. Pilih tanggal dan rombel/kelas yang ingin dipantau.</p>
            </div>

            <div class="step-card">
                <div class="step-number">2</div>
                <p class="text-slate-300 font-medium">Sistem akan menampilkan jadwal guru di kelas tersebut. Ubah status menjadi <span class="text-white font-bold">"Hadir"</span>, <span class="text-white font-bold">"Terlambat"</span>, atau <span class="text-white font-bold">"Tidak Hadir"</span> sesuai kondisi riil di lapangan.</p>
                <div class="mt-4 glass rounded-2xl overflow-hidden border border-white/10 aspect-video relative group">
                    @php
                        $path7 = 'assets/docs/piket/absensi_guru_step_2.png';
                        $image7 = file_exists(public_path($path7)) ? asset($path7) : 'https://placehold.co/1200x675/0f172a/71717a?text=Screenshot:+Input+Absensi';
                    @endphp
                    <img src="{{ $image7 }}" alt="Step 2" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="text-[10px] text-white font-bold uppercase tracking-widest">Screenshot: Form Input Absensi Guru</p>
                    </div>
                    <p class="absolute bottom-4 left-4 text-[8px] text-slate-400 bg-black/50 px-2 py-1 rounded">Lokasi: public/assets/docs/piket/absensi_guru_step_2.png</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Terakhir -->
    <div class="p-8 glass rounded-[32px] border border-red-500/20 bg-red-500/5">
        <h3 class="text-xl font-bold text-red-500 mb-2 font-outfit uppercase tracking-tighter">Bantuan Teknis</h3>
        <p class="text-slate-300 text-sm font-medium leading-relaxed">Apabila Bapak/Ibu menemukan kendala dalam penggunaan sistem atau ada ketidaksesuaian data, harap segera hubungi <span class="text-white font-bold">Tim IT SMK Telkom Lampung</span> di ruang server atau melalui grup koordinasi.</p>
    </div>
</div>
@endsection
