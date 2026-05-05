<?php

namespace App\Http\Controllers;

use App\Models\DigitalDocument;
use App\Models\UserDigitalSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DigitalSignatureController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $signature  = UserDigitalSignature::where('user_id', $user->id)->first();
        $documents  = DigitalDocument::where('signed_by', $user->id)
            ->orderByDesc('signed_at')
            ->paginate(10);
        $validCount = DigitalDocument::where('signed_by', $user->id)->where('is_valid', true)->count();

        return view('pages.tanda-tangan.index', compact('signature', 'documents', 'validCount'));
    }

    public function setup(Request $request)
    {
        $request->validate([
            'ttd_image'             => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pin'                   => 'nullable|digits_between:4,8|confirmed',
            'pin_confirmation'      => 'nullable',
            'auto_sign_izin_keluar' => 'nullable|boolean',
            'auto_sign_perizinan'   => 'nullable|boolean',
        ]);

        $signature = UserDigitalSignature::firstOrNew(['user_id' => Auth::id()]);

        if ($request->hasFile('ttd_image')) {
            if ($signature->ttd_image_path) {
                Storage::disk('public')->delete($signature->ttd_image_path);
            }
            $signature->ttd_image_path = $request->file('ttd_image')->store('tanda-tangan', 'public');
        }

        if ($request->filled('pin')) {
            $signature->pin_hash = Hash::make($request->pin);
        }

        $signature->is_active             = true;
        $signature->auto_sign_izin_keluar = $request->boolean('auto_sign_izin_keluar');
        $signature->auto_sign_perizinan   = $request->boolean('auto_sign_perizinan');
        $signature->save();

        return back()->with('success', 'Tanda tangan digital berhasil diperbarui.');
    }

    public function sign(Request $request)
    {
        $request->validate([
            'pin'           => 'required|string',
            'document_type' => 'required|string',
            'document_title'=> 'required|string',
            'reference_id'  => 'nullable|integer',
            'hash_content'  => 'required|string',
            'signer_nip'    => 'nullable|string',
        ]);

        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (!$signature || !$signature->isReady()) {
            return response()->json(['success' => false, 'message' => 'Tanda tangan digital belum disetup. Silakan setup terlebih dahulu.'], 422);
        }

        if (!$signature->verifyPin($request->pin)) {
            return response()->json(['success' => false, 'message' => 'PIN salah. Coba lagi.'], 422);
        }

        $hash = DigitalDocument::generateHash(explode('|', $request->hash_content));
        $hmac = DigitalDocument::generateHmac($hash);

        $doc = DigitalDocument::where('document_type', $request->document_type)
            ->where('reference_id', $request->reference_id)
            ->first();

        if ($doc) {
            $doc->update([
                'document_hash'  => $hash,
                'hmac_signature' => $hmac,
                'signed_by'      => $user->id,
                'signer_name'    => $user->name,
                'signer_nip'     => $request->signer_nip,
                'signer_role'    => $user->getRoleNames()->first() ?? 'Staff',
                'signed_at'      => now(),
                'is_valid'       => true,
                'revoked_at'     => null,
                'revoke_reason'  => null,
            ]);
        } else {
            $doc = DigitalDocument::create([
                'document_type'  => $request->document_type,
                'document_title' => $request->document_title,
                'reference_id'   => $request->reference_id,
                'document_hash'  => $hash,
                'hmac_signature' => $hmac,
                'signed_by'      => $user->id,
                'signer_name'    => $user->name,
                'signer_nip'     => $request->signer_nip,
                'signer_role'    => $user->getRoleNames()->first() ?? 'Staff',
                'signed_at'      => now(),
            ]);
        }

        return response()->json([
            'success'      => true,
            'token'        => $doc->token,
            'verification_url' => route('verifikasi.dokumen', $doc->token),
        ]);
    }

    public function signBulk(Request $request)
    {
        $request->validate([
            'pin'                     => 'required|string',
            'pengumuman_kelulusan_id' => 'required|exists:pengumuman_kelulusans,id',
        ]);

        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (!$signature || !$signature->isReady()) {
            return response()->json(['success' => false, 'message' => 'Tanda tangan digital belum disetup. Silakan setup terlebih dahulu di menu Tanda Tangan Digital.'], 422);
        }

        if (!$signature->verifyPin($request->pin)) {
            return response()->json(['success' => false, 'message' => 'PIN salah. Coba lagi.'], 422);
        }

        $kelulusans = \App\Models\SiswaKelulusan::where('pengumuman_kelulusan_id', $request->pengumuman_kelulusan_id)
            ->where('status', 'lulus')
            ->with('siswa')
            ->get();

        $newlySigned  = 0;
        $alreadySigned = 0;
        $signerRole   = $user->getRoleNames()->first() ?? 'Staff';

        foreach ($kelulusans as $kelulusan) {
            $existing = DigitalDocument::where('document_type', 'SKL')
                ->where('reference_id', $kelulusan->id)
                ->where('is_valid', true)
                ->first();

            if ($existing) {
                $alreadySigned++;
                continue;
            }

            $nama        = $kelulusan->siswa->nama_lengkap ?? 'Siswa';
            $hash        = DigitalDocument::generateHash(['SKL', (string) $kelulusan->id, (string) $kelulusan->master_siswa_id, $nama]);
            $hmac        = DigitalDocument::generateHmac($hash);
            $signerData  = [
                'document_hash'  => $hash,
                'hmac_signature' => $hmac,
                'signed_by'      => $user->id,
                'signer_name'    => $user->name,
                'signer_nip'     => null,
                'signer_role'    => $signerRole,
                'signed_at'      => now(),
                'is_valid'       => true,
                'revoked_at'     => null,
                'revoke_reason'  => null,
            ];

            $doc = DigitalDocument::where('document_type', 'SKL')
                ->where('reference_id', $kelulusan->id)
                ->first();

            if ($doc) {
                $doc->update($signerData);
            } else {
                DigitalDocument::create(array_merge($signerData, [
                    'document_type'  => 'SKL',
                    'document_title' => 'SKL - ' . $nama,
                    'reference_id'   => $kelulusan->id,
                ]));
            }

            $newlySigned++;
        }

        return response()->json([
            'success'        => true,
            'newly_signed'   => $newlySigned,
            'already_signed' => $alreadySigned,
            'total'          => $kelulusans->count(),
        ]);
    }

    public function revoke(Request $request)
    {
        $request->validate([
            'token'         => 'required|string|exists:digital_documents,token',
            'pin'           => 'required|string',
            'revoke_reason' => 'nullable|string|max:255',
        ]);

        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (!$signature || !$signature->verifyPin($request->pin)) {
            return back()->with('error', 'PIN salah. Pencabutan gagal.');
        }

        $doc = DigitalDocument::where('token', $request->token)
            ->where('signed_by', $user->id)
            ->firstOrFail();

        $doc->update([
            'is_valid'      => false,
            'revoked_at'    => now(),
            'revoke_reason' => $request->revoke_reason ?? 'Dicabut oleh penandatangan.',
        ]);

        return back()->with('success', 'Tanda tangan berhasil dicabut.');
    }

    public function revokeSelected(Request $request)
    {
        $request->validate([
            'pin'           => 'required|string',
            'tokens'        => 'required|array|min:1',
            'tokens.*'      => 'required|string|exists:digital_documents,token',
            'revoke_reason' => 'nullable|string|max:255',
        ]);

        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (!$signature || !$signature->verifyPin($request->pin)) {
            return back()->with('error', 'PIN salah. Pencabutan gagal.');
        }

        $count = DigitalDocument::whereIn('token', $request->tokens)
            ->where('signed_by', $user->id)
            ->where('is_valid', true)
            ->update([
                'is_valid'      => false,
                'revoked_at'    => now(),
                'revoke_reason' => $request->revoke_reason ?: 'Dicabut massal oleh penandatangan.',
            ]);

        return back()->with('success', "{$count} tanda tangan berhasil dicabut.");
    }

    public function revokeAll(Request $request)
    {
        $request->validate([
            'pin'           => 'required|string',
            'revoke_reason' => 'nullable|string|max:255',
        ]);

        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (!$signature || !$signature->verifyPin($request->pin)) {
            return back()->with('error', 'PIN salah. Pencabutan gagal.');
        }

        $count = DigitalDocument::where('signed_by', $user->id)
            ->where('is_valid', true)
            ->update([
                'is_valid'      => false,
                'revoked_at'    => now(),
                'revoke_reason' => $request->revoke_reason ?: 'Dicabut semua oleh penandatangan.',
            ]);

        return back()->with('success', "Semua {$count} tanda tangan berhasil dicabut.");
    }
}
