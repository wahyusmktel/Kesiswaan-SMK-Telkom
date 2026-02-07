<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\File;

class SystemUpdateController extends Controller
{
    /**
     * Tampilkan halaman pembaharuan sistem.
     */
    public function index()
    {
        $scriptPath = base_path('deploy_sisfo.sh');
        $scriptExists = File::exists($scriptPath);

        return view('pages.super-admin.system-update', compact('scriptExists'));
    }

    /**
     * Jalankan proses deployment.
     */
    public function deploy(Request $request)
    {
        $scriptPath = base_path('deploy_sisfo.sh');

        if (!File::exists($scriptPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Script deploy_sisfo.sh tidak ditemukan di direktori root.',
            ], 404);
        }

        // Set timeout 5 menit (300 detik) karena proses git pull & composer install bisa lama
        $process = new Process(['sh', $scriptPath]);
        $process->setWorkingDirectory(base_path());
        $process->setTimeout(300);

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menjalankan script pembaharuan.',
                    'output' => $process->getErrorOutput() ?: $process->getOutput(),
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Aplikasi berhasil diperbarui!',
                'output' => $process->getOutput(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }
}
