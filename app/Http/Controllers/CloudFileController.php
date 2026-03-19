<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CloudFileController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\CloudFile::where('user_id', \Illuminate\Support\Facades\Auth::id())->latest();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $files = $query->paginate(24);

        return view('pages.shared.cloud-files.index', compact('files'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:51200', // 50MB max per file
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $mimeType = $file->getMimeType();
                $size = $file->getSize();

                // Safe storage in 'local' disk instead of 'public'
                $path = $file->store('cloud-files/' . \Illuminate\Support\Facades\Auth::id(), 'local');

                \App\Models\CloudFile::create([
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'name' => $originalName,
                    'file_name' => basename($path),
                    'file_path' => $path,
                    'mime_type' => $mimeType,
                    'extension' => $extension,
                    'size' => $size,
                ]);
            }
        }

        toast('File berhasil diunggah.', 'success');
        return redirect()->back();
    }

    public function download(\App\Models\CloudFile $cloudFile)
    {
        if ($cloudFile->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'Unauthorized access to this file.');
        }

        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($cloudFile->file_path)) {
            abort(404, 'File not found on the server.');
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($cloudFile->file_path, $cloudFile->name);
    }

    public function destroy(\App\Models\CloudFile $cloudFile)
    {
        if ($cloudFile->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($cloudFile->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($cloudFile->file_path);
        }

        $cloudFile->delete();

        toast('File berhasil dihapus.', 'success');
        return redirect()->back();
    }
}
