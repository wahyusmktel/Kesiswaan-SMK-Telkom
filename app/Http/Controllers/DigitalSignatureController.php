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
        $user      = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();
        $documents = DigitalDocument::where('signed_by', $user->id)
            ->orderByDesc('signed_at')
            ->paginate(10);

        return view('pages.tanda-tangan.index', compact('signature', 'documents'));
    }

    public function setup(Request $request)
    {
        $request->validate([
            'ttd_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'pin'         => 'nullable|digits_between:4,8|confirmed',
            'pin_confirmation' => 'nullable',
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

        $signature->is_active = true;
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
}
