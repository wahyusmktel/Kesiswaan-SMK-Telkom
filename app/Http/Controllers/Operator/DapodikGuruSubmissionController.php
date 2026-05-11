<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\DapodikGuruSubmission;
use App\Models\DapodikGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DapodikGuruSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $submissions = DapodikGuruSubmission::with(['masterGuru.user', 'operator'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pendingCount = DapodikGuruSubmission::where('status', 'pending')->count();

        return view('pages.operator.dapodik-guru.submissions.index', compact('submissions', 'status', 'pendingCount'));
    }

    public function show(DapodikGuruSubmission $submission)
    {
        $submission->load(['masterGuru.dapodikGuru', 'masterGuru.user', 'operator']);
        return view('pages.operator.dapodik-guru.submissions.show', compact('submission'));
    }

    public function approve(Request $request, DapodikGuruSubmission $submission)
    {
        if (!$submission->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $guru    = $submission->masterGuru;
        $dapodik = DapodikGuru::firstOrCreate(
            ['master_guru_id' => $guru->id],
            ['master_guru_id' => $guru->id]
        );

        $dapodik->update($submission->new_data);

        $submission->update([
            'status'       => 'approved',
            'operator_id'  => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('operator.dapodik-guru.submissions.index')
            ->with('success', 'Pengajuan disetujui dan data Dapodik guru telah diperbarui.');
    }

    public function reject(Request $request, DapodikGuruSubmission $submission)
    {
        if (!$submission->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $submission->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'operator_id'      => Auth::id(),
            'processed_at'     => now(),
        ]);

        return back()->with('success', 'Pengajuan ditolak. Alasan telah tercatat untuk guru yang bersangkutan.');
    }
}
