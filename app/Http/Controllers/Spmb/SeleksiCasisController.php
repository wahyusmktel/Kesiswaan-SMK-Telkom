<?php

namespace App\Http\Controllers\Spmb;

use App\Http\Controllers\Controller;
use App\Models\SpmbPesertaSeleksi;
use App\Models\SpmbTesButaWarnaJawaban;
use App\Models\SpmbTesButaWarnaSesi;
use App\Models\SpmbTesButaWarnaSoal;
use App\Models\SpmbTpaJawaban;
use App\Models\SpmbTpaKategori;
use App\Models\SpmbTpaSesi;
use App\Models\SpmbTpaSoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SeleksiCasisController extends Controller
{
    /**
     * Mapel slug to database code mapping and sequential order.
     */
    protected array $mapelSequence = [
        'mtk'   => ['kode' => 'MTK',   'urutan' => 1, 'next' => 'bing',  'prev' => null,   'nama' => 'Matematika'],
        'bing'  => ['kode' => 'BING',  'urutan' => 2, 'next' => 'bindo', 'prev' => 'mtk',  'nama' => 'Bahasa Inggris'],
        'bindo' => ['kode' => 'BINDO', 'urutan' => 3, 'next' => 'tik',   'prev' => 'bing', 'nama' => 'Bahasa Indonesia'],
        'tik'   => ['kode' => 'TIK',   'urutan' => 4, 'next' => null,    'prev' => 'bindo','nama' => 'Dasar TIK'],
    ];

    /**
     * Get candidate associated with currently authenticated user.
     */
    protected function getPeserta(): ?SpmbPesertaSeleksi
    {
        if (! Auth::check()) {
            return null;
        }

        return SpmbPesertaSeleksi::with(['tanggal.kelompok', 'tesButaWarnaSesi', 'tpaSesi'])
            ->where('user_id', Auth::id())
            ->first();
    }

    /**
     * Display CBT Login page (TKA Kemdikbud style).
     */
    public function loginView()
    {
        if (Auth::check()) {
            $peserta = $this->getPeserta();
            if ($peserta) {
                return redirect()->route('seleksi.dashboard');
            }
        }

        return view('pages.spmb.seleksi.login');
    }

    /**
     * Authenticate candidate using Username (5-digit or No Pendaftaran) & Password.
     */
    public function loginSubmit(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Nomor Peserta / Username wajib diisi.',
            'password.required' => 'Kata Sandi / Token wajib diisi.',
        ]);

        $usernameInput = trim($validated['username']);
        $passwordInput = trim($validated['password']);

        // Find candidate by raw_password (5-digit), nomor_pendaftaran, or email
        $peserta = SpmbPesertaSeleksi::where('raw_password', $usernameInput)
            ->orWhere('nomor_pendaftaran', $usernameInput)
            ->orWhereHas('user', function ($q) use ($usernameInput) {
                $q->where('email', $usernameInput)
                    ->orWhere('email', $usernameInput . '@spmb.smktelkom-lpg.sch.id');
            })
            ->first();

        if (! $peserta || ! $peserta->user) {
            return back()->withInput($request->only('username'))->withErrors([
                'auth_failed' => 'Data peserta seleksi tidak ditemukan. Pastikan Anda memasukkan Nomor Peserta / Username yang tertera pada kartu pendaftaran.',
            ]);
        }

        $user = $peserta->user;

        // Verify password against User hashed password OR raw_password stored on candidate
        $passwordMatches = Hash::check($passwordInput, $user->password) || ($peserta->raw_password === $passwordInput);

        if (! $passwordMatches) {
            return back()->withInput($request->only('username'))->withErrors([
                'auth_failed' => 'Kata Sandi / Token ujian salah. Silakan coba kembali atau hubungi Panitia SPMB jika Anda lupa kata sandi.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();
        session([
            'is_spmb_casis' => true,
            'spmb_peserta_id' => $peserta->id,
        ]);

        return redirect()->route('seleksi.dashboard');
    }

    /**
     * Logout candidate from CBT Portal.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seleksi.login')->with('success', 'Anda telah berhasil keluar dari Sistem CBT Seleksi SPMB.');
    }

    /**
     * Display Candidate Dashboard with sequential test progress.
     */
    public function dashboard()
    {
        $peserta = $this->getPeserta();
        if (! $peserta) {
            Auth::logout();
            return redirect()->route('seleksi.login')->withErrors([
                'auth_failed' => 'Akun Anda tidak terdaftar sebagai peserta seleksi SPMB.',
            ]);
        }

        $bwSesi = $peserta->tesButaWarnaSesi;
        $tpaSesi = $peserta->tpaSesi;

        $bwSelesai = $bwSesi && $bwSesi->status === 'Selesai';

        $mtkSelesai = $tpaSesi && $tpaSesi->status_mtk === 'Selesai';
        $bingSelesai = $tpaSesi && $tpaSesi->status_bing === 'Selesai';
        $bindoSelesai = $tpaSesi && $tpaSesi->status_bindo === 'Selesai';
        $tikSelesai = $tpaSesi && $tpaSesi->status_tik === 'Selesai';

        $allTpaSelesai = $mtkSelesai && $bingSelesai && $bindoSelesai && $tikSelesai;
        $allFinished = $bwSelesai && $allTpaSelesai;

        // Count questions per test
        $totalSoalBw = SpmbTesButaWarnaSoal::where('is_active', true)->count();
        $kategoris = SpmbTpaKategori::withCount('activeSoals')->get()->keyBy('kode');

        return view('pages.spmb.seleksi.dashboard', compact(
            'peserta',
            'bwSesi',
            'tpaSesi',
            'bwSelesai',
            'mtkSelesai',
            'bingSelesai',
            'bindoSelesai',
            'tikSelesai',
            'allTpaSelesai',
            'allFinished',
            'totalSoalBw',
            'kategoris'
        ));
    }

    /**
     * Display Colorblind Test sheet (10 Ishihara plates).
     */
    public function butaWarnaView()
    {
        $peserta = $this->getPeserta();
        if (! $peserta) {
            return redirect()->route('seleksi.login');
        }

        $bwSesi = $peserta->tesButaWarnaSesi;
        if ($bwSesi && $bwSesi->status === 'Selesai') {
            return redirect()->route('seleksi.dashboard')->with('info', 'Anda sudah menyelesaikan Tes Buta Warna.');
        }

        $soals = SpmbTesButaWarnaSoal::where('is_active', true)->orderBy('nomor_urut')->get();

        if ($soals->isEmpty()) {
            return redirect()->route('seleksi.dashboard')->with('error', 'Bank soal Tes Buta Warna belum tersedia. Silakan hubungi panitia SPMB.');
        }

        // Initialize or resume session
        if (! $bwSesi) {
            $bwSesi = SpmbTesButaWarnaSesi::create([
                'spmb_peserta_seleksi_id' => $peserta->id,
                'spmb_piket_tanggal_id' => $peserta->spmb_piket_tanggal_id,
                'tahun_ajaran' => $peserta->tahun_ajaran ?? '2027/2028',
                'status' => 'Sedang Mengerjakan',
                'waktu_mulai' => now(),
                'total_soal' => $soals->count(),
            ]);
        } elseif ($bwSesi->status !== 'Sedang Mengerjakan') {
            $bwSesi->update([
                'status' => 'Sedang Mengerjakan',
                'waktu_mulai' => $bwSesi->waktu_mulai ?? now(),
            ]);
        }

        // Existing answers for this session
        $existingAnswers = SpmbTesButaWarnaJawaban::where('spmb_tes_buta_warna_sesi_id', $bwSesi->id)
            ->pluck('jawaban_peserta', 'spmb_tes_buta_warna_soal_id')
            ->toArray();

        return view('pages.spmb.seleksi.buta-warna', compact('peserta', 'bwSesi', 'soals', 'existingAnswers'));
    }

    /**
     * Submit and grade Colorblind Test.
     */
    public function butaWarnaSubmit(Request $request)
    {
        $peserta = $this->getPeserta();
        if (! $peserta) {
            return redirect()->route('seleksi.login');
        }

        $bwSesi = $peserta->tesButaWarnaSesi;
        if (! $bwSesi) {
            return redirect()->route('seleksi.dashboard');
        }

        $soals = SpmbTesButaWarnaSoal::where('is_active', true)->get();
        $jawabans = $request->input('jawaban', []);

        $totalSoal = $soals->count();
        $benarCount = 0;
        $salahCount = 0;

        foreach ($soals as $soal) {
            $jawabanUser = isset($jawabans[$soal->id]) ? trim($jawabans[$soal->id]) : null;
            $kunci = trim($soal->kunci_jawaban);

            $isCorrect = ($jawabanUser !== null && strcasecmp($jawabanUser, $kunci) === 0);
            $skor = $isCorrect ? (float) ($soal->bobot ?? 10) : 0;

            if ($isCorrect) {
                $benarCount++;
            } else {
                $salahCount++;
            }

            SpmbTesButaWarnaJawaban::updateOrCreate(
                [
                    'spmb_tes_buta_warna_sesi_id' => $bwSesi->id,
                    'spmb_tes_buta_warna_soal_id' => $soal->id,
                ],
                [
                    'jawaban_peserta' => $jawabanUser,
                    'is_correct' => $isCorrect,
                    'skor_diperoleh' => $skor,
                ]
            );
        }

        $finalScore = $totalSoal > 0 ? round(($benarCount / $totalSoal) * 100, 2) : 0;
        $evaluasi = $finalScore >= 80 ? 'Normal' : 'Buta Warna Parsial / Total';

        $bwSesi->update([
            'status' => 'Selesai',
            'waktu_selesai' => now(),
            'total_soal' => $totalSoal,
            'jawaban_benar' => $benarCount,
            'jawaban_salah' => $salahCount,
            'skor' => $finalScore,
            'hasil_evaluasi' => $evaluasi,
        ]);

        return redirect()->route('seleksi.dashboard')->with('success', 'Tes Buta Warna berhasil diselesaikan! Silakan lanjutkan ke tahap berikutnya: Tes Potensi Akademik (Matematika).');
    }

    /**
     * Display TPA Exam Sheet for a specific subject (mtk, bing, bindo, tik).
     */
    public function tpaView(string $mapel)
    {
        $peserta = $this->getPeserta();
        if (! $peserta) {
            return redirect()->route('seleksi.login');
        }

        $mapel = strtolower($mapel);
        if (! isset($this->mapelSequence[$mapel])) {
            return redirect()->route('seleksi.dashboard')->with('error', 'Mata uji tidak valid.');
        }

        $config = $this->mapelSequence[$mapel];
        $kode = $config['kode'];

        // 1. RULE: Must complete Colorblind test first!
        $bwSesi = $peserta->tesButaWarnaSesi;
        if (! $bwSesi || $bwSesi->status !== 'Selesai') {
            return redirect()->route('seleksi.dashboard')->with('error', 'Anda harus menyelesaikan Tes Buta Warna terlebih dahulu sebelum mengerjakan Tes Potensi Akademik.');
        }

        // 2. RULE: Sequential Subject Order!
        $tpaSesi = $peserta->tpaSesi;
        if ($config['prev']) {
            $prevStatusField = 'status_' . $config['prev'];
            if (! $tpaSesi || $tpaSesi->$prevStatusField !== 'Selesai') {
                $prevConfig = $this->mapelSequence[$config['prev']];
                return redirect()->route('seleksi.dashboard')->with('error', "Anda harus menyelesaikan mata pelajaran {$prevConfig['nama']} terlebih dahulu.");
            }
        }

        // 3. RULE: Already finished check
        $currentStatusField = 'status_' . $mapel;
        if ($tpaSesi && $tpaSesi->$currentStatusField === 'Selesai') {
            return redirect()->route('seleksi.dashboard')->with('info', "Anda sudah menyelesaikan ujian {$config['nama']}.");
        }

        // Get Category & Questions
        $kategori = SpmbTpaKategori::where('kode', $kode)->first();
        if (! $kategori) {
            return redirect()->route('seleksi.dashboard')->with('error', "Kategori mata uji {$config['nama']} belum dikonfigurasi.");
        }

        $soals = SpmbTpaSoal::where('spmb_tpa_kategori_id', $kategori->id)
            ->where('is_active', true)
            ->orderBy('nomor_urut')
            ->get();

        if ($soals->isEmpty()) {
            return redirect()->route('seleksi.dashboard')->with('error', "Bank soal {$kategori->nama} belum tersedia. Silakan hubungi panitia SPMB.");
        }

        // Initialize or update TpaSesi
        if (! $tpaSesi) {
            $tpaSesi = SpmbTpaSesi::create([
                'spmb_peserta_seleksi_id' => $peserta->id,
                'spmb_piket_tanggal_id' => $peserta->spmb_piket_tanggal_id,
                'tahun_ajaran' => $peserta->tahun_ajaran ?? '2027/2028',
                'status' => 'Sedang Mengerjakan',
                'waktu_mulai' => now(),
                $currentStatusField => 'Sedang Mengerjakan',
                'waktu_mulai_' . $mapel => now(),
            ]);
        } else {
            $updates = [];
            if ($tpaSesi->status === 'Belum') {
                $updates['status'] = 'Sedang Mengerjakan';
                $updates['waktu_mulai'] = now();
            }
            if ($tpaSesi->$currentStatusField !== 'Sedang Mengerjakan') {
                $updates[$currentStatusField] = 'Sedang Mengerjakan';
                $updates['waktu_mulai_' . $mapel] = $tpaSesi->{'waktu_mulai_' . $mapel} ?? now();
            }
            if (! empty($updates)) {
                $tpaSesi->update($updates);
            }
        }

        // Calculate timer (remaining seconds)
        $waktuMulai = $tpaSesi->{'waktu_mulai_' . $mapel} ?? now();
        $durasiMenit = $kategori->durasi_menit > 0 ? $kategori->durasi_menit : 20;
        $waktuBatas = Carbon::parse($waktuMulai)->addMinutes($durasiMenit);
        $sisaDetik = max(0, now()->diffInSeconds($waktuBatas, false));

        // Existing answers
        $existingAnswers = SpmbTpaJawaban::where('spmb_tpa_sesi_id', $tpaSesi->id)
            ->whereIn('spmb_tpa_soal_id', $soals->pluck('id'))
            ->pluck('jawaban_peserta', 'spmb_tpa_soal_id')
            ->toArray();

        return view('pages.spmb.seleksi.tpa-exam', compact(
            'peserta',
            'tpaSesi',
            'kategori',
            'mapel',
            'config',
            'soals',
            'existingAnswers',
            'sisaDetik',
            'durasiMenit'
        ));
    }

    /**
     * Submit and grade a TPA Subject Exam.
     */
    public function tpaSubmit(Request $request, string $mapel)
    {
        $peserta = $this->getPeserta();
        if (! $peserta) {
            return redirect()->route('seleksi.login');
        }

        $mapel = strtolower($mapel);
        if (! isset($this->mapelSequence[$mapel])) {
            return redirect()->route('seleksi.dashboard');
        }

        $config = $this->mapelSequence[$mapel];
        $tpaSesi = $peserta->tpaSesi;

        if (! $tpaSesi) {
            return redirect()->route('seleksi.dashboard');
        }

        $kategori = SpmbTpaKategori::where('kode', $config['kode'])->first();
        $soals = SpmbTpaSoal::where('spmb_tpa_kategori_id', $kategori->id)->where('is_active', true)->get();

        $jawabans = $request->input('jawaban', []);
        $totalSoal = $soals->count();
        $benarCount = 0;

        foreach ($soals as $soal) {
            $jawabanUser = isset($jawabans[$soal->id]) ? strtoupper(trim($jawabans[$soal->id])) : null;
            $kunci = strtoupper(trim($soal->kunci_jawaban));

            $isCorrect = ($jawabanUser !== null && $jawabanUser === $kunci);
            $skor = $isCorrect ? (float) ($soal->bobot ?? 1) : 0;

            if ($isCorrect) {
                $benarCount++;
            }

            SpmbTpaJawaban::updateOrCreate(
                [
                    'spmb_tpa_sesi_id' => $tpaSesi->id,
                    'spmb_tpa_soal_id' => $soal->id,
                ],
                [
                    'jawaban_peserta' => $jawabanUser,
                    'is_correct' => $isCorrect,
                    'skor_diperoleh' => $skor,
                ]
            );
        }

        // Calculate score for this subject out of 100
        $mapelScore = $totalSoal > 0 ? round(($benarCount / $totalSoal) * 100, 2) : 0;

        $updateData = [
            'status_' . $mapel => 'Selesai',
            'waktu_selesai_' . $mapel => now(),
            'skor_' . $mapel => $mapelScore,
        ];

        // If this is the last subject (TIK), finalize overall TPA session
        if ($mapel === 'tik') {
            $updateData['status'] = 'Selesai';
            $updateData['waktu_selesai'] = now();

            // Calculate total correct across all answers in this session
            $allAnswers = SpmbTpaJawaban::where('spmb_tpa_sesi_id', $tpaSesi->id)->get();
            $totalBenarAll = $allAnswers->where('is_correct', true)->count();
            $totalSemuaSoal = SpmbTpaSoal::where('is_active', true)->count();

            // Total score: average of 4 subject scores
            $skorMtk = (float) ($tpaSesi->skor_mtk ?? 0);
            $skorBing = (float) ($tpaSesi->skor_bing ?? 0);
            $skorBindo = (float) ($tpaSesi->skor_bindo ?? 0);
            $skorTik = $mapelScore;

            $skorTotal = round(($skorMtk + $skorBing + $skorBindo + $skorTik) / 4, 2);

            $updateData['total_soal'] = $totalSemuaSoal;
            $updateData['total_benar'] = $totalBenarAll;
            $updateData['total_salah'] = $allAnswers->where('is_correct', false)->whereNotNull('jawaban_peserta')->count();
            $updateData['total_kosong'] = $totalSemuaSoal - $allAnswers->whereNotNull('jawaban_peserta')->count();
            $updateData['skor_total'] = $skorTotal;
            $updateData['hasil_evaluasi'] = $skorTotal >= 65 ? 'Lulus' : 'Dipertimbangkan';
        }

        $tpaSesi->update($updateData);

        // If last subject finished, redirect to Selesai page!
        if ($mapel === 'tik') {
            return redirect()->route('seleksi.selesai');
        }

        // Otherwise redirect to dashboard to proceed to next subject
        $nextConfig = $this->mapelSequence[$config['next']];
        return redirect()->route('seleksi.dashboard')->with('success', "Ujian {$config['nama']} berhasil diselesaikan! Silakan lanjutkan ke mata pelajaran berikutnya: {$nextConfig['nama']}.");
    }

    /**
     * Display completion & graduation announcement notice page.
     */
    public function selesaiView()
    {
        $peserta = $this->getPeserta();
        if (! $peserta) {
            return redirect()->route('seleksi.login');
        }

        return view('pages.spmb.seleksi.selesai', compact('peserta'));
    }
}
