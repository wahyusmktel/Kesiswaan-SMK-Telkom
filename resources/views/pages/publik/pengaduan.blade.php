<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaduan Orang Tua — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- TomSelect CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #fce7f3 0%, #fef3c7 50%, #d1fae5 100%);
        }
        /* Custom TomSelect Red Theme */
        .ts-control {
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            border-color: #e5e7eb !important;
        }
        .ts-control.focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
        }
        .ts-dropdown {
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 gradient-bg min-h-screen flex flex-col items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-gray-800 mb-2">Pusat Pengaduan <span class="text-red-600">Orang Tua</span></h1>
            <p class="text-gray-600">Sampaikan aspirasi, kritik, atau saran Anda demi kemajuan sekolah kami.</p>
        </div>

        <div class="glass rounded-3xl shadow-2xl overflow-hidden">
            <div class="p-8">
                <form action="{{ route('pengaduan.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Pelapor --}}
                        <div>
                            <label for="nama_pelapor" class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap Anda</label>
                            <input type="text" name="nama_pelapor" id="nama_pelapor" value="{{ old('nama_pelapor') }}" required
                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring focus:ring-red-200 transition-all placeholder-gray-400"
                                placeholder="Contoh: Budi Santoso">
                            @error('nama_pelapor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Hubungan --}}
                        <div>
                            <label for="hubungan" class="block text-sm font-semibold text-gray-700 mb-1">Hubungan</label>
                            <select name="hubungan" id="hubungan" required
                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring focus:ring-red-200 transition-all cursor-pointer">
                                <option value="" disabled selected>Pilih Hubungan</option>
                                <option value="Ayah" {{ old('hubungan') == 'Ayah' ? 'selected' : '' }}>Ayah</option>
                                <option value="Ibu" {{ old('hubungan') == 'Ibu' ? 'selected' : '' }}>Ibu</option>
                                <option value="Wali" {{ old('hubungan') == 'Wali' ? 'selected' : '' }}>Wali</option>
                                <option value="Lainnya" {{ old('hubungan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('hubungan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nomor WA --}}
                        <div>
                            <label for="nomor_wa" class="block text-sm font-semibold text-gray-700 mb-1">Nomor WhatsApp</label>
                            <input type="text" name="nomor_wa" id="nomor_wa" value="{{ old('nomor_wa') }}" required
                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring focus:ring-red-200 transition-all placeholder-gray-400"
                                placeholder="Contoh: 08123456789">
                            @error('nomor_wa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-1">Kategori Pengaduan</label>
                            <select name="kategori" id="kategori" required
                                class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring focus:ring-red-200 transition-all cursor-pointer">
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="Fasilitas" {{ old('kategori') == 'Fasilitas' ? 'selected' : '' }}>Fasilitas</option>
                                <option value="Guru / Staff" {{ old('kategori') == 'Guru / Staff' ? 'selected' : '' }}>Guru / Staff</option>
                                <option value="Kurikulum" {{ old('kategori') == 'Kurikulum' ? 'selected' : '' }}>Kurikulum</option>
                                <option value="Kesiswaan" {{ old('kategori') == 'Kesiswaan' ? 'selected' : '' }}>Kesiswaan</option>
                                <option value="Kebersihan" {{ old('kategori') == 'Kebersihan' ? 'selected' : '' }}>Kebersihan</option>
                                <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Siswa --}}
                        <div>
                            <label for="select_siswa" class="block text-sm font-semibold text-gray-700 mb-1">Nama Siswa (Anak)</label>
                            <select id="select_siswa" placeholder="Cari nama siswa..." required autocomplete="off">
                                <option value="">Cari nama siswa...</option>
                                @foreach($siswa as $s)
                                    @php
                                        $namaKelas = $s->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas';
                                    @endphp
                                    <option value="{{ $s->id }}" data-nama="{{ $s->nama_lengkap }}" data-kelas="{{ $namaKelas }}"
                                        {{ old('nama_siswa') == $s->nama_lengkap ? 'selected' : '' }}>
                                        {{ $s->nama_lengkap }} ({{ $namaKelas }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="nama_siswa" id="nama_siswa_hidden" value="{{ old('nama_siswa') }}">
                            @error('nama_siswa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kelas Siswa --}}
                        <div>
                            <label for="kelas_siswa" class="block text-sm font-semibold text-gray-700 mb-1">Kelas</label>
                            <input type="text" name="kelas_siswa" id="kelas_siswa" value="{{ old('kelas_siswa') }}" required readonly
                                class="w-full px-4 py-3 rounded-xl border-gray-200 bg-gray-50 focus:border-red-500 focus:ring focus:ring-red-200 transition-all placeholder-gray-400 group-read-only:cursor-not-allowed"
                                placeholder="Terisi otomatis">
                            @error('kelas_siswa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Isi Pengaduan --}}
                    <div>
                        <label for="isi_pengaduan" class="block text-sm font-semibold text-gray-700 mb-1">Detail Pengaduan</label>
                        <textarea name="isi_pengaduan" id="isi_pengaduan" rows="5" required
                            class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring focus:ring-red-200 transition-all placeholder-gray-400"
                            placeholder="Ceritakan secara detail pengaduan atau saran Anda...">{{ old('isi_pengaduan') }}</textarea>
                        @error('isi_pengaduan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div>
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-red-200">
                            Kirim Pengaduan Sekarang
                        </button>
                    </div>
                </form>
            </div>
            <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
                <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-red-600 transition-colors">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </div>

        <p class="text-center text-gray-500 mt-8 text-sm">
            &copy; 2025 {{ config('app.name') }}. Semua pengaduan akan dijaga kerahasiaannya.
        </p>
    </div>

    @include('sweetalert::alert')

    {{-- TomSelect JS --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var select = new TomSelect('#select_siswa', {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                onChange: function(value) {
                    var item = this.options[value];
                    if (item) {
                        // TomSelect picks up data-* attributes and adds them to the item object
                        // but sometimes they are nested or require specific naming.
                        // We'll use the values we know are there.
                        document.getElementById('nama_siswa_hidden').value = item.nama || '';
                        document.getElementById('kelas_siswa').value = item.kelas || '';
                    } else {
                        document.getElementById('nama_siswa_hidden').value = '';
                        document.getElementById('kelas_siswa').value = '';
                    }
                }
            });

            // Trigger change if there is old value (for validation errors)
            if (select.getValue()) {
                // We need to wait a bit for TomSelect to fully initialize
                setTimeout(function() {
                    var val = select.getValue();
                    var item = select.options[val];
                    if (item) {
                        document.getElementById('nama_siswa_hidden').value = item.nama || '';
                        document.getElementById('kelas_siswa').value = item.kelas || '';
                    }
                }, 100);
            }
        });
    </script>
</body>
</html>
