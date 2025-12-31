<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\DapodikSubmission;
use App\Models\DapodikSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DapodikSubmissionController extends Controller
{
    public function index()
    {
        $submissions = DapodikSubmission::with('masterSiswa')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->paginate(15);

        return view('pages.operator.dapodik.submissions.index', compact('submissions'));
    }

    public function show(DapodikSubmission $submission)
    {
        $submission->load('masterSiswa.dapodik');
        return view('pages.operator.dapodik.submissions.show', compact('submission'));
    }

    public function approve(Request $request, DapodikSubmission $submission)
    {
        if ($submission->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        // Update DapodikSiswa data
        $dapodik = DapodikSiswa::firstOrCreate(
            ['master_siswa_id' => $submission->master_siswa_id]
        );

        $newData = $submission->new_data;

        // If nama_lengkap is present, update MasterSiswa and User profile
        if (isset($newData['nama_lengkap'])) {
            $siswa = $submission->masterSiswa;
            $siswa->update([
                'nama_lengkap' => $newData['nama_lengkap']
            ]);
            
            // Also update the User table if an account exists
            if ($siswa->user) {
                $siswa->user->update([
                    'name' => $newData['nama_lengkap']
                ]);
            }
            
            // Remove it so it doesn't break DapodikSiswa update (column doesn't exist there)
            unset($newData['nama_lengkap']);
        }

        $dapodik->update($newData);

        // Update Submission status
        $submission->update([
            'status' => 'approved',
            'operator_id' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('operator.dapodik.submissions.index')->with('success', 'Pengajuan berhasil disetujui and data Dapodik siswa telah diperbarui.');
    }

    public function reject(Request $request, DapodikSubmission $submission)
    {
        if ($submission->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $submission->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'operator_id' => Auth::id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('operator.dapodik.submissions.index')->with('success', 'Pengajuan telah ditolak.');
    }
}
