<?php

namespace App\Services;

use App\Models\OkrWeeklyReport;
use Illuminate\Support\Str;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Slide\Background\Color as BackgroundColor;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

class OkrWeeklyPresentationBuilder
{
    private const NAVY = 'FF172554';

    private const BLUE = 'FF2563EB';

    private const CYAN = 'FF0891B2';

    private const GREEN = 'FF059669';

    private const RED = 'FFDC2626';

    private const INK = 'FF172033';

    private const MUTED = 'FF64748B';

    private const LIGHT = 'FFF8FAFC';

    public function build(OkrWeeklyReport $report, array $narrative): string
    {
        $presentation = new PhpPresentation;
        $presentation->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9, true);
        $presentation->getLayout()
            ->setCX(1280, DocumentLayout::UNIT_PIXEL)
            ->setCY(720, DocumentLayout::UNIT_PIXEL);
        $presentation->getDocumentProperties()
            ->setCreator(config('app.name'))
            ->setCompany('SMK Telkom Lampung')
            ->setTitle($narrative['title'])
            ->setSubject('Evaluasi Jumat OKR '.$report->unit->name)
            ->setDescription('Presentasi otomatis berdasarkan laporan pekanan OKR.');

        $presentation->removeSlideByIndex(0);
        $this->titleSlide($presentation, $report, $narrative);
        $this->overviewSlide($presentation, $report, $narrative);

        foreach ($report->items as $index => $item) {
            $this->commitmentSlide($presentation, $report, $item, $narrative['items'][$index] ?? []);
        }

        $this->closingSlide($presentation, $report, $narrative);

        $directory = storage_path('app/tmp/okr-presentations');
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
        $path = $directory.'/'.Str::uuid().'.pptx';
        IOFactory::createWriter($presentation, 'PowerPoint2007')->save($path);

