<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DapodikSiswa extends Model
{
    protected $table = 'dapodik_siswa';

    protected $fillable = [
        'master_siswa_id',
        // Data Pribadi
        'nipd', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama',
        // Alamat
        'alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos',
        // Kontak dan Transportasi
        'jenis_tinggal', 'alat_transportasi', 'telepon', 'hp', 'email',
        // Dokumen
        'skhun', 'no_peserta_ujian_nasional', 'no_seri_ijazah', 'no_registrasi_akta_lahir', 'no_kk',
        // KPS/KIP
        'penerima_kps', 'no_kps', 'penerima_kip', 'nomor_kip', 'nama_di_kip', 'nomor_kks',
        // PIP
        'layak_pip', 'alasan_layak_pip',
        // Bank
        'bank', 'nomor_rekening_bank', 'rekening_atas_nama',
        // Data Ayah
        'nama_ayah', 'tahun_lahir_ayah', 'jenjang_pendidikan_ayah', 'pekerjaan_ayah', 'penghasilan_ayah', 'nik_ayah',
        // Data Ibu
        'nama_ibu', 'tahun_lahir_ibu', 'jenjang_pendidikan_ibu', 'pekerjaan_ibu', 'penghasilan_ibu', 'nik_ibu',
        // Data Wali
        'nama_wali', 'tahun_lahir_wali', 'jenjang_pendidikan_wali', 'pekerjaan_wali', 'penghasilan_wali', 'nik_wali',
        // Lainnya
        'rombel_saat_ini', 'kebutuhan_khusus', 'sekolah_asal', 'anak_ke_berapa',
        'lintang', 'bujur', 'berat_badan', 'tinggi_badan', 'lingkar_kepala',
        'jumlah_saudara_kandung', 'jarak_rumah_ke_sekolah',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'anak_ke_berapa' => 'integer',
        'berat_badan' => 'integer',
        'tinggi_badan' => 'integer',
        'lingkar_kepala' => 'integer',
        'jumlah_saudara_kandung' => 'integer',
        'jarak_rumah_ke_sekolah' => 'decimal:2',
    ];

    public function masterSiswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
}
