<?php

namespace App\Services;

use App\Models\MasterGuru;
use App\Models\MasterSiswa;
use App\Models\PrakerinIndustri;
use App\Models\PrakerinPembimbing;
use App\Models\PrakerinPenempatan;
use App\Models\PrakerinRombel;
use App\Models\PrakerinSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;

class PrakerinRombelImportService
{
    /**
     * Guru alias / ID mapping dictionary for variations in Surat Tugas names.
     */
    protected array $guruMap = [
        'Ahmad Iksan, M.Kom' => 21,
        'Ahmad Ikhsan, M.Kom' => 21,
        'Aji Sakti Kurniawan, S.Kom' => 22,
        'Anang Esa Sulistiawan, S.Kom' => 55,
        'Arohman, S.Kom' => 23,
        'Astri Damayanti, S.Pd' => 24,
        'Azhar Mustofa, S.Kom' => 25,
        'Budi Safta Nugraha, S.Kom' => 26,
        'Dany Rahmatullah, S.Kom' => 27,
        'Dita Safitri, S.Pd' => 28,
        'Erwin Romel, S.Kom' => 29,
        'Fatriade Saputra, S.Kom' => 30,
        'Hari Hartoyo, S.Pd' => 32,
        'Hermawan Rijal Arrasy, S.Kom' => 33,
        'Hermawan Rijal Arasy, S.Kom' => 33,
        'Ihwan Hamid Huzain, S.Kom' => 34,
        'Imam Suhendri, S.Kom' => 36,
        'Khafidh Febriansyah, S.Pd' => 37,
        'Meliya Trisna Wahyuni, S.Pd' => 39,
        'Muhammad Galih Febrian, S.Pd' => 41,
        'Nur Cahyana Aminullah, S.Kom' => 43,
        'Nur Cahyana Aminuallah, S.Kom' => 43,
        'Rendi Syaputra, S.Kom' => 45,
        'Rizky Nurmansyah, S.Pd' => 46,
        'Rizki Nurmansyah, S.Pd' => 46,
        'Shinta Amelia Whardani, S.Pd' => 47,
        'Shinta Amelia Wardhani, S.Pd' => 47,
        'Surya Aditia Pratama, S.Pd' => 63,
        'Tamara Azhar Azizah, S.Pd' => 59,
        'Wahyu Rahmat Hidayat, S.Kom' => 50,
        'Yuni Marlina, S.Pd' => 52,
    ];

    /**
     * Parse an uploaded Excel or CSV file into uniform row arrays.
     */
    public function parseUploadedFile(UploadedFile $file): array
    {
        $rawSheets = Excel::toArray(new class implements ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $file);

        $sheet = $rawSheets[0] ?? [];
        return $this->parseSheetRows($sheet);
    }