        return $path;
    }

    private function titleSlide(PhpPresentation $presentation, OkrWeeklyReport $report, array $narrative): void
    {
        $slide = $presentation->createSlide();
        $this->background($slide, self::NAVY);
        $this->accentLine($slide, 72, 72, 92, self::CYAN);
        $this->text($slide, 'EVALUASI PEKANAN OKR', 72, 116, 780, 28, 17, 'FFA5F3FC', true, 1.6);
        $this->text($slide, $narrative['title'], 72, 160, 810, 145, 34, 'FFFFFFFF', true);
        $this->text($slide, $report->unit->name, 72, 330, 650, 36, 20, 'FFDBEAFE', true);
        $this->text(
            $slide,
            $report->week_start->translatedFormat('d M').' – '.$report->week_end->translatedFormat('d M Y'),
            72,
            378,
            650,
            28,
            15,
            'FFBFDBFE'
        );
        $this->text($slide, 'SMK TELKOM LAMPUNG', 72, 632, 500, 22, 11, 'FF94A3B8', true, 1.4);
        $this->logo($slide, 940, 70, 150);
    }

    private function overviewSlide(PhpPresentation $presentation, OkrWeeklyReport $report, array $narrative): void
    {
        $slide = $presentation->createSlide();
        $this->background($slide, 'FFFFFFFF');
        $this->slideHeader($slide, 'Ringkasan Pekan', $report);

        $average = round((float) $report->items->avg('completion_percent'), 1);
        $completed = $report->items->where('final_status', 'completed')->count();
        $blocked = $report->items->where('final_status', 'blocked')->count();
        $metrics = [
            [$average.'%', 'Rata-rata progres', self::BLUE],
            [$completed.'/'.$report->items->count(), 'Komitmen tercapai', self::GREEN],
            [(string) $blocked, 'Komitmen terhambat', $blocked > 0 ? self::RED : self::CYAN],
        ];
        foreach ($metrics as $index => [$value, $label, $color]) {
            $x = 72 + ($index * 278);
            $this->text($slide, $value, $x, 155, 220, 64, 34, $color, true);
            $this->text($slide, $label, $x, 220, 220, 26, 13, self::MUTED, true);
        }

        $this->text($slide, 'Fokus yang disepakati', 72, 305, 410, 25, 12, self::BLUE, true, 1.2);
        $this->text($slide, $report->weekly_focus, 72, 342, 520, 140, 21, self::INK, true);
        $this->text($slide, 'Ringkasan evaluasi', 660, 305, 410, 25, 12, self::CYAN, true, 1.2);
        $this->text($slide, $narrative['opening_summary'], 660, 342, 490, 140, 19, self::INK);

        if ($report->support_needed) {
            $this->text($slide, 'Dukungan yang dibutuhkan: '.$report->support_needed, 72, 535, 1060, 60, 14, 'FF92400E', true);
        }
        $this->slideFooter($slide, 2);
    }

    private function commitmentSlide(PhpPresentation $presentation, OkrWeeklyReport $report, $item, array $copy): void
    {
        $slide = $presentation->createSlide();
        $this->background($slide, self::LIGHT);
        $this->slideHeader($slide, $copy['title'] ?? $item->commitment, $report);

        $progress = min(100, max(0, (float) $item->completion_percent));
        $statusColor = $item->final_status === 'blocked' ? self::RED : ($item->final_status === 'completed' ? self::GREEN : self::BLUE);
        $statusLabel = match ($item->final_status) {
            'completed' => 'Tercapai',
            'blocked' => 'Terhambat',
            'on_progress' => 'Berjalan',
            default => 'Belum dimulai',
        };

        $this->text($slide, $copy['headline'] ?? $item->commitment, 72, 140, 770, 82, 25, self::INK, true);
        $this->text($slide, $progress.'%', 930, 142, 210, 60, 38, $statusColor, true, 0, Alignment::HORIZONTAL_RIGHT);
        $this->text($slide, $statusLabel, 930, 205, 210, 25, 13, $statusColor, true, 1.1, Alignment::HORIZONTAL_RIGHT);
        $this->progressBar($slide, 72, 250, 1068, $progress, $statusColor);

        $this->text($slide, 'Target terukur', 72, 298, 450, 24, 12, self::BLUE, true, 1.1);
        $this->text($slide, $item->measurable_target, 72, 330, 480, 105, 17, self::INK, true);
        $this->text($slide, 'Hasil evaluasi', 630, 298, 450, 24, 12, self::GREEN, true, 1.1);
        $this->text($slide, $copy['result_summary'] ?? ($item->actual_result ?: 'Evaluasi akhir belum diisi.'), 630, 330, 510, 105, 17, self::INK);

        $this->text($slide, 'Perkembangan pekerjaan', 72, 472, 450, 24, 12, self::CYAN, true, 1.1);
        $this->text($slide, $copy['progress_summary'] ?? 'Belum ada pembaruan progres.', 72, 505, 480, 95, 16, self::INK);
        $this->text($slide, $item->final_status === 'blocked' ? 'Kendala utama' : 'Tindak lanjut', 630, 472, 450, 24, 12, $statusColor, true, 1.1);
        $lastMessage = $item->final_status === 'blocked'
            ? ($copy['blocker_summary'] ?? $item->blockers ?? 'Kendala perlu dikonfirmasi dalam forum.')
            : ($copy['next_step'] ?? $item->next_follow_up ?? 'Tidak ada tindak lanjut khusus.');
        $this->text($slide, $lastMessage, 630, 505, 510, 95, 16, self::INK, $item->final_status === 'blocked');
        $this->slideFooter($slide, $item->priority_order + 2);
    }

    private function closingSlide(PhpPresentation $presentation, OkrWeeklyReport $report, array $narrative): void
    {
        $slide = $presentation->createSlide();
        $this->background($slide, self::NAVY);
        $this->accentLine($slide, 72, 76, 92, self::GREEN);
        $this->text($slide, 'KESIMPULAN EVALUASI', 72, 120, 600, 28, 16, 'FF6EE7B7', true, 1.4);
        $this->text($slide, rtrim($narrative['unit_conclusion'], ".!? \t\n\r\0\x0B"), 72, 175, 850, 150, 30, 'FFFFFFFF', true);

        $followUps = $report->items->pluck('next_follow_up')->filter()->values();
        if ($followUps->isNotEmpty()) {
            $this->text($slide, 'Prioritas pekan berikutnya', 72, 380, 520, 28, 14, 'FFA5F3FC', true);
            $y = 425;
            foreach ($followUps->take(3) as $index => $followUp) {
                $this->text($slide, ($index + 1).'. '.$followUp, 72, $y, 920, 42, 16, 'FFDBEAFE');
                $y += 52;
            }
        }
        $this->text($slide, $report->unit->name.' · '.$report->week_end->translatedFormat('d M Y'), 72, 632, 700, 24, 11, 'FF94A3B8', true);
        $this->text($slide, (string) ($report->items->count() + 3), 1100, 672, 40, 18, 9, 'FF94A3B8', true, 0, Alignment::HORIZONTAL_RIGHT);
        $this->logo($slide, 1000, 500, 140);
    }

    private function slideHeader($slide, string $title, OkrWeeklyReport $report): void
    {
        $this->accentLine($slide, 72, 52, 76, self::BLUE);
        $this->text($slide, $title, 72, 76, 900, 58, 28, self::NAVY, true);
        $this->text($slide, $report->unit->name, 970, 82, 170, 24, 12, self::MUTED, true, 0, Alignment::HORIZONTAL_RIGHT);
    }

    private function slideFooter($slide, int $number): void
    {
        $this->text($slide, 'SMK Telkom Lampung', 72, 672, 300, 18, 9, 'FF94A3B8', true);
        $this->text($slide, (string) $number, 1100, 672, 40, 18, 9, 'FF94A3B8', true, 0, Alignment::HORIZONTAL_RIGHT);
    }

    private function progressBar($slide, int $x, int $y, int $width, float $progress, string $color): void
    {
        $background = $slide->createRichTextShape()->setOffsetX($x)->setOffsetY($y)->setWidth($width)->setHeight(12);
        $background->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFE2E8F0'));
        $background->getBorder()->setLineWidth(0);

        if ($progress > 0) {
            $fill = $slide->createRichTextShape()->setOffsetX($x)->setOffsetY($y)->setWidth((int) round($width * ($progress / 100)))->setHeight(12);
            $fill->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($color));
            $fill->getBorder()->setLineWidth(0);
        }
    }

    private function background($slide, string $color): void
    {
        $slide->setBackground((new BackgroundColor)->setColor(new Color($color)));
    }

    private function accentLine($slide, int $x, int $y, int $width, string $color): void
    {
        $shape = $slide->createRichTextShape()->setOffsetX($x)->setOffsetY($y)->setWidth($width)->setHeight(7);
        $shape->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($color));
        $shape->getBorder()->setLineWidth(0);
    }

    private function text(
        $slide,
        string $value,
        int $x,
        int $y,
        int $width,
        int $height,
        int $size,
        string $color,
        bool $bold = false,
        float $spacing = 0,
        string $alignment = Alignment::HORIZONTAL_LEFT
    ): RichText {
        $shape = $slide->createRichTextShape()
            ->setOffsetX($x)
            ->setOffsetY($y)
            ->setWidth($width)
            ->setHeight($height);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal($alignment);
        $run = $shape->createTextRun($value);
        $run->getFont()->setName('Aptos')->setSize($size)->setBold($bold)->setColor(new Color($color));

        return $shape;
    }

    private function logo($slide, int $x, int $y, int $height): void
    {
        $path = public_path('images/asset-report/smk-telkom-lampung-white.png');
        if (! is_file($path)) {
            return;
        }

        $slide->createDrawingShape()
            ->setName('Logo SMK Telkom Lampung')
            ->setPath($path)
            ->setHeight($height)
            ->setOffsetX($x)
            ->setOffsetY($y);
    }
}
