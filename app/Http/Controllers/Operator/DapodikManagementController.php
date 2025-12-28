<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\DapodikSiswa;
use App\Models\DapodikSyncHistory;
use App\Models\MasterSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DapodikManagementController extends Controller
{
    /**
     * Display the Dapodik management page.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $dapodik = DapodikSiswa::with('masterSiswa')
            ->when($search, function ($query) use ($search) {
                $query->where('nipd', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhereHas('masterSiswa', function ($q) use ($search) {
                        $q->where('nama_lengkap', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $syncHistory = DapodikSyncHistory::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        $stats = [
            'total_dapodik' => DapodikSiswa::count(),
            'total_siswa' => MasterSiswa::count(),
            'synced_today' => DapodikSyncHistory::whereDate('created_at', today())->count(),
        ];
        
        return view('pages.operator.dapodik.index', compact('dapodik', 'syncHistory', 'stats'));
    }

    /**
     * Import Dapodik data from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file_import');
            
            // Read Excel using PhpSpreadsheet via Laravel Excel or simple CSV
            $data = $this->parseExcelFile($file);
            
            $inserted = 0;
            $updated = 0;
            $failed = 0;
            $failedRecords = [];
            $rowNumber = 1; // Start from 1 because row 0 is header
            
            DB::beginTransaction();
            
            foreach ($data as $row) {
                $rowNumber++;
                try {
                    // Skip header row or empty rows
                    if (empty($row['nipd']) && empty($row['nisn']) && empty($row['nama'])) {
                        continue;
                    }
                    
                    // Validate required fields
                    if (empty($row['nipd'])) {
                        throw new \Exception('NIPD tidak boleh kosong');
                    }
                    
                    if (empty($row['nama'])) {
                        throw new \Exception('Nama tidak boleh kosong');
                    }
                    
                    // Find master_siswa by NIS (NIPD in Dapodik)
                    $masterSiswa = MasterSiswa::where('nis', $row['nipd'])->first();
                    
                    if (!$masterSiswa) {
                        // Create new master_siswa
                        $masterSiswa = MasterSiswa::create([
                            'nis' => $row['nipd'],
                            'nama_lengkap' => $row['nama'] ?? '',
                            'jenis_kelamin' => $row['jk'] ?? 'L',
                            'tempat_lahir' => $row['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
                            'alamat' => $row['alamat'] ?? null,
                        ]);
                        $inserted++;
                    }
                    
                    // Update or create Dapodik data
                    DapodikSiswa::updateOrCreate(
                        ['master_siswa_id' => $masterSiswa->id],
                        $this->mapRowToDapodik($row)
                    );
                    
                    // Update last_synced_at
                    $masterSiswa->update(['last_synced_at' => now()]);
                    
                    if (!$inserted || $masterSiswa->wasRecentlyCreated === false) {
                        $updated++;
                    }
                    
                } catch (\Exception $e) {
                    $failed++;
                    $errorMessage = $e->getMessage();
                    $recommendation = $this->getErrorRecommendation($errorMessage);
                    
                    $failedRecords[] = [
                        'row' => $rowNumber,
                        'nipd' => $row['nipd'] ?? '-',
                        'nama' => $row['nama'] ?? '-',
                        'error' => $errorMessage,
                        'recommendation' => $recommendation,
                    ];
                    
                    \Log::error("Dapodik import row {$rowNumber} error: " . $errorMessage);
                }
            }
            
            // Record sync history
            DapodikSyncHistory::create([
                'user_id' => Auth::id(),
                'type' => 'import',
                'total_records' => count($data),
                'inserted_count' => $inserted,
                'updated_count' => $updated,
                'failed_count' => $failed,
                'notes' => 'Import dari file: ' . $file->getClientOriginalName(),
            ]);
            
            DB::commit();
            
            return redirect()->route('operator.dapodik.index')
                ->with('success', "Import berhasil! {$inserted} data baru, {$updated} data diperbarui, {$failed} gagal.")
                ->with('failed_records', $failedRecords);
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Dapodik import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    /**
     * Get recommendation based on error message.
     */
    private function getErrorRecommendation($errorMessage)
    {
        $recommendations = [
            'NIPD tidak boleh kosong' => 'Pastikan kolom NIPD terisi untuk setiap baris data siswa.',
            'Nama tidak boleh kosong' => 'Pastikan kolom Nama terisi untuk setiap baris data siswa.',
            'Duplicate entry' => 'Data dengan NIPD yang sama sudah ada. Hapus duplikasi atau gunakan NIPD yang berbeda.',
            'Data too long' => 'Data yang dimasukkan terlalu panjang. Periksa panjang karakter setiap field.',
            'Incorrect date value' => 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD atau DD/MM/YYYY.',
            'Invalid data' => 'Data tidak valid. Periksa format dan tipe data yang dimasukkan.',
        ];

        foreach ($recommendations as $errorKey => $recommendation) {
            if (stripos($errorMessage, $errorKey) !== false) {
                return $recommendation;
            }
        }

        return 'Periksa kembali data pada baris ini dan pastikan format sesuai dengan template.';
    }

    /**
     * Sync Dapodik data with Master Siswa.
     */
    public function sync(Request $request)
    {
        try {
            $inserted = 0;
            $updated = 0;
            $failed = 0;
            
            DB::beginTransaction();
            
            // Get all Dapodik data that has NIPD
            $dapodikRecords = DapodikSiswa::whereNotNull('nipd')->get();
            
            foreach ($dapodikRecords as $dapodik) {
                try {
                    // Find or create master_siswa
                    $masterSiswa = MasterSiswa::where('nis', $dapodik->nipd)->first();
                    
                    if ($masterSiswa) {
                        // Update existing master_siswa with dapodik data
                        $masterSiswa->update([
                            'nama_lengkap' => $dapodik->masterSiswa->nama_lengkap ?? $masterSiswa->nama_lengkap,
                            'jenis_kelamin' => $dapodik->jenis_kelamin ?? $masterSiswa->jenis_kelamin,
                            'tempat_lahir' => $dapodik->tempat_lahir ?? $masterSiswa->tempat_lahir,
                            'tanggal_lahir' => $dapodik->tanggal_lahir ?? $masterSiswa->tanggal_lahir,
                            'alamat' => $dapodik->alamat ?? $masterSiswa->alamat,
                            'last_synced_at' => now(),
                        ]);
                        
                        // Link dapodik to master_siswa if not linked
                        if ($dapodik->master_siswa_id !== $masterSiswa->id) {
                            $dapodik->update(['master_siswa_id' => $masterSiswa->id]);
                        }
                        
                        $updated++;
                    } else {
                        // Create new master_siswa from dapodik data
                        $newMasterSiswa = MasterSiswa::create([
                            'nis' => $dapodik->nipd,
                            'nama_lengkap' => $dapodik->masterSiswa->nama_lengkap ?? 'Nama Belum Diisi',
                            'jenis_kelamin' => $dapodik->jenis_kelamin ?? 'L',
                            'tempat_lahir' => $dapodik->tempat_lahir,
                            'tanggal_lahir' => $dapodik->tanggal_lahir,
                            'alamat' => $dapodik->alamat,
                            'last_synced_at' => now(),
                        ]);
                        
                        $dapodik->update(['master_siswa_id' => $newMasterSiswa->id]);
                        $inserted++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                    \Log::error('Dapodik sync row error: ' . $e->getMessage());
                }
            }
            
            // Record sync history
            DapodikSyncHistory::create([
                'user_id' => Auth::id(),
                'type' => 'sync',
                'total_records' => $dapodikRecords->count(),
                'inserted_count' => $inserted,
                'updated_count' => $updated,
                'failed_count' => $failed,
                'notes' => 'Sinkronisasi manual',
            ]);
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Sinkronisasi berhasil! {$inserted} data baru, {$updated} data diperbarui, {$failed} gagal.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Dapodik sync error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Download import template Excel file.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Nama', 'NIPD', 'JK', 'NISN', 'Tempat Lahir', 'Tanggal Lahir', 'NIK', 'Agama',
            'Alamat', 'RT', 'RW', 'Dusun', 'Kelurahan', 'Kecamatan', 'Kode Pos',
            'Jenis Tinggal', 'Alat Transportasi', 'Telepon', 'HP', 'E-Mail',
            'SKHUN', 'Penerima KPS', 'No. KPS',
            'Nama Ayah', 'Tahun Lahir Ayah', 'Jenjang Pendidikan Ayah', 'Pekerjaan Ayah', 'Penghasilan Ayah', 'NIK Ayah',
            'Nama Ibu', 'Tahun Lahir Ibu', 'Jenjang Pendidikan Ibu', 'Pekerjaan Ibu', 'Penghasilan Ibu', 'NIK Ibu',
            'Nama Wali', 'Tahun Lahir Wali', 'Jenjang Pendidikan Wali', 'Pekerjaan Wali', 'Penghasilan Wali', 'NIK Wali',
            'Rombel Saat Ini', 'No Peserta Ujian Nasional', 'No Seri Ijazah',
            'Penerima KIP', 'Nomor KIP', 'Nama di KIP', 'Nomor KKS',
            'No Registrasi Akta Lahir', 'Bank', 'Nomor Rekening Bank', 'Rekening Atas Nama',
            'Layak PIP (Usulan dari Sekolah)', 'Alasan Layak PIP',
            'Kebutuhan Khusus', 'Sekolah Asal', 'Anak Ke-berapa',
            'Lintang', 'Bujur', 'No KK',
            'Berat Badan', 'Tinggi Badan', 'Lingkar Kepala',
            'Jml. Saudara Kandung', 'Jarak Rumah ke Sekolah (KM)'
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Dapodik');

        // Set headers
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '1', $header);
            
            // Style header
            $sheet->getStyle($column . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669']
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);
            
            // Auto width
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add sample data row
        $sampleData = [
            'Contoh Nama Siswa', '12345', 'L', '0012345678', 'Bandar Lampung', '2008-05-15', '1871234567890123', 'Islam',
            'Jl. Contoh No. 123', '001', '002', 'Dusun Contoh', 'Kel. Contoh', 'Kec. Contoh', '35123',
            'Bersama Orang Tua', 'Sepeda Motor', '0721123456', '081234567890', 'contoh@email.com',
            '', 'Tidak', '',
            'Nama Ayah', '1975', 'S1', 'Wiraswasta', 'Rp. 2,000,000 - Rp. 4,999,999', '1871234567890001',
            'Nama Ibu', '1978', 'SMA', 'Tidak Bekerja', 'Tidak Berpenghasilan', '1871234567890002',
            '', '', '', '', '', '',
            'X RPL 1', '', '',
            'Ya', '1234567890123456', 'Contoh Nama Siswa', '',
            '', 'BRI', '1234567890', 'Contoh Nama Siswa',
            'Tidak', '',
            'Tidak Ada', 'SMP Negeri 1 Contoh', '2',
            '-5.4252', '105.2587', '1871234567890003',
            '45', '155', '50',
            '2', '5.5'
        ];

        foreach ($sampleData as $index => $value) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '2', $value);
            $sheet->getStyle($column . '2')->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ],
                'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']]
            ]);
        }

        // Create file response
        $filename = 'template_import_dapodik_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    /**
     * Parse Excel file and return array of data.
     */
    private function parseExcelFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $data = [];
        
        if ($extension === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            $headers = null;
            
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (!$headers) {
                    $headers = array_map(function ($h) {
                        return $this->normalizeHeader($h);
                    }, $row);
                    continue;
                }
                
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $rowData[$header] = $row[$index] ?? null;
                }
                $data[] = $rowData;
            }
            fclose($handle);
        } else {
            // Use PhpSpreadsheet for xlsx/xls
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $spreadsheet = $reader->load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $headers = array_map(function ($h) {
                return $this->normalizeHeader($h);
            }, $rows[0]);
            
            for ($i = 1; $i < count($rows); $i++) {
                $rowData = [];
                foreach ($headers as $index => $header) {
                    $rowData[$header] = $rows[$i][$index] ?? null;
                }
                $data[] = $rowData;
            }
        }
        
        return $data;
    }

    /**
     * Normalize header names to match database columns.
     */
    private function normalizeHeader($header)
    {
        $header = trim(strtolower($header));
        $header = preg_replace('/[\r\n]+/', ' ', $header);
        
        $mapping = [
            'nama' => 'nama',
            'nipd' => 'nipd',
            'jk' => 'jk',
            'nisn' => 'nisn',
            'tempat lahir' => 'tempat_lahir',
            'tanggal lahir' => 'tanggal_lahir',
            'nik' => 'nik',
            'agama' => 'agama',
            'alamat' => 'alamat',
            'rt' => 'rt',
            'rw' => 'rw',
            'dusun' => 'dusun',
            'kelurahan' => 'kelurahan',
            'kecamatan' => 'kecamatan',
            'kode pos' => 'kode_pos',
            'jenis tinggal' => 'jenis_tinggal',
            'alat transportasi' => 'alat_transportasi',
            'telepon' => 'telepon',
            'hp' => 'hp',
            'e-mail' => 'email',
            'skhun' => 'skhun',
            'penerima kps' => 'penerima_kps',
            'no. kps' => 'no_kps',
            'nama ayah' => 'nama_ayah',
            'tahun lahir ayah' => 'tahun_lahir_ayah',
            'jenjang pendidikan ayah' => 'jenjang_pendidikan_ayah',
            'pekerjaan ayah' => 'pekerjaan_ayah',
            'penghasilan ayah' => 'penghasilan_ayah',
            'nik ayah' => 'nik_ayah',
            'nama ibu' => 'nama_ibu',
            'tahun lahir ibu' => 'tahun_lahir_ibu',
            'jenjang pendidikan ibu' => 'jenjang_pendidikan_ibu',
            'pekerjaan ibu' => 'pekerjaan_ibu',
            'penghasilan ibu' => 'penghasilan_ibu',
            'nik ibu' => 'nik_ibu',
            'nama wali' => 'nama_wali',
            'tahun lahir wali' => 'tahun_lahir_wali',
            'jenjang pendidikan wali' => 'jenjang_pendidikan_wali',
            'pekerjaan wali' => 'pekerjaan_wali',
            'penghasilan wali' => 'penghasilan_wali',
            'nik wali' => 'nik_wali',
            'rombel saat ini' => 'rombel_saat_ini',
            'no peserta ujian nasional' => 'no_peserta_ujian_nasional',
            'no seri ijazah' => 'no_seri_ijazah',
            'penerima kip' => 'penerima_kip',
            'nomor kip' => 'nomor_kip',
            'nama di kip' => 'nama_di_kip',
            'nomor kks' => 'nomor_kks',
            'no registrasi akta lahir' => 'no_registrasi_akta_lahir',
            'bank' => 'bank',
            'nomor rekening bank' => 'nomor_rekening_bank',
            'rekening atas nama' => 'rekening_atas_nama',
            'layak pip (usulan dari sekolah)' => 'layak_pip',
            'alasan layak pip' => 'alasan_layak_pip',
            'kebutuhan khusus' => 'kebutuhan_khusus',
            'sekolah asal' => 'sekolah_asal',
            'anak ke-berapa' => 'anak_ke_berapa',
            'lintang' => 'lintang',
            'bujur' => 'bujur',
            'no kk' => 'no_kk',
            'berat badan' => 'berat_badan',
            'tinggi badan' => 'tinggi_badan',
            'lingkar kepala' => 'lingkar_kepala',
            'jml. saudara kandung' => 'jumlah_saudara_kandung',
            'jarak rumah ke sekolah (km)' => 'jarak_rumah_ke_sekolah',
        ];
        
        return $mapping[$header] ?? str_replace([' ', '.', '-'], '_', $header);
    }

    /**
     * Map Excel row to Dapodik fields.
     */
    private function mapRowToDapodik($row)
    {
        return [
            'nipd' => $row['nipd'] ?? null,
            'nisn' => $row['nisn'] ?? null,
            'nik' => $row['nik'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? null),
            'jenis_kelamin' => $row['jk'] ?? null,
            'agama' => $row['agama'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'rt' => $row['rt'] ?? null,
            'rw' => $row['rw'] ?? null,
            'dusun' => $row['dusun'] ?? null,
            'kelurahan' => $row['kelurahan'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kode_pos' => $row['kode_pos'] ?? null,
            'jenis_tinggal' => $row['jenis_tinggal'] ?? null,
            'alat_transportasi' => $row['alat_transportasi'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'hp' => $row['hp'] ?? null,
            'email' => $row['email'] ?? null,
            'skhun' => $row['skhun'] ?? null,
            'no_peserta_ujian_nasional' => $row['no_peserta_ujian_nasional'] ?? null,
            'no_seri_ijazah' => $row['no_seri_ijazah'] ?? null,
            'no_registrasi_akta_lahir' => $row['no_registrasi_akta_lahir'] ?? null,
            'no_kk' => $row['no_kk'] ?? null,
            'penerima_kps' => $row['penerima_kps'] ?? null,
            'no_kps' => $row['no_kps'] ?? null,
            'penerima_kip' => $row['penerima_kip'] ?? null,
            'nomor_kip' => $row['nomor_kip'] ?? null,
            'nama_di_kip' => $row['nama_di_kip'] ?? null,
            'nomor_kks' => $row['nomor_kks'] ?? null,
            'layak_pip' => $row['layak_pip'] ?? null,
            'alasan_layak_pip' => $row['alasan_layak_pip'] ?? null,
            'bank' => $row['bank'] ?? null,
            'nomor_rekening_bank' => $row['nomor_rekening_bank'] ?? null,
            'rekening_atas_nama' => $row['rekening_atas_nama'] ?? null,
            'nama_ayah' => $row['nama_ayah'] ?? null,
            'tahun_lahir_ayah' => $row['tahun_lahir_ayah'] ?? null,
            'jenjang_pendidikan_ayah' => $row['jenjang_pendidikan_ayah'] ?? null,
            'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? null,
            'penghasilan_ayah' => $row['penghasilan_ayah'] ?? null,
            'nik_ayah' => $row['nik_ayah'] ?? null,
            'nama_ibu' => $row['nama_ibu'] ?? null,
            'tahun_lahir_ibu' => $row['tahun_lahir_ibu'] ?? null,
            'jenjang_pendidikan_ibu' => $row['jenjang_pendidikan_ibu'] ?? null,
            'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? null,
            'penghasilan_ibu' => $row['penghasilan_ibu'] ?? null,
            'nik_ibu' => $row['nik_ibu'] ?? null,
            'nama_wali' => $row['nama_wali'] ?? null,
            'tahun_lahir_wali' => $row['tahun_lahir_wali'] ?? null,
            'jenjang_pendidikan_wali' => $row['jenjang_pendidikan_wali'] ?? null,
            'pekerjaan_wali' => $row['pekerjaan_wali'] ?? null,
            'penghasilan_wali' => $row['penghasilan_wali'] ?? null,
            'nik_wali' => $row['nik_wali'] ?? null,
            'rombel_saat_ini' => $row['rombel_saat_ini'] ?? null,
            'kebutuhan_khusus' => $row['kebutuhan_khusus'] ?? null,
            'sekolah_asal' => $row['sekolah_asal'] ?? null,
            'anak_ke_berapa' => is_numeric($row['anak_ke_berapa'] ?? null) ? (int)$row['anak_ke_berapa'] : null,
            'lintang' => $row['lintang'] ?? null,
            'bujur' => $row['bujur'] ?? null,
            'berat_badan' => is_numeric($row['berat_badan'] ?? null) ? (int)$row['berat_badan'] : null,
            'tinggi_badan' => is_numeric($row['tinggi_badan'] ?? null) ? (int)$row['tinggi_badan'] : null,
            'lingkar_kepala' => is_numeric($row['lingkar_kepala'] ?? null) ? (int)$row['lingkar_kepala'] : null,
            'jumlah_saudara_kandung' => is_numeric($row['jumlah_saudara_kandung'] ?? null) ? (int)$row['jumlah_saudara_kandung'] : null,
            'jarak_rumah_ke_sekolah' => is_numeric($row['jarak_rumah_ke_sekolah'] ?? null) ? (float)$row['jarak_rumah_ke_sekolah'] : null,
        ];
    }

    /**
     * Parse date from various formats.
     */
    private function parseDate($date)
    {
        if (empty($date)) return null;
        
        try {
            if (is_numeric($date)) {
                // Excel serial date
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