    /**
     * Parse 2D sheet rows into standardized student placement rows.
     */
    public function parseSheetRows(array $sheetRows): array
    {
        $headerFound = false;
        $colMap = [
            'guru' => 1,
            'siswa' => 2,
            'nis' => 3,
            'kelas' => 4,
            'lokasi' => 5,
        ];

        $currentGuru = '';
        $parsed = [];

        foreach ($sheetRows as $row) {
            $row = array_map(fn ($v) => trim((string) $v), $row);
            if (empty(array_filter($row))) {
                continue;
            }

            $joined = strtolower(implode(' ', $row));

            // Check for table header row
            if ((str_contains($joined, 'guru') || str_contains($joined, 'pembimbing')) && (str_contains($joined, 'nis') || str_contains($joined, 'siswa'))) {
                $headerFound = true;
                foreach ($row as $idx => $cell) {
                    $cellLower = strtolower($cell);
                    if (str_contains($cellLower, 'guru') || str_contains($cellLower, 'pembimbing')) {
                        $colMap['guru'] = $idx;
                    } elseif (str_contains($cellLower, 'siswa') && !str_contains($cellLower, 'jumlah') && !str_contains($cellLower, 'total')) {
                        $colMap['siswa'] = $idx;
                    } elseif (str_contains($cellLower, 'nis')) {
                        $colMap['nis'] = $idx;
                    } elseif (str_contains($cellLower, 'kelas') || str_contains($cellLower, 'jurusan')) {
                        $colMap['kelas'] = $idx;
                    } elseif (str_contains($cellLower, 'lokasi') || str_contains($cellLower, 'industri') || str_contains($cellLower, 'tempat')) {
                        $colMap['lokasi'] = $idx;
                    }
                }
                continue;
            }

            // Skip summary, header titles, or irrelevant metadata rows
            if (str_contains($joined, 'total seluruh') || str_contains($joined, 'surat perintah') || str_contains($joined, 'periode:')) {
                continue;
            }

            // Determine values from columns
            $guruCell = $row[$colMap['guru']] ?? '';
            $siswaCell = $row[$colMap['siswa']] ?? '';
            $nisCell = $row[$colMap['nis']] ?? '';
            $kelasCell = $row[$colMap['kelas']] ?? '';
            $lokasiCell = $row[$colMap['lokasi']] ?? '';

            // If header was not explicitly flagged yet, check if this looks like a data row with NIS
            if (!$headerFound) {
                // If col 3 is numeric NIS (e.g. 553241026) and student name is not empty
                if (is_numeric($nisCell) && strlen($nisCell) >= 5 && !empty($siswaCell)) {
                    $headerFound = true;
                } else {
                    continue;
                }
            }

            if (!empty($guruCell)) {
                $currentGuru = $guruCell;
            }

            // Validate NIS and student
            if (empty($nisCell) || empty($siswaCell)) {
                continue;
            }

            $parsed[] = [
                'guru' => $currentGuru,
                'siswa' => $siswaCell,
                'nis' => $nisCell,
                'kelas' => $kelasCell,
                'lokasi' => $lokasiCell,
            ];
        }

        return $parsed;
    }

    /**
     * Resolve or create MasterGuru.
     */
    public function resolveGuru(string $rawGuruName): MasterGuru
    {
        $rawGuruName = trim($rawGuruName);

        // Check if explicitly mapped
        if (isset($this->guruMap[$rawGuruName])) {
            $guru = MasterGuru::find($this->guruMap[$rawGuruName]);
            if ($guru) {
                return $guru;
            }
        }

        // Special case for Faalih Qowiy, S.Pd
        if (stripos($rawGuruName, 'Faalih') !== false) {
            $guru = MasterGuru::where('nama_lengkap', 'like', '%Faalih%')->first();
            if ($guru) {
                return $guru;
            }

            // Create User
            $user = User::firstOrCreate(
                ['email' => 'faalih.qowiy@smktelkom-lpg.sch.id'],
                [
                    'name' => 'Faalih Qowiy, S.Pd',
                    'password' => Hash::make('SmkTelkom123!'),
                ]
            );

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Guru Kelas');
            }

            return MasterGuru::create([
                'nama_lengkap' => 'Faalih Qowiy, S.Pd',
                'user_id' => $user->id,
                'employee_category' => 'guru',
                'is_active' => true,
            ]);
        }

        // Search by stripped name
        $stripped = preg_replace('/,.*$/', '', $rawGuruName);
        $guru = MasterGuru::where('nama_lengkap', 'like', '%' . trim($stripped) . '%')->first();

        if ($guru) {
            return $guru;
        }

