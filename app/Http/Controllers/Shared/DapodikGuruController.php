<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Imports\DapodikGuruImport;
use App\Models\DapodikGuru;
use App\Models\MasterGuru;
use App\Models\User;
use App\Support\EmploymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Throwable;

class DapodikGuruController extends Controller
{
    public function index(Request $request)
    {
        $context = $this->context($request);
        $query = DapodikGuru::with('masterGuru.user')->where('employee_category', $context['category']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                    ->orWhere('nik', 'like', "%{$s}%")
                    ->orWhere('nuptk', 'like', "%{$s}%")
                    ->orWhere('nip', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'linked') {
                $query->whereNotNull('master_guru_id');
            } elseif ($request->status === 'unlinked') {
                $query->whereNull('master_guru_id');
            }
        }

        if ($request->filled('jenis_ptk')) {
            $query->where('jenis_ptk', $request->jenis_ptk);
        }

        $dapodikGurus = $query->orderBy('nama')->paginate(20)->withQueryString();

        $categoryQuery = DapodikGuru::where('employee_category', $context['category']);
        $totalDapodik = (clone $categoryQuery)->count();
        $totalLinked = (clone $categoryQuery)->whereNotNull('master_guru_id')->count();
        $totalAccountsLinked = (clone $categoryQuery)
            ->whereHas('masterGuru', fn ($query) => $query->whereNotNull('user_id'))
            ->count();
        $totalUnlinked = (clone $categoryQuery)->whereNull('master_guru_id')->count();
        $jenisPtkList = (clone $categoryQuery)->select('jenis_ptk')->distinct()->whereNotNull('jenis_ptk')->orderBy('jenis_ptk')->pluck('jenis_ptk');

        $employees = MasterGuru::with('user.roles')
            ->where('employee_category', $context['category'])
            ->orderBy('nama_lengkap')
            ->get();

        $accountOptions = $context['category'] === DapodikGuru::CATEGORY_TPA
            ? User::with(['roles', 'masterGuru'])
                ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'Siswa'))
                ->orderBy('name')
                ->get(['id', 'name', 'email'])
            : collect();

        return view('pages.shared.dapodik-guru.index', compact(
            'dapodikGurus', 'totalDapodik', 'totalLinked', 'totalAccountsLinked', 'totalUnlinked', 'jenisPtkList', 'employees', 'accountOptions', 'context'
        ));
    }

    public function show(Request $request, DapodikGuru $dapodikGuru)
    {
        $context = $this->context($request);
        abort_unless($dapodikGuru->employee_category === $context['category'], 404);
        $dapodikGuru->load('masterGuru.user.roles');

        return view('pages.shared.dapodik-guru.show', compact('dapodikGuru', 'context'));
    }

    public function create(Request $request)
    {
        $dapodikGuru = new DapodikGuru;
        $isCreate = true;
        $context = $this->context($request);

        return view('pages.shared.dapodik-guru.edit', compact('dapodikGuru', 'isCreate', 'context'));
    }

    public function edit(Request $request, DapodikGuru $dapodikGuru)
    {
        $context = $this->context($request);
        abort_unless($dapodikGuru->employee_category === $context['category'], 404);
        $dapodikGuru->load('masterGuru.user');
        $isCreate = false;

        return view('pages.shared.dapodik-guru.edit', compact('dapodikGuru', 'isCreate', 'context'));
    }

    public function store(Request $request)
    {
        $context = $this->context($request);
        $data = $this->validatedData($request);

        try {
            $masterGuru = null;
            if ($request->filled('nik')) {
                $masterGuru = MasterGuru::where('nik', $request->nik)->first();
            }

            $dapodikGuru = DapodikGuru::create(array_merge(
                $data,
                ['master_guru_id' => $masterGuru?->id, 'employee_category' => $context['category']]
            ));

            toast('Data '.$context['label'].' berhasil ditambahkan.', 'success');

            return redirect()->route($context['route'].'.show', $dapodikGuru);
        } catch (\Exception $e) {
            Log::error('DapodikGuru store error: '.$e->getMessage());
            toast('Gagal menambahkan: '.$e->getMessage(), 'error');

            return back()->withInput();
        }
    }

    public function update(Request $request, DapodikGuru $dapodikGuru)
    {
        $context = $this->context($request);
        abort_unless($dapodikGuru->employee_category === $context['category'], 404);
        $data = $this->validatedData($request, $dapodikGuru);

        try {
            // Re-link master_guru if NIK changed
            $masterGuru = null;
            if ($request->filled('nik')) {
                $masterGuru = MasterGuru::where('nik', $request->nik)->first();
            }

            $dapodikGuru->update(array_merge(
                $data,
                ['master_guru_id' => $masterGuru?->id ?? $dapodikGuru->master_guru_id]
            ));

            toast('Data dapodik berhasil diperbarui.', 'success');
        } catch (\Exception $e) {
            Log::error('DapodikGuru update error: '.$e->getMessage());
            toast('Gagal menyimpan: '.$e->getMessage(), 'error');
        }

        return redirect()->route($context['route'].'.show', $dapodikGuru);
    }

    private function validatedData(Request $request, ?DapodikGuru $dapodikGuru = null): array
    {
        return $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('dapodik_gurus', 'nik')->ignore($dapodikGuru?->id)],
            'nuptk' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:255',
            'kewarganegaraan' => 'nullable|string|max:5',
            'no_kk' => 'nullable|string|max:20',
            'nama_ibu_kandung' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:30',
            'status_kepegawaian' => ['nullable', Rule::in(EmploymentStatus::options())],
            'jenis_ptk' => 'nullable|string|max:255',
            'tugas_tambahan' => 'nullable|string|max:255',
            'pangkat_golongan' => 'nullable|string|max:255',
            'sumber_gaji' => 'nullable|string|max:255',
            'lembaga_pengangkatan' => 'nullable|string|max:255',
            'sk_pengangkatan' => 'nullable|string|max:255',
            'tmt_pengangkatan' => 'nullable|date',
            'sk_cpns' => 'nullable|string|max:255',
            'tanggal_cpns' => 'nullable|date',
            'tmt_pns' => 'nullable|date',
            'nuks' => 'nullable|string|max:30',
            'karpeg' => 'nullable|string|max:20',
            'karis_karsu' => 'nullable|string|max:20',
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'nama_dusun' => 'nullable|string|max:255',
            'desa_kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'hp' => 'nullable|string|max:20',
            'email_dapodik' => 'nullable|email',
            'lintang' => 'nullable|string|max:255',
            'bujur' => 'nullable|string|max:255',
            'status_perkawinan' => 'nullable|string|max:255',
            'nama_pasangan' => 'nullable|string|max:255',
            'nip_pasangan' => 'nullable|string|max:30',
            'pekerjaan_pasangan' => 'nullable|string|max:255',
            'npwp' => 'nullable|string|max:30',
            'nama_wajib_pajak' => 'nullable|string|max:255',
            'bank' => 'nullable|string|max:255',
            'no_rekening' => 'nullable|string|max:30',
            'rekening_atas_nama' => 'nullable|string|max:255',
            'lisensi_kepala_sekolah' => 'nullable|in:Ya,Tidak',
            'diklat_kepengawasan' => 'nullable|in:Ya,Tidak',
            'keahlian_braille' => 'nullable|in:Ya,Tidak',
            'keahlian_bahasa_isyarat' => 'nullable|in:Ya,Tidak',
        ]);
    }

    public function updateMapping(Request $request, DapodikGuru $dapodikGuru)
    {
        $context = $this->context($request);
        abort_unless($dapodikGuru->employee_category === $context['category'], 404);
        $data = $request->validate([
            'master_guru_id' => [
                'nullable',
                Rule::exists('master_gurus', 'id')->where('employee_category', $context['category']),
            ],
        ]);

        DB::transaction(function () use ($dapodikGuru, $data) {
            $masterGuruId = $data['master_guru_id'] ?? null;

            if ($masterGuruId) {
                DapodikGuru::where('master_guru_id', $masterGuruId)
                    ->whereKeyNot($dapodikGuru->id)
                    ->update(['master_guru_id' => null]);
            }

            $dapodikGuru->update(['master_guru_id' => $masterGuruId]);
        });

        return back()->with('success', 'Mapping '.$context['label'].' ke data pegawai berhasil diperbarui.');
    }

    public function reconcileTpaAccount(Request $request, DapodikGuru $dapodikGuru)
    {
        abort_unless($request->routeIs('dapodik-tpa.*') && $dapodikGuru->is_tpa, 404);

        $input = $request->validate([
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
        ]);

        $result = DB::transaction(function () use ($dapodikGuru, $input) {
            $dapodik = DapodikGuru::query()->lockForUpdate()->findOrFail($dapodikGuru->id);
            $account = User::with('roles')->lockForUpdate()->findOrFail($input['user_id']);

            if ($account->hasRole('Siswa')) {
                throw ValidationException::withMessages([
                    'user_id' => 'Akun siswa tidak dapat ditautkan sebagai akun pegawai.',
                ]);
            }

            $emailOwner = filled($dapodik->email_dapodik)
                ? User::whereRaw('LOWER(email) = ?', [Str::lower(trim($dapodik->email_dapodik))])->first()
                : null;
            if ($emailOwner && $emailOwner->id !== $account->id) {
                throw ValidationException::withMessages([
                    'user_id' => 'Email Dapodik sudah digunakan akun '.$emailOwner->name.'. Perbaiki email Dapodik sebelum melakukan rekonsiliasi.',
                ]);
            }

            $source = $dapodik->master_guru_id
                ? MasterGuru::query()->lockForUpdate()->find($dapodik->master_guru_id)
                : null;
            $target = MasterGuru::query()->lockForUpdate()->where('user_id', $account->id)->first();

            if ($source?->user_id && $source->user_id !== $account->id) {
                throw ValidationException::withMessages([
                    'user_id' => 'Data Dapodik sedang terhubung ke akun lain. Lepaskan atau periksa mapping tersebut terlebih dahulu.',
                ]);
            }

            if (! $target) {
                $target = $source ?: MasterGuru::create([
                    'nama_lengkap' => $dapodik->nama,
                    'jenis_kelamin' => in_array($dapodik->jenis_kelamin, ['L', 'P'], true) ? $dapodik->jenis_kelamin : 'L',
                    'employee_category' => MasterGuru::CATEGORY_TPA,
                ]);
            }

            $otherDapodik = DapodikGuru::where('master_guru_id', $target->id)
                ->whereKeyNot($dapodik->id)
                ->first();
            if ($otherDapodik) {
                throw ValidationException::withMessages([
                    'user_id' => 'Akun tersebut sudah terhubung dengan data Dapodik '.$otherDapodik->nama.'. Periksa kemungkinan data ganda.',
                ]);
            }

            $this->reconcileIdentity($dapodik, $source, $target);

            $target->user_id = $account->id;
            $target->employee_category = MasterGuru::CATEGORY_TPA;
            $target->save();

            $dapodik->update([
                'master_guru_id' => $target->id,
                'employee_category' => DapodikGuru::CATEGORY_TPA,
            ]);

            $archivedDuplicate = false;
            if ($source && $source->id !== $target->id && ! $source->user_id && ! DapodikGuru::where('master_guru_id', $source->id)->exists()) {
                $source->is_active = false;
                $source->save();
                $archivedDuplicate = true;
            }

            return compact('account', 'archivedDuplicate');
        });

        $message = 'Akun SISFO '.$result['account']->name.' berhasil direkonsiliasi. Seluruh role akun tetap dipertahankan.';
        if ($result['archivedDuplicate']) {
            $message .= ' Master pegawai duplikat lama telah dinonaktifkan.';
        }

        return back()->with('success', $message);
    }

    public function destroyTpa(Request $request, DapodikGuru $dapodikGuru)
    {
        abort_unless($request->routeIs('dapodik-tpa.*') && $dapodikGuru->is_tpa, 404);

        $name = $dapodikGuru->nama;
        $dapodikGuru->delete();

        return redirect()->route('dapodik-tpa.index')->with(
            'success',
            'Data Dapodik TPA '.$name.' berhasil dihapus. Master pegawai, akun SISFO, dan seluruh role tetap disimpan.'
        );
    }

    private function reconcileIdentity(DapodikGuru $dapodik, ?MasterGuru $source, MasterGuru $target): void
    {
        foreach (['nik' => 'NIK', 'nuptk' => 'NUPTK'] as $field => $label) {
            $incoming = filled($dapodik->{$field}) ? trim((string) $dapodik->{$field}) : null;
            $current = filled($target->{$field}) ? trim((string) $target->{$field}) : null;

            if ($incoming && $current && $incoming !== $current) {
                throw ValidationException::withMessages([
                    'user_id' => $label.' akun terpilih berbeda dengan '.$label.' Dapodik. Periksa identitas pegawai terlebih dahulu.',
                ]);
            }

            if (! $incoming || $current) {
                continue;
            }

            $owner = MasterGuru::where($field, $incoming)->lockForUpdate()->first();
            if ($owner && $owner->id !== $target->id && $owner->id !== $source?->id) {
                throw ValidationException::withMessages([
                    'user_id' => $label.' Dapodik telah dimiliki master pegawai lain. Periksa kemungkinan data ganda.',
                ]);
            }

            if ($source && $source->id !== $target->id && $source->{$field} === $incoming) {
                $source->{$field} = null;
                $source->save();
            }

            $target->{$field} = $incoming;
        }
    }

    public function import(Request $request)
    {
        $context = $this->context($request);
        $request->validate([
            'file_import' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $import = new DapodikGuruImport($context['category']);
            Excel::import($import, $request->file('file_import'));

            $msg = "Import selesai: {$import->created} data baru, {$import->updated} diperbarui";
            if ($import->skipped > 0) {
                $msg .= ", {$import->skipped} dilewati";
            }
            toast($msg.'.', 'success');

            if (! empty($import->errors)) {
                session()->flash('dapodik_import_errors', $import->errors);
            }
        } catch (\Exception $e) {
            Log::error('DapodikGuru import error: '.$e->getMessage());
            toast('Gagal memproses file: '.$e->getMessage(), 'error');
        }

        return back();
    }

    public function syncTpaAccounts()
    {
        $role = Role::findOrCreate('TPA', 'web');
        $summary = ['linked' => 0, 'created' => 0, 'unchanged' => 0, 'skipped' => 0];
        $credentials = [];
        $errors = [];

        DapodikGuru::query()
            ->where('employee_category', DapodikGuru::CATEGORY_TPA)
            ->with('masterGuru.user')
            ->orderBy('id')
            ->each(function (DapodikGuru $dapodik) use ($role, &$summary, &$credentials, &$errors) {
                try {
                    $result = DB::transaction(fn () => $this->syncTpaAccount($dapodik, $role));
                    $summary[$result['status']]++;

                    if (isset($result['credential'])) {
                        $credentials[] = $result['credential'];
                    }
                } catch (Throwable $exception) {
                    $summary['skipped']++;
                    $errors[] = $dapodik->nama.': '.$exception->getMessage();
                    Log::warning('Gagal sinkronisasi akun TPA', [
                        'dapodik_guru_id' => $dapodik->id,
                        'message' => $exception->getMessage(),
                    ]);
                }
            });

        $message = "Sinkronisasi akun TPA selesai: {$summary['linked']} akun lama ditautkan, {$summary['created']} akun baru dibuat, {$summary['unchanged']} sudah terhubung";
        if ($summary['skipped']) {
            $message .= ", {$summary['skipped']} perlu ditangani";
        }

        return back()
            ->with('success', $message.'.')
            ->with('tpa_generated_credentials', $credentials)
            ->with('tpa_account_sync_errors', $errors);
    }

    private function syncTpaAccount(DapodikGuru $dapodik, Role $role): array
    {
        $email = Str::lower(trim((string) $dapodik->email_dapodik));
        $emailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        $matchedUser = $emailIsValid ? User::where('email', $email)->first() : null;
        $masterByNik = $dapodik->nik ? MasterGuru::where('nik', $dapodik->nik)->first() : null;
        $masterByNuptk = $dapodik->nuptk ? MasterGuru::where('nuptk', $dapodik->nuptk)->first() : null;
        $mappedMaster = $dapodik->masterGuru;
        $userMaster = $matchedUser?->masterGuru;

        $masterIds = collect([$mappedMaster?->id, $masterByNik?->id, $masterByNuptk?->id, $userMaster?->id])->filter()->unique();
        if ($masterIds->count() > 1) {
            throw new \RuntimeException('NIK, NUPTK, email, atau mapping mengarah ke pegawai yang berbeda. Gunakan Rekonsiliasi Akun SISFO untuk memeriksanya.');
        }

        $master = $mappedMaster ?? $masterByNik ?? $masterByNuptk ?? $userMaster;
        if (! $master) {
            if (! $dapodik->nik) {
                throw new \RuntimeException('NIK kosong sehingga master pegawai tidak dapat dibuat.');
            }

            $master = MasterGuru::create([
                'nama_lengkap' => $dapodik->nama,
                'nik' => $dapodik->nik,
                'nuptk' => $this->availableMasterNuptk($dapodik->nuptk),
                'jenis_kelamin' => in_array($dapodik->jenis_kelamin, ['L', 'P'], true) ? $dapodik->jenis_kelamin : 'L',
                'employee_category' => MasterGuru::CATEGORY_TPA,
            ]);
        } else {
            $master->update([
                'nama_lengkap' => $dapodik->nama,
                'nik' => $dapodik->nik ?: $master->nik,
                'nuptk' => $this->availableMasterNuptk($dapodik->nuptk, $master) ?: $master->nuptk,
                'jenis_kelamin' => in_array($dapodik->jenis_kelamin, ['L', 'P'], true) ? $dapodik->jenis_kelamin : $master->jenis_kelamin,
                'employee_category' => MasterGuru::CATEGORY_TPA,
            ]);
        }

        DapodikGuru::where('master_guru_id', $master->id)
            ->whereKeyNot($dapodik->id)
            ->update(['master_guru_id' => null]);
        $dapodik->update(['master_guru_id' => $master->id]);

        if ($master->user_id) {
            return ['status' => 'unchanged'];
        }

        if ($matchedUser) {
            if ($matchedUser->masterGuru && $matchedUser->masterGuru->id !== $master->id) {
                throw new \RuntimeException('Email sudah terhubung ke master pegawai lain.');
            }

            $master->update(['user_id' => $matchedUser->id]);

            // Role akun lama sengaja tidak diubah.
            return ['status' => 'linked'];
        }

        if (! $emailIsValid) {
            throw new \RuntimeException('Email Dapodik kosong atau tidak valid; akun baru belum dibuat.');
        }

        $temporaryPassword = Str::random(12);
        $user = User::create([
            'name' => $dapodik->nama,
            'email' => $email,
            'phone_number' => $dapodik->hp,
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();
        $user->assignRole($role);
        $master->update(['user_id' => $user->id]);

        return [
            'status' => 'created',
            'credential' => [
                'name' => $dapodik->nama,
                'email' => $email,
                'password' => $temporaryPassword,
            ],
        ];
    }

    private function availableMasterNuptk(?string $nuptk, ?MasterGuru $except = null): ?string
    {
        if (! $nuptk) {
            return null;
        }

        $exists = MasterGuru::where('nuptk', $nuptk)
            ->when($except, fn ($query) => $query->whereKeyNot($except->id))
            ->exists();

        return $exists ? null : $nuptk;
    }

    private function context(Request $request): array
    {
        $isTpa = $request->routeIs('dapodik-tpa.*');

        return [
            'category' => $isTpa ? DapodikGuru::CATEGORY_TPA : DapodikGuru::CATEGORY_TEACHER,
            'route' => $isTpa ? 'dapodik-tpa' : 'dapodik-guru',
            'label' => $isTpa ? 'Dapodik TPA' : 'Dapodik Guru',
            'person_label' => $isTpa ? 'TPA' : 'Guru',
            'description' => $isTpa ? 'Tenaga Penunjang Akademik' : 'Guru',
        ];
    }
}
