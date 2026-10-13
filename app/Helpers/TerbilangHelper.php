<?php

namespace App\Helpers;

class TerbilangHelper
{
    /**
     * Konversi angka nominal menjadi kata-kata terbilang bahasa Indonesia.
     *
     * @param float|int|string $angka
     * @return string
     */
    public static function terbilang($angka): string
    {
        $angka = abs((float) $angka);
        $huruf = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
            'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'
        ];

        if ($angka < 12) {
            return $huruf[(int) $angka];
        } elseif ($angka < 20) {
            return self::terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            return self::terbilang((int) ($angka / 10)) . ' Puluh ' . self::terbilang($angka % 10);
        } elseif ($angka < 200) {
            return 'Seratus ' . self::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return self::terbilang((int) ($angka / 100)) . ' Ratus ' . self::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return 'Seribu ' . self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return self::terbilang((int) ($angka / 1000)) . ' Ribu ' . self::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return self::terbilang((int) ($angka / 1000000)) . ' Juta ' . self::terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return self::terbilang((int) ($angka / 1000000000)) . ' Miliar ' . self::terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            return self::terbilang((int) ($angka / 1000000000000)) . ' Triliun ' . self::terbilang(fmod($angka, 1000000000000));
        }

        return 'Angka Terlalu Besar';
    }

    /**
     * Konversi angka nominal rupiah dengan sufiks "Rupiah".
     *
     * @param float|int|string $angka
     * @return string
     */
    public static function terbilangRupiah($angka): string
    {
        $hasil = trim(self::terbilang($angka));
        if ($hasil === '' || $angka == 0) {
            return 'Nol Rupiah';
        }

        // Hapus multiple spasi jika ada
        $hasil = preg_replace('/\s+/', ' ', $hasil);

        return $hasil . ' Rupiah';
    }
}
