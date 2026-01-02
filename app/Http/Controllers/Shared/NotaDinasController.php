<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\NdeRefJenis;
use App\Models\NotaDinas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Spatie\Permission\Models\Role;

class NotaDinasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // NDE Masuk (Recipient)
        $ndeMasuk = $user->notaDinasMasuk()
            ->with(['pengirim', 'jenis'])
            ->latest()
            ->paginate(10, ['*'], 'masuk');

        // NDE Keluar (Sender)
        $ndeKeluar = $user->notaDinasKeluar()
            ->with('jenis')
            ->latest()
            ->paginate(10, ['*'], 'keluar');

        return view('pages.shared.nde.index', compact('ndeMasuk', 'ndeKeluar'));
    }

    public function create()
    {
        $jenisNde = NdeRefJenis::all();
        $users = User::where('id', '!=', Auth::id())->with('roles')->get();
        $roles = Role::all();
        return view('pages.shared.nde.create', compact('jenisNde', 'users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_id' => 'required|exists:nde_ref_jenis,id',
            'perihal' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal' => 'required|date',
            'penerima_ids' => 'required|array',
            'penerima_ids.*' => 'exists:users,id',
            'lampiran' => 'nullable|file|mimes:jpg,png,pdf,docx,xlsx|max:5120',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('public/nota_dinas');
        }

        // Generate nomor nota (simple prefix for now)
        $nomorNota = 'ND/' . date('Ymd') . '/' . strtoupper(Str::random(5));

        $notaDinas = NotaDinas::create([
            'user_id' => Auth::id(),
            'nomor_nota' => $nomorNota,
            'jenis_id' => $request->jenis_id,
            'perihal' => $request->perihal,
            'isi' => $request->isi,
            'tanggal' => $request->tanggal,
            'lampiran' => $lampiranPath,
            'status' => 'dikirim',
        ]);

        $notaDinas->penerimas()->attach($request->penerima_ids);

        return redirect()->route('shared.nde.index')->with('success', 'Nota Dinas berhasil dikirim.');
    }

    public function show($id)
    {
        $notaDinas = NotaDinas::with(['pengirim.roles', 'jenis', 'penerimas.roles'])->findOrFail($id);
        $user = Auth::user();

        // Mark as read if user is the recipient
        $penerima = $notaDinas->penerimas()->where('users.id', $user->id)->first();
        if ($penerima && !$penerima->pivot->is_read) {
            $notaDinas->penerimas()->updateExistingPivot($user->id, [
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return view('pages.shared.nde.show', compact('notaDinas'));
    }

    public function download($id)
    {
        $notaDinas = NotaDinas::findOrFail($id);
        
        if (!$notaDinas->lampiran) {
            return redirect()->back()->with('error', 'Lampiran tidak ditemukan.');
        }

        return Storage::download($notaDinas->lampiran);
    }
}
