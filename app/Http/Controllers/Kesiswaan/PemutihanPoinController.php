<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\SiswaPemutihan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PemutihanPoinController extends Controller
{
    public function index(Request $request)
    {
        $query = SiswaPemutihan::with(['siswa', 'pengaju', 'penyetuju'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%$search%")->orWhere('nis', 'like', "%$search%");
            });
        }

        $pemutihans = $query->paginate(10);
        
        return view('kesiswaan.poin.pemutihan', compact('pemutihans'));
    }

    public function printPdf(SiswaPemutihan $pemutihan)
    {
        $user = auth()->user();
        
        // Security check: Siswa hanya boleh cetak surat miliknya sendiri
        if ($user->hasRole('Siswa')) {
            if (!$user->masterSiswa || $user->masterSiswa->id !== $pemutihan->master_siswa_id) {
                abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
            }
        }

        $pemutihan->load(['siswa', 'pengaju', 'penyetuju']);
        
        $pdf = Pdf::loadView('pdf.berita-acara-pemutihan', compact('pemutihan'));
        
        $filename = 'Berita_Acara_Pemutihan_' . str_replace(' ', '_', $pemutihan->siswa->nama_lengkap) . '_' . $pemutihan->tanggal . '.pdf';
        
        return $pdf->stream($filename);
    }

    public function store(Request $request)
    {
        $request->validate([
            'master_siswa_id' => 'required|exists:master_siswa,id',
            'tanggal' => 'required|date',
            'poin_dikurangi' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $isBK = auth()->user()->hasRole('Guru BK');
        
        $data['status'] = $isBK ? 'diajukan' : 'disetujui';
        $data['diajukan_oleh'] = $isBK ? auth()->id() : null;
        $data['disetujui_oleh'] = $isBK ? null : auth()->id();

        SiswaPemutihan::create($data);

        $message = $isBK ? 'Pengajuan pemutihan poin siswa berhasil diajukan.' : 'Pemutihan poin siswa berhasil dicatat.';
        return redirect()->back()->with('success', $message);
    }

    public function approve(SiswaPemutihan $pemutihan)
    {
        $pemutihan->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan pemutihan poin telah disetujui.');
    }

    public function reject(Request $request, SiswaPemutihan $pemutihan)
    {
        $pemutihan->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan pemutihan poin telah ditolak.');
    }

    public function destroy(SiswaPemutihan $input_pemutihan)
    {
        $input_pemutihan->delete();
        return redirect()->back()->with('success', 'Catatan pemutihan berhasil dihapus.');
    }
}
