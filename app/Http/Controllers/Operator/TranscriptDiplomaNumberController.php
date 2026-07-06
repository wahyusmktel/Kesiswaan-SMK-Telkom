<?php

namespace App\Http\Controllers\Operator;

use App\Exports\TranscriptDiplomaNumberTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\TranscriptDiplomaNumberImport;
use App\Models\MasterSiswa;
use App\Models\TranscriptDiplomaNumber;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TranscriptDiplomaNumberController extends Controller
{
    public function index(Request $request)
    {
        $students = $this->finalStudents()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($searchQuery) use ($request) {
                    $searchQuery->where('nama_lengkap', 'like', '%' . $request->search . '%')
                        ->orWhere('nis', 'like', '%' . $request->search . '%');
                });
            })
            ->paginate(20)
            ->withQueryString();

        return view('pages.operator.transcript.diploma-numbers', compact('students'));
    }

    public function update(Request $request, MasterSiswa $student)
    {
        $data = $request->validate(['diploma_number' => 'nullable|string|max:255']);
        TranscriptDiplomaNumber::updateOrCreate(['master_siswa_id' => $student->id], $data);
        toast('Nomor ijazah berhasil diperbarui.', 'success');

        return back();
    }

    public function destroy(MasterSiswa $student)
    {
        TranscriptDiplomaNumber::where('master_siswa_id', $student->id)->delete();
        toast('Nomor ijazah berhasil dihapus.', 'success');

        return back();
    }

    public function template()
    {
        $students = $this->finalStudents()->get();

        return Excel::download(new TranscriptDiplomaNumberTemplateExport($students), 'template_nomor_ijazah_transkrip.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file_import' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        $import = new TranscriptDiplomaNumberImport();
        Excel::import($import, $request->file('file_import'));

        toast('Import nomor ijazah selesai.', 'success');

        return back()->with('import_report', $import->summary());
    }

    private function finalStudents()
    {
        return MasterSiswa::with(['rombels.kelas', 'transcriptDiplomaNumber'])
            ->whereHas('rombels.kelas', function ($query) {
                $query->where('nama_kelas', 'like', '%XII%')
                    ->orWhere('nama_kelas', 'like', '%12%')
                    ->orWhere('nama_kelas', 'like', '%XII %');
            })
            ->orderBy('nama_lengkap');
    }
}
