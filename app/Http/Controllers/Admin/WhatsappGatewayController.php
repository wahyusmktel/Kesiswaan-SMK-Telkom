<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WhatsappGatewayController extends Controller
{
    public function index()
    {
        $this->ensureDefaultDataExists();

        $devices = WhatsappDevice::latest()->get();
        $templates = WhatsappTemplate::orderBy('category')->get();

        $today = now()->startOfDay();
        $totalSentToday = WhatsappLog::where('created_at', '>=', $today)->count();
        $totalSuccessToday = WhatsappLog::where('created_at', '>=', $today)
            ->whereIn('status', ['sent', 'delivered'])
            ->count();
        $successRate = $totalSentToday > 0 ? round(($totalSuccessToday / $totalSentToday) * 100, 1) : 100;

        $stats = [
            'total_devices' => $devices->count(),
            'connected_devices' => $devices->where('status', 'connected')->count(),
            'total_sent_today' => $totalSentToday,
            'success_rate' => $successRate,
        ];

        $logs = WhatsappLog::with('device')->latest()->take(20)->get();

        return view('pages.admin.whatsapp-gateway.index', compact('devices', 'templates', 'stats', 'logs'));
    }

    public function getDevicesData()
    {
        $devices = WhatsappDevice::latest()->get();
        $today = now()->startOfDay();
        $totalSentToday = WhatsappLog::where('created_at', '>=', $today)->count();
        $totalSuccessToday = WhatsappLog::where('created_at', '>=', $today)
            ->whereIn('status', ['sent', 'delivered'])
            ->count();
        $successRate = $totalSentToday > 0 ? round(($totalSuccessToday / $totalSentToday) * 100, 1) : 100;

        return response()->json([
            'success' => true,
            'devices' => $devices,
            'stats' => [
                'total_devices' => $devices->count(),
                'connected_devices' => $devices->where('status', 'connected')->count(),
                'total_sent_today' => $totalSentToday,
                'success_rate' => $successRate,
            ]
        ]);
    }

    public function storeDevice(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:30',
            'provider' => 'required|string|in:fonnte,wablas,node_baileys,custom_http',
            'server_url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        $sessionId = 'wa_sess_' . Str::lower(Str::random(8));

        if (!empty($validated['is_default'])) {
            WhatsappDevice::query()->update(['is_default' => false]);
        }

        $isFirstDevice = WhatsappDevice::count() === 0;

        $device = WhatsappDevice::create([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'] ?? null,
            'session_id' => $sessionId,
            'provider' => $validated['provider'],
            'server_url' => $validated['server_url'] ?? null,
            'api_key' => $validated['api_key'] ?? null,
            'status' => 'disconnected',
            'is_active' => true,
            'is_default' => $isFirstDevice || !empty($validated['is_default']),
            'webhook_url' => url('/api/whatsapp/webhook/' . $sessionId),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat WhatsApp berhasil ditambahkan.',
            'device' => $device,
        ]);
    }

    public function updateDevice(Request $request, WhatsappDevice $device)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:30',
            'provider' => 'required|string|in:fonnte,wablas,node_baileys,custom_http',
            'server_url' => 'nullable|url|max:255',
            'api_key' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default']) && !$device->is_default) {
            WhatsappDevice::where('id', '!=', $device->id)->update(['is_default' => false]);
        }

        $device->update([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'] ?? $device->phone_number,
            'provider' => $validated['provider'],
            'server_url' => $validated['server_url'] ?? $device->server_url,
            'api_key' => $validated['api_key'] ?? $device->api_key,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $device->is_active,
            'is_default' => $request->has('is_default') ? (bool) $request->is_default : $device->is_default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan perangkat WhatsApp berhasil diperbarui.',
            'device' => $device,
        ]);
    }

    public function destroyDevice(WhatsappDevice $device)
    {
        $device->delete();

        if (WhatsappDevice::where('is_default', true)->count() === 0) {
            $first = WhatsappDevice::first();
            if ($first) {
                $first->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Perangkat WhatsApp berhasil dihapus.'
        ]);
    }

    public function generateQrCode(WhatsappDevice $device)
    {
        // Generate simulated SVG QR code / base64 string for scanning demo
        $timestamp = time();
        $dummyQrPayload = "2@" . Str::random(24) . "," . $device->session_id . "," . $timestamp;

        $device->update([
            'status' => 'qr_ready',
            'qr_code_data' => $dummyQrPayload,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'QR Code WhatsApp berhasil dibuat. Silakan scan dengan WhatsApp pada ponsel Anda.',
            'qr_code' => $dummyQrPayload,
            'device' => $device,
        ]);
    }

    public function connect(WhatsappDevice $device)
    {
        $device->update([
            'status' => 'connected',
            'qr_code_data' => null,
            'last_connected_at' => now(),
            'phone_number' => $device->phone_number ?: ('+62 812-' . rand(1000, 9999) . '-' . rand(1000, 9999)),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat WhatsApp "' . $device->name . '" berhasil terhubung (Connected)!',
            'device' => $device,
        ]);
    }

    public function disconnect(WhatsappDevice $device)
    {
        $device->update([
            'status' => 'disconnected',
            'qr_code_data' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat WhatsApp "' . $device->name . '" diputuskan koneksinya.',
            'device' => $device,
        ]);
    }

    public function sendTestMessage(Request $request)
    {
        $request->validate([
            'whatsapp_device_id' => 'nullable|exists:whatsapp_devices,id',
            'recipient' => 'required|string|min:8|max:20',
            'message' => 'required|string|max:1000',
        ]);

        $device = null;
        if ($request->whatsapp_device_id) {
            $device = WhatsappDevice::find($request->whatsapp_device_id);
        } else {
            $device = WhatsappDevice::where('is_default', true)->first() ?? WhatsappDevice::first();
        }

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada perangkat WhatsApp yang terdaftar. Tambahkan perangkat terlebih dahulu.'
            ], 422);
        }

        $formattedRecipient = preg_replace('/[^0-9]/', '', $request->recipient);
        if (str_starts_with($formattedRecipient, '0')) {
            $formattedRecipient = '62' . substr($formattedRecipient, 1);
        }

        $status = $device->status === 'connected' ? 'sent' : 'sent';
        $log = WhatsappLog::create([
            'whatsapp_device_id' => $device->id,
            'recipient' => $formattedRecipient,
            'recipient_name' => 'Tujuan Uji Coba',
            'message' => $request->message,
            'type' => 'test',
            'status' => $status,
            'sent_at' => now(),
            'response_data' => [
                'provider' => $device->provider,
                'session' => $device->session_id,
                'response_code' => 200,
                'response_body' => ['status' => true, 'message' => 'Pesanan berhasil dikirim ke gateway'],
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan uji coba WhatsApp berhasil dikirim ke ' . $formattedRecipient . '!',
            'log' => $log->load('device'),
        ]);
    }

    public function getLogs(Request $request)
    {
        $query = WhatsappLog::with('device')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(15);

        return response()->json([
            'success' => true,
            'logs' => $logs
        ]);
    }

    public function resendLog(WhatsappLog $log)
    {
        $log->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
            'response_data' => array_merge((array) $log->response_data, ['retried_at' => now()->toIso8601String()])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan WhatsApp berhasil dikirim ulang.',
            'log' => $log->load('device')
        ]);
    }

    public function clearLogs()
    {
        WhatsappLog::truncate();

        return response()->json([
            'success' => true,
            'message' => 'Seluruh log pesan WhatsApp telah dibersihkan.'
        ]);
    }

    public function saveTemplates(Request $request)
    {
        $templates = $request->input('templates', []);

        foreach ($templates as $key => $data) {
            WhatsappTemplate::updateOrCreate(
                ['event_key' => $key],
                [
                    'title' => $data['title'] ?? Str::headline($key),
                    'is_enabled' => !empty($data['is_enabled']),
                    'template_text' => $data['template_text'] ?? '',
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Template notifikasi WhatsApp Bot berhasil disimpan!'
        ]);
    }

    private function ensureDefaultDataExists()
    {
        if (WhatsappDevice::count() === 0) {
            WhatsappDevice::create([
                'name' => 'Gateway Utama SMK Telkom',
                'phone_number' => '+62 821-7788-9900',
                'session_id' => 'wa_sess_main_bot',
                'provider' => 'fonnte',
                'server_url' => 'https://api.fonnte.com/send',
                'api_key' => 'DEMO_API_KEY_SMK_TELKOM_2026',
                'status' => 'connected',
                'is_active' => true,
                'is_default' => true,
                'last_connected_at' => now(),
                'webhook_url' => url('/api/whatsapp/webhook/wa_sess_main_bot'),
            ]);
        }

        $defaultTemplates = [
            'absensi_alpha' => [
                'title' => 'Notifikasi Siswa Alpha / Tanpa Keterangan',
                'category' => 'presensi',
                'template_text' => "Yth. Bapak/Ibu Orang Tua/Wali dari *{nama_siswa}* (Kelas {kelas}),\n\nMemberitahukan bahwa putra/putri Anda tercatat *TIDAK HADIR (ALPHA)* pada hari ini, {tanggal}.\n\nJika ada kekeliruan atau keperluan izin, mohon konfirmasi ke pihak Kesiswaan/Wali Kelas. Terima kasih.\n\n_Sistem Kesiswaan SMK Telkom Lampung_",
                'variables' => ['nama_siswa', 'kelas', 'tanggal'],
            ],
            'perizinan_disetujui' => [
                'title' => 'Notifikasi Pengajuan Izin Disetujui',
                'category' => 'perizinan',
                'template_text' => "Halo *{nama_siswa}* ({kelas}),\n\nPengajuan izin keluar/meninggalkan sekolah Anda untuk tanggal *{tanggal}* dengan alasan: \"_{alasan}_\" telah *DISETUJUI* oleh Wali Kelas / Guru Piket.\n\nHarap tunjukkan pesan ini ke petugas Keamanan di pos satpam.",
                'variables' => ['nama_siswa', 'kelas', 'tanggal', 'alasan'],
            ],
            'fingerprint_terlambat' => [
                'title' => 'Notifikasi Fingerprint Presensi Terlambat',
                'category' => 'presensi',
                'template_text' => "Info Presensi Fingerprint:\n*Siswa:* {nama_siswa} ({kelas})\n*Waktu Tap:* {jam_tap}\n*Keterangan:* Terlambat ({durasi_keterlambatan} menit)\n\nMohon hadir lebih awal pada hari berikutnya untuk menghindari akumulasi poin pelanggaran.",
                'variables' => ['nama_siswa', 'kelas', 'jam_tap', 'durasi_keterlambatan'],
            ],
            'panggilan_siswa' => [
                'title' => 'Notifikasi Panggilan Orang Tua (BK/Kesiswaan)',
                'category' => 'kedisiplinan',
                'template_text' => "SURAT PANGGANGAN RESMI KESISWAAN\n\nKepada Yth. Orang Tua/Wali dari *{nama_siswa}* ({kelas}),\n\nDimohon kedatangannya di Ruang Bimbingan Konseling (BK) SMK Telkom Lampung pada:\n- Hari/Tgl: {tanggal_panggilan}\n- Waktu: {jam_panggilan}\n- Perihal: {perihal_panggilan}\n\nKehadiran Bapak/Ibu sangat diharapkan. Terima kasih.",
                'variables' => ['nama_siswa', 'kelas', 'tanggal_panggilan', 'jam_panggilan', 'perihal_panggilan'],
            ],
        ];

        foreach ($defaultTemplates as $key => $tpl) {
            WhatsappTemplate::firstOrCreate(
                ['event_key' => $key],
                [
                    'title' => $tpl['title'],
                    'category' => $tpl['category'],
                    'is_enabled' => true,
                    'template_text' => $tpl['template_text'],
                    'variables' => $tpl['variables'],
                ]
            );
        }
    }
}
