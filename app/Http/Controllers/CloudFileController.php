<?php

namespace App\Http\Controllers;

use App\Models\CloudFile;
use App\Services\GoogleDriveService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CloudFileController extends Controller
{
    public function index(Request $request, GoogleDriveService $drive)
    {
        $query = CloudFile::where('user_id', Auth::id())->latest();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $files = $query->paginate(24);
        $driveConnection = $drive->connectionFor(Auth::user());
        $driveFiles = [];
        $driveError = null;

        if ($driveConnection) {
            try {
                $driveFiles = $drive->listFiles(Auth::user(), $request->search);
            } catch (Exception $e) {
                $driveError = $e->getMessage();
            }
        }

        return view('pages.shared.cloud-files.index', compact('files', 'driveConnection', 'driveFiles', 'driveError'));
    }

    public function store(Request $request, GoogleDriveService $drive)
    {
        $request->validate([
            'files.*' => 'required|file|max:51200', // 50MB max per file
            'storage_target' => 'nullable|in:local,google_drive',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($request->input('storage_target') === 'google_drive') {
                    try {
                        $drive->upload(Auth::user(), $file);
                    } catch (Exception $e) {
                        return back()->withErrors(['files' => 'Gagal upload ke Google Drive: ' . $e->getMessage()]);
                    }

                    continue;
                }

                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $mimeType = $file->getMimeType();
                $size = $file->getSize();

                // Safe storage in 'local' disk instead of 'public'
                $path = $file->store('cloud-files/' . Auth::id(), 'local');

                CloudFile::create([
                    'user_id' => Auth::id(),
                    'name' => $originalName,
                    'file_name' => basename($path),
                    'file_path' => $path,
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'size' => $size,
                ]);
            }
        }

        toast($request->input('storage_target') === 'google_drive' ? 'File berhasil diunggah ke Google Drive.' : 'File berhasil diunggah.', 'success');
        return redirect()->back();
    }

    public function download(CloudFile $cloudFile)
    {
        if ($cloudFile->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this file.');
        }

        if (!Storage::disk('local')->exists($cloudFile->file_path)) {
            abort(404, 'File not found on the server.');
        }

        return Storage::disk('local')->download($cloudFile->file_path, $cloudFile->name);
    }

    public function destroy(CloudFile $cloudFile)
    {
        if ($cloudFile->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (Storage::disk('local')->exists($cloudFile->file_path)) {
            Storage::disk('local')->delete($cloudFile->file_path);
        }

        $cloudFile->delete();

        toast('File berhasil dihapus.', 'success');
        return redirect()->back();
    }
}
