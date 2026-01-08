<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\DatabaseActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use RealRashid\SweetAlert\Facades\Alert;

class DatabaseController extends Controller
{
    public function index()
    {
        $activities = DatabaseActivity::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tableList = array_map(function ($table) use ($dbName) {
            $key = "Tables_in_" . $dbName;
            return $table->$key;
        }, $tables);

        $backups = [];
        if (Storage::exists('backups')) {
            $files = Storage::files('backups');
            foreach ($files as $file) {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => Storage::size($file),
                    'last_modified' => Storage::lastModified($file),
                ];
            }
        }

        // Sort backups by last modified desc
        usort($backups, function ($a, $b) {
            return $b['last_modified'] <=> $a['last_modified'];
        });

        return view('pages.kesiswaan.database.index', compact('activities', 'tableList', 'backups'));
    }

    public function backup()
    {
        $dbConfig = config('database.connections.mysql');
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($path)
        );

        $process = Process::fromShellCommandline($command);
        
        try {
            $process->mustRun();
            
            $tables = DB::select('SHOW TABLES');
            $dbName = $dbConfig['database'];
            $tableList = array_map(function ($table) use ($dbName) {
                $key = "Tables_in_" . $dbName;
                return $table->$key;
            }, $tables);

            DatabaseActivity::create([
                'user_id' => Auth::id(),
                'type' => 'backup',
                'filename' => $filename,
                'file_size' => filesize($path),
                'tables_count' => count($tableList),
                'details' => ['tables' => $tableList],
                'status' => 'success',
            ]);

            Alert::success('Berhasil', 'Database berhasil di-backup.');
            return redirect()->back();
        } catch (ProcessFailedException $exception) {
            DatabaseActivity::create([
                'user_id' => Auth::id(),
                'type' => 'backup',
                'filename' => $filename,
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            Alert::error('Gagal', 'Gagal membackup database: ' . $exception->getMessage());
            return redirect()->back();
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'filename' => 'required_without:backup_file',
            'backup_file' => 'nullable|file|mimes:sql',
        ]);

        $dbConfig = config('database.connections.mysql');
        $filename = '';
        $path = '';

        if ($request->hasFile('backup_file')) {
            $file = $request->file('backup_file');
            $filename = 'upload-' . date('Y-m-d-H-i-s') . '-' . $file->getClientOriginalName();
            $path = $file->storeAs('backups', $filename);
            $path = storage_path('app/' . $path);
        } else {
            $filename = $request->filename;
            $path = storage_path('app/backups/' . $filename);
        }

        if (!file_exists($path)) {
            Alert::error('Gagal', 'File backup tidak ditemukan.');
            return redirect()->back();
        }

        // Automatic backup before restore
        $this->autoBackupBeforeRestore();

        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s %s < %s',
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($path)
        );

        $process = Process::fromShellCommandline($command);

        try {
            $process->mustRun();

            $tables = DB::select('SHOW TABLES');
            $dbName = $dbConfig['database'];
            $tableList = array_map(function ($table) use ($dbName) {
                $key = "Tables_in_" . $dbName;
                return $table->$key;
            }, $tables);

            DatabaseActivity::create([
                'user_id' => Auth::id(),
                'type' => 'restore',
                'filename' => $filename,
                'file_size' => filesize($path),
                'tables_count' => count($tableList),
                'details' => ['tables' => $tableList],
                'status' => 'success',
            ]);

            Alert::success('Berhasil', 'Database berhasil di-restore.');
            return redirect()->back();
        } catch (ProcessFailedException $exception) {
            DatabaseActivity::create([
                'user_id' => Auth::id(),
                'type' => 'restore',
                'filename' => $filename,
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            Alert::error('Gagal', 'Gagal me-restore database: ' . $exception->getMessage());
            return redirect()->back();
        }
    }

    private function autoBackupBeforeRestore()
    {
        $dbConfig = config('database.connections.mysql');
        $filename = 'auto-backup-before-restore-' . date('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($path)
        );

        $process = Process::fromShellCommandline($command);
        $process->run();
    }

    public function download($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::exists($path)) {
            return Storage::download($path);
        }

        Alert::error('Gagal', 'File tidak ditemukan.');
        return redirect()->back();
    }

    public function destroy($filename)
    {
        $path = 'backups/' . $filename;
        if (Storage::exists($path)) {
            Storage::delete($path);
            Alert::success('Berhasil', 'File backup berhasil dihapus.');
        } else {
            Alert::error('Gagal', 'File tidak ditemukan.');
        }

        return redirect()->back();
    }
}
