<?php

namespace App\Exports;

use App\Models\MasterGuru;
use App\Models\TelegramBot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TelegramPegawaiLinkExport implements FromCollection, ShouldAutoSize, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;
    private Collection $data;
    private ?TelegramBot $bot = null;
    private int $totalEmployees = 0;
    private int $totalLinked = 0;
    private int $totalUnlinked = 0;

    public function __construct(private ?int $botId = null)
    {
        if ($this->botId) {
            $this->bot = TelegramBot::find($this->botId);
        }
        $this->data = $this->collectEmployeesData();
        $this->totalEmployees = $this->data->count();
        $this->totalLinked = $this->data->where('is_linked', true)->count();
        $this->totalUnlinked = $this->totalEmployees - $this->totalLinked;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'Kategori',
            'Jabatan / Jenis PTK',
            'Kode / NIP / NUPTK',
            'No. HP (Dapodik)',
            'Email Akun SISFO',
            'Status Telegram',
            'Bot Telegram',
            'Username Telegram',
            'Nama Akun Telegram',
            'Chat ID',
            'Tanggal Terhubung',
            'Interaksi Terakhir',
            'Keterangan & Kesiapan',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row['nama_pegawai'],
            $row['kategori'],
            $row['jenis_ptk'],
            $row['kode_pegawai'],
            $row['hp_dapodik'],
            $row['email_sisfo'],
            $row['is_linked'] ? 'TERHUBUNG' : 'BELUM TERHUBUNG',
            $row['bot_name'],
            $row['telegram_username'],
            $row['telegram_name'],
            $row['chat_id'],
            $row['linked_at'],
            $row['last_interaction_at'],
            $row['keterangan'],
        ];
    }

    public function title(): string
    {
        return 'Status Telegram Pegawai';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 32,  // Nama Pegawai
            'C' => 22,  // Kategori
            'D' => 24,  // Jabatan / PTK
            'E' => 18,  // Kode / NIP
            'F' => 18,  // No HP Dapodik
            'G' => 26,  // Email SISFO
            'H' => 18,  // Status
            'I' => 24,  // Bot
            'J' => 20,  // Username
            'K' => 24,  // Nama Telegram
            'L' => 16,  // Chat ID
            'M' => 18,  // Tgl Terhubung
            'N' => 18,  // Terakhir Interaksi
            'O' => 34,  // Keterangan
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Sisipkan 6 baris di bagian atas untuk Kop Laporan dan Ringkasan KPI
        $sheet->insertNewRowBefore(1, 6);

        // Header Title
        $sheet->setCellValue('A1', 'DAFTAR STATUS KONEKSI AKUN TELEGRAM BOT PEGAWAI');
        $sheet->setCellValue('A2', 'SMK TELKOM LAMPUNG — SISTEM INFORMASI KESISWAAN & KEPEGAWAIAN (SISFO)');
        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');

        $botInfo = $this->bot ? "Bot Spesifik: {$this->bot->name} ({$this->bot->purpose})" : 'Cakupan: Seluruh Bot Telegram SISFO';
        $exportTime = Carbon::now('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB';

        // KPI Ringkasan
        $persenLinked = $this->totalEmployees > 0 ? round(($this->totalLinked / $this->totalEmployees) * 100, 1) : 0;
        $persenUnlinked = $this->totalEmployees > 0 ? round(($this->totalUnlinked / $this->totalEmployees) * 100, 1) : 0;

        $sheet->setCellValue('A4', "Total Pegawai: {$this->totalEmployees} Orang");
        $sheet->setCellValue('D4', "Sudah Terhubung: {$this->totalLinked} Orang ({$persenLinked}%)");
        $sheet->setCellValue('H4', "Belum Terhubung: {$this->totalUnlinked} Orang ({$persenUnlinked}%)");
        $sheet->setCellValue('L4', "Waktu Export: {$exportTime}");

        $sheet->setCellValue('A5', $botInfo);
        $sheet->mergeCells('A5:O5');

        // Style Judul Kop
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0284C7']], // Sky 600
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => 'E0F2FE']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0369A1']], // Sky 700
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Style Baris KPI (Baris 4)
        $sheet->getStyle('A4:O4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '0F172A']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('D4')->getFont()->getColor()->setRGB('15803D'); // Hijau untuk Terhubung
        $sheet->getStyle('H4')->getFont()->getColor()->setRGB('B91C1C'); // Merah untuk Belum Terhubung
        $sheet->getRowDimension(4)->setRowHeight(22);

        // Style Baris Info Bot (Baris 5)
        $sheet->getStyle('A5:O5')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '64748B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(18);

        // Baris Header Kolom Tabel (Sekarang baris 7)
        $headerRow = 7;
        $sheet->getStyle("A{$headerRow}:O{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']], // Slate 900
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '334155']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        $lastRow = $sheet->getHighestRow();

        // Style Baris Data (Baris 8 sampai lastRow)
        if ($lastRow >= 8) {
            $sheet->getStyle("A8:O{$lastRow}")->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);

            // Alignment khusus kolom
            $sheet->getStyle("A8:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C8:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E8:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H8:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J8:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("L8:N{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Styling status badge per baris
            for ($r = 8; $r <= $lastRow; $r++) {
                $statusVal = (string) $sheet->getCell("H{$r}")->getValue();
                $isLinked = $statusVal === 'TERHUBUNG';

                if ($isLinked) {
                    $sheet->getStyle("H{$r}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']], // Emerald 100
                        'font' => ['bold' => true, 'color' => ['rgb' => '166534']], // Emerald 800
                    ]);
                } else {
                    $sheet->getStyle("H{$r}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']], // Red 100
                        'font' => ['bold' => true, 'color' => ['rgb' => '991B1B']], // Red 800
                    ]);
                }

                $sheet->getRowDimension($r)->setRowHeight(20);
            }
        }

        // Freeze Pane agar header tetap tampak saat scroll ke bawah
        $sheet->freezePane('A8');

        // AutoFilter pada Header Tabel
        $sheet->setAutoFilter("A{$headerRow}:O{$lastRow}");

        return [];
    }

    /**
     * Kumpulkan seluruh data pegawai sekolah beserta status Telegram link mereka.
     */
    private function collectEmployeesData(): Collection
    {
        $masterGurus = MasterGuru::with([
            'user.roles',
            'user.telegramLinks.bot',
            'dapodikGuru',
        ])
        ->orderBy('nama_lengkap')
        ->get();

        $rows = collect();
        $processedUserIds = [];

        foreach ($masterGurus as $mg) {
            $user = $mg->user;
            if ($user) {
                $processedUserIds[] = (int) $user->id;
            }

            $userLinks = $user?->telegramLinks ?? collect();
            if ($this->botId) {
                $userLinks = $userLinks->where('telegram_bot_id', $this->botId);
            }

            $isLinked = $userLinks->isNotEmpty();
            $link = $userLinks->first();

            // Format informasi bot jika terhubung ke lebih dari 1 bot
            $botNames = $isLinked ? $userLinks->map(fn ($l) => $l->bot?->name)->filter()->unique()->join(', ') : '-';
            $usernames = $isLinked ? $userLinks->map(fn ($l) => $l->telegram_username ? '@' . $l->telegram_username : null)->filter()->unique()->join(', ') : '-';
            if ($usernames === '') {
                $usernames = '-';
            }
            $telegramNames = $isLinked ? $userLinks->map(fn ($l) => $l->telegram_name)->filter()->unique()->join(', ') : '-';
            $chatIds = $isLinked ? $userLinks->pluck('chat_id')->filter()->unique()->join(', ') : '-';

            $firstLinkedAt = $isLinked && $link?->linked_at ? $link->linked_at->format('d/m/Y H:i') : '-';
            $lastInteractionAt = $isLinked && $link?->last_interaction_at ? $link->last_interaction_at->format('d/m/Y H:i') : '-';

            // Keterangan / Analisis Kesiapan
            if ($isLinked) {
                $keterangan = 'Akun Telegram terhubung aktif';
            } elseif (! $user) {
                $keterangan = 'Belum dibuatkan akun pengguna SISFO';
            } elseif (empty($mg->dapodikGuru?->hp)) {
                $keterangan = 'Nomor HP di Dapodik belum diisi';
            } else {
                $keterangan = 'Siap dihubungkan (HP: ' . $mg->dapodikGuru->hp . ')';
            }

            $rows->push([
                'nama_pegawai' => $mg->nama_lengkap ?: ($user?->name ?? '-'),
                'kategori' => $mg->employee_category === MasterGuru::CATEGORY_TPA ? 'Tenaga Kependidikan (TPA)' : 'Guru',
                'jenis_ptk' => $mg->dapodikGuru?->jenis_ptk ?: ($mg->employee_category === MasterGuru::CATEGORY_TPA ? 'Staff Tata Usaha' : 'Guru Mapel'),
                'kode_pegawai' => $mg->kode_guru ?: ($mg->dapodikGuru?->nip ?: ($mg->nuptk ?: '-')),
                'hp_dapodik' => $mg->dapodikGuru?->hp ?: '-',
                'email_sisfo' => $user?->email ?: ($mg->dapodikGuru?->email_dapodik ?: '-'),
                'is_linked' => $isLinked,
                'bot_name' => $botNames ?: '-',
                'telegram_username' => $usernames ?: '-',
                'telegram_name' => $telegramNames ?: '-',
                'chat_id' => $chatIds ?: '-',
                'linked_at' => $firstLinkedAt,
                'last_interaction_at' => $lastInteractionAt,
                'keterangan' => $keterangan,
            ]);
        }

        // Sertakan juga akun pengguna lain yang berperan staf/pegawai (non-guru) yang belum tercatat di MasterGuru
        $otherEmployees = User::with(['roles', 'telegramLinks.bot'])
            ->whereNotIn('id', array_filter($processedUserIds))
            ->whereDoesntHave('roles', fn ($q) => $q->whereIn('name', ['Siswa', 'siswa', 'Kantin', 'Alumni', 'Orang Tua']))
            ->whereHas('roles')
            ->orderBy('name')
            ->get();

        foreach ($otherEmployees as $user) {
            $userLinks = $user->telegramLinks ?? collect();
            if ($this->botId) {
                $userLinks = $userLinks->where('telegram_bot_id', $this->botId);
            }

            $isLinked = $userLinks->isNotEmpty();
            $link = $userLinks->first();

            $botNames = $isLinked ? $userLinks->map(fn ($l) => $l->bot?->name)->filter()->unique()->join(', ') : '-';
            $usernames = $isLinked ? $userLinks->map(fn ($l) => $l->telegram_username ? '@' . $l->telegram_username : null)->filter()->unique()->join(', ') : '-';
            if ($usernames === '') {
                $usernames = '-';
            }
            $telegramNames = $isLinked ? $userLinks->map(fn ($l) => $l->telegram_name)->filter()->unique()->join(', ') : '-';
            $chatIds = $isLinked ? $userLinks->pluck('chat_id')->filter()->unique()->join(', ') : '-';

            $firstLinkedAt = $isLinked && $link?->linked_at ? $link->linked_at->format('d/m/Y H:i') : '-';
            $lastInteractionAt = $isLinked && $link?->last_interaction_at ? $link->last_interaction_at->format('d/m/Y H:i') : '-';

            $roleTitle = $user->getRoleNames()->join(', ');

            $rows->push([
                'nama_pegawai' => $user->name,
                'kategori' => 'Pegawai SISFO',
                'jenis_ptk' => $roleTitle ?: 'Pegawai',
                'kode_pegawai' => '-',
                'hp_dapodik' => '-',
                'email_sisfo' => $user->email,
                'is_linked' => $isLinked,
                'bot_name' => $botNames ?: '-',
                'telegram_username' => $usernames ?: '-',
                'telegram_name' => $telegramNames ?: '-',
                'chat_id' => $chatIds ?: '-',
                'linked_at' => $firstLinkedAt,
                'last_interaction_at' => $lastInteractionAt,
                'keterangan' => $isLinked ? 'Akun Telegram terhubung aktif' : 'Belum terhubung bot Telegram',
            ]);
        }

        return $rows;
    }
}