        // Fallback: create MasterGuru with user
        $slug = Str::slug($rawGuruName);
        $user = User::firstOrCreate(
            ['email' => $slug . '@smktelkom-lpg.sch.id'],
            [
                'name' => $rawGuruName,
                'password' => Hash::make('SmkTelkom123!'),
            ]
        );

        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Guru Kelas');
        }

        return MasterGuru::create([
            'nama_lengkap' => $rawGuruName,
            'user_id' => $user->id,
            'employee_category' => 'guru',
            'is_active' => true,
        ]);
    }

    /**
     * Resolve or create internal PrakerinPembimbing.
     */
    public function resolvePembimbingInternal(MasterGuru $guru): PrakerinPembimbing
    {
        return PrakerinPembimbing::firstOrCreate(
            [
                'tipe' => 'internal',
                'master_guru_id' => $guru->id,
            ],
            [
                'nama' => $guru->nama_lengkap,
                'telepon' => $guru->user?->phone ?? null,
                'email' => $guru->user?->email ?? null,
                'is_active' => true,
            ]
        );
    }

    /**
     * Resolve or create PrakerinIndustri.
     */
    public function resolveIndustri(string $namaLokasi): PrakerinIndustri
    {
        $namaLokasi = trim($namaLokasi);

        // Normalize some common names
        $normalizedNama = $namaLokasi;
        if (strcasecmp($namaLokasi, 'Telkom Witel lampung') === 0) {
            $normalizedNama = 'Telkom Witel Lampung';
        } elseif (strcasecmp($namaLokasi, 'Polda lampung') === 0) {
            $normalizedNama = 'Polda Lampung';
        } elseif (strcasecmp($namaLokasi, 'PGN COM dan Gasnet RO lampung') === 0) {
            $normalizedNama = 'PGN COM dan Gasnet RO Lampung';
        }

        $industri = PrakerinIndustri::where('nama_industri', $normalizedNama)
            ->orWhere('nama_industri', $namaLokasi)
            ->first();

        if ($industri) {
            return $industri;
        }

        $kota = $this->inferKota($normalizedNama);
        $bidang = $this->inferBidangUsaha($normalizedNama);

        return PrakerinIndustri::create([
            'nama_industri' => $normalizedNama,
            'bidang_usaha' => $bidang,
            'alamat' => "Wilayah {$kota}",
            'kota' => $kota,
            'is_mou_active' => true,
        ]);
    }

    /**
     * Infer Kota from Location Name.
     */
    protected function inferKota(string $lokasi): string
    {
        $lower = strtolower($lokasi);
        if (str_contains($lower, 'pesawaran')) return 'Kabupaten Pesawaran';
        if (str_contains($lower, 'pringsewu') || str_contains($lower, 'gadingrejo')) return 'Kabupaten Pringsewu';
        if (str_contains($lower, 'metro')) return 'Kota Metro';
        if (str_contains($lower, 'bandung')) return 'Kota Bandung';
        if (str_contains($lower, 'makasar')) return 'Kota Makassar';
        return 'Kota Bandar Lampung';
    }

    /**
     * Infer Bidang Usaha.
     */
    protected function inferBidangUsaha(string $lokasi): string
    {
        $lower = strtolower($lokasi);
        if (str_contains($lower, 'animasi') || str_contains($lower, 'ventura') || str_contains($lower, 'art studio') || str_contains($lower, 'gamelab')) {
            return 'Multimedia, Desain & Animasi';
        }
        if (str_contains($lower, 'telkom') || str_contains($lower, 'pln') || str_contains($lower, 'jaringan') || str_contains($lower, 'isp') || str_contains($lower, 'frekuensi')) {
            return 'Jaringan Komputer & Telekomunikasi';
        }
        if (str_contains($lower, 'dinas') || str_contains($lower, 'badan') || str_contains($lower, 'polda') || str_contains($lower, 'polres') || str_contains($lower, 'kecamatan') || str_contains($lower, 'pengadilan') || str_contains($lower, 'bps') || str_contains($lower, 'bpn') || str_contains($lower, 'diskominfo')) {
            return 'Instansi Pemerintah / BUMN';
        }
        if (str_contains($lower, 'universitas') || str_contains($lower, 'itba') || str_contains($lower, 'darmajaya') || str_contains($lower, 'bmti')) {
            return 'Pendidikan & Pelatihan';
        }
        return 'Teknologi Informasi & Rekayasa Perangkat Lunak';
    }

    /**
     * Main Import method from parsed CSV/Excel rows.
     *
     * Expected row structure:
     * ['guru' => '...', 'siswa' => '...', 'nis' => '...', 'kelas' => '...', 'lokasi' => '...']
     */
    public function import(array $rows, string $tanggalMulai = '2026-07-06', string $tanggalSelesai = '2026-12-11'): array
    {
        // Update Setting defaults if needed
        $setting = PrakerinSetting::first();
        if ($setting) {
            $setting->update([
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ]);
        }

        $stats = [
            'total_rows' => count($rows),
            'matched_students' => 0,
            'missing_students' => [],
            'industri_count' => 0,
            'rombel_count' => 0,
            'penempatan_count' => 0,
            'guru_count' => 0,
        ];

        // Group rows by [Lokasi][Guru]
        $grouped = [];
        $uniqueGurus = [];

        foreach ($rows as $r) {
            $guruRaw = trim($r['guru'] ?? '');
            $lokasiRaw = trim($r['lokasi'] ?? '');
            $nis = trim($r['nis'] ?? '');
            $siswaRaw = trim($r['siswa'] ?? '');
            $kelasRaw = trim($r['kelas'] ?? '');

            if (!$guruRaw || !$lokasiRaw || !$nis) {
                continue;
            }

            $uniqueGurus[$guruRaw] = true;
            $grouped[$lokasiRaw][$guruRaw][] = [
                'nis' => $nis,
                'nama' => $siswaRaw,
                'kelas' => $kelasRaw,
            ];
        }

        $stats['guru_count'] = count($uniqueGurus);

        DB::transaction(function () use ($grouped, $tanggalMulai, $tanggalSelesai, &$stats) {
            foreach ($grouped as $lokasiName => $guruGroups) {
                // Resolve Industri
                $industri = $this->resolveIndustri($lokasiName);
                $stats['industri_count']++;

                foreach ($guruGroups as $guruName => $studentsList) {
                    // Resolve Guru & Pembimbing Internal
                    $masterGuru = $this->resolveGuru($guruName);
                    $pembimbingInternal = $this->resolvePembimbingInternal($masterGuru);

                    // Build Clean Rombel Name
                    $guruShort = preg_replace('/,.*$/', '', $masterGuru->nama_lengkap);
                    $namaRombel = "PKL - {$industri->nama_industri} ({$guruShort})";

                    // Create or update PrakerinRombel
                    $rombel = PrakerinRombel::firstOrCreate(
                        [
                            'prakerin_industri_id' => $industri->id,
                            'pembimbing_internal_id' => $pembimbingInternal->id,
                        ],
                        [
                            'nama_rombel' => $namaRombel,
                            'pembimbing_external_id' => null,
                            'gunakan_periode_kustom' => true,
                            'tanggal_mulai' => $tanggalMulai,
                            'tanggal_selesai' => $tanggalSelesai,
                            'status' => 'aktif',
                        ]
                    );

                    // Ensure rombel name and active status are set
                    $rombel->update([
                        'nama_rombel' => $namaRombel,
                        'gunakan_periode_kustom' => true,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai,
                        'status' => 'aktif',
                    ]);

                    $stats['rombel_count']++;

                    // Map students to this rombel
                    foreach ($studentsList as $st) {
                        $nis = $st['nis'];
                        $siswa = MasterSiswa::where('nis', $nis)->first();

                        if (!$siswa && !empty($st['nama'])) {
                            $siswa = MasterSiswa::where('nama_lengkap', 'like', '%' . trim($st['nama']) . '%')->first();
                        }

                        if (!$siswa) {
                            $stats['missing_students'][] = "{$st['nama']} (NIS: {$nis})";
                            continue;
                        }

                        $stats['matched_students']++;

                        // Update or create placement
                        PrakerinPenempatan::updateOrCreate(
                            ['master_siswa_id' => $siswa->id],
                            [
                                'prakerin_rombel_id' => $rombel->id,
                                'prakerin_industri_id' => $industri->id,
                                'master_guru_id' => $masterGuru->id,
                                'nama_pembimbing_industri' => '-',
                                'tanggal_mulai' => $tanggalMulai,
                                'tanggal_selesai' => $tanggalSelesai,
                                'status' => 'aktif',
                            ]
                        );

                        $stats['penempatan_count']++;
                    }
                }
            }
        });

        return $stats;
    }
}
