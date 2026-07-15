<?php

namespace App\Support;

use Illuminate\Support\Arr;

final class TeachingModuleSchema
{
    public const VERSION = 1;

    private const PROFILE_DIMENSIONS = [
        ['key' => 'faith', 'label' => 'Keimanan dan Ketakwaan terhadap Tuhan YME'],
        ['key' => 'creativity', 'label' => 'Kreativitas'],
        ['key' => 'health', 'label' => 'Kesehatan'],
        ['key' => 'citizenship', 'label' => 'Kewargaan'],
        ['key' => 'collaboration', 'label' => 'Kolaborasi'],
        ['key' => 'communication', 'label' => 'Komunikasi'],
        ['key' => 'critical_reasoning', 'label' => 'Penalaran Kritis'],
        ['key' => 'independence', 'label' => 'Kemandirian'],
    ];

    public static function defaults(array $context = []): array
    {
        $allocation = self::text($context['allocation'] ?? '4 JP', 50);

        return [
            'schema_version' => self::VERSION,
            'identification' => [
                'students' => [''],
                'materials' => [''],
                'graduate_profile' => array_map(
                    fn (array $dimension) => $dimension + ['selected' => false, 'note' => ''],
                    self::PROFILE_DIMENSIONS
                ),
            ],
            'design' => [
                'learning_outcomes' => [''],
                'learning_objectives' => [''],
                'learning_topics' => [''],
                'pedagogical_practices' => [''],
                'learning_partners' => [''],
                'learning_environment' => [''],
                'digital_use' => [''],
            ],
            'experiences' => [self::defaultMeeting($allocation)],
            'assessment' => [
                'initial' => [''],
                'process' => [''],
                'final' => [''],
                'criteria' => [''],
            ],
            'supporting' => [
                'trigger_questions' => [''],
                'differentiation' => [''],
                'enrichment' => [''],
                'remedial' => [''],
            ],
            'attachments' => [
                'teaching_materials' => [''],
                'worksheets' => [''],
                'assessments' => [''],
            ],
            'approval' => [
                'location' => self::text($context['location'] ?? 'Pringsewu', 100),
                'date' => self::text($context['date'] ?? now()->format('Y-m-d'), 20),
                'validator_title' => 'Wakil Kepala Sekolah Bidang Kurikulum',
                'validator_name' => '',
                'validator_nip' => '',
                'teacher_title' => 'Guru Mata Pelajaran',
                'teacher_name' => self::text($context['teacher_name'] ?? '', 255),
                'teacher_nip' => self::text($context['teacher_nip'] ?? '', 100),
            ],
        ];
    }

    public static function normalize(?array $content, array $context = []): array
    {
        if (empty($content)) {
            return self::defaults($context);
        }

        $defaults = self::defaults($context);
        $identification = Arr::get($content, 'identification', []);
        $design = Arr::get($content, 'design', []);
        $assessment = Arr::get($content, 'assessment', []);
        $supporting = Arr::get($content, 'supporting', []);
        $attachments = Arr::get($content, 'attachments', []);
        $approval = Arr::get($content, 'approval', []);

        return [
            'schema_version' => self::VERSION,
            'identification' => [
                'students' => self::stringList($identification['students'] ?? null),
                'materials' => self::stringList($identification['materials'] ?? null),
                'graduate_profile' => self::graduateProfile($identification['graduate_profile'] ?? []),
            ],
            'design' => [
                'learning_outcomes' => self::stringList($design['learning_outcomes'] ?? null),
                'learning_objectives' => self::stringList($design['learning_objectives'] ?? null),
                'learning_topics' => self::stringList($design['learning_topics'] ?? null),
                'pedagogical_practices' => self::stringList($design['pedagogical_practices'] ?? null),
                'learning_partners' => self::stringList($design['learning_partners'] ?? null),
                'learning_environment' => self::stringList($design['learning_environment'] ?? null),
                'digital_use' => self::stringList($design['digital_use'] ?? null),
            ],
            'experiences' => self::meetings(
                Arr::get($content, 'experiences', []),
                self::text($context['allocation'] ?? '4 JP', 50)
            ),
            'assessment' => [
                'initial' => self::stringList($assessment['initial'] ?? null),
                'process' => self::stringList($assessment['process'] ?? null),
                'final' => self::stringList($assessment['final'] ?? null),
                'criteria' => self::stringList($assessment['criteria'] ?? null),
            ],
            'supporting' => [
                'trigger_questions' => self::stringList($supporting['trigger_questions'] ?? null),
                'differentiation' => self::stringList($supporting['differentiation'] ?? null),
                'enrichment' => self::stringList($supporting['enrichment'] ?? null),
                'remedial' => self::stringList($supporting['remedial'] ?? null),
            ],
            'attachments' => [
                'teaching_materials' => self::stringList($attachments['teaching_materials'] ?? null),
                'worksheets' => self::stringList($attachments['worksheets'] ?? null),
                'assessments' => self::stringList($attachments['assessments'] ?? null),
            ],
            'approval' => [
                'location' => self::text($approval['location'] ?? $defaults['approval']['location'], 100),
                'date' => self::text($approval['date'] ?? $defaults['approval']['date'], 20),
                'validator_title' => self::text($approval['validator_title'] ?? $defaults['approval']['validator_title'], 255),
                'validator_name' => self::text($approval['validator_name'] ?? '', 255),
                'validator_nip' => self::text($approval['validator_nip'] ?? '', 100),
                'teacher_title' => self::text($approval['teacher_title'] ?? $defaults['approval']['teacher_title'], 255),
                'teacher_name' => self::text($approval['teacher_name'] ?? $defaults['approval']['teacher_name'], 255),
                'teacher_nip' => self::text($approval['teacher_nip'] ?? $defaults['approval']['teacher_nip'], 100),
            ],
        ];
    }

    public static function sanitize(array $content, array $context = []): array
    {
        return self::normalize($content, $context);
    }

    private static function defaultMeeting(string $allocation): array
    {
        return [
            'number' => 1,
            'title' => '',
            'allocation' => $allocation,
            'opening' => [''],
            'core_phases' => [
                self::defaultPhase('Orientasi Masalah', 'Bermakna dan berkesadaran'),
                self::defaultPhase('Mengorganisasi Peserta Didik untuk Belajar', 'Berkesadaran, bermakna, dan menggembirakan'),
                self::defaultPhase('Penyelidikan', 'Berkesadaran dan menggembirakan'),
                self::defaultPhase('Mengembangkan dan Menyajikan Hasil Karya', 'Berkesadaran dan menggembirakan'),
                self::defaultPhase('Analisis dan Evaluasi', 'Bermakna dan berkesadaran'),
            ],
            'closing' => [''],
        ];
    }

    private static function defaultPhase(string $title = '', string $principles = ''): array
    {
        return [
            'title' => $title,
            'principles' => $principles,
            'teacher_activities' => [''],
            'student_activities' => [''],
            'outputs' => [''],
        ];
    }

    private static function meetings(mixed $value, string $fallbackAllocation): array
    {
        if (! is_array($value) || $value === []) {
            return [self::defaultMeeting($fallbackAllocation)];
        }

        $meetings = [];
        foreach (array_slice($value, 0, 20) as $index => $meeting) {
            if (! is_array($meeting)) {
                continue;
            }

            $phases = [];
            $rawPhases = $meeting['core_phases'] ?? [];
            if (is_array($rawPhases)) {
                foreach (array_slice($rawPhases, 0, 20) as $phase) {
                    if (! is_array($phase)) {
                        continue;
                    }

                    $phases[] = [
                        'title' => self::text($phase['title'] ?? '', 255),
                        'principles' => self::text($phase['principles'] ?? '', 500),
                        'teacher_activities' => self::stringList($phase['teacher_activities'] ?? null),
                        'student_activities' => self::stringList($phase['student_activities'] ?? null),
                        'outputs' => self::stringList($phase['outputs'] ?? null),
                    ];
                }
            }

            if ($phases === []) {
                $phases[] = self::defaultPhase('Kegiatan Inti', 'Berkesadaran, bermakna, dan menggembirakan');
            }

            $meetings[] = [
                'number' => $index + 1,
                'title' => self::text($meeting['title'] ?? '', 255),
                'allocation' => self::text($meeting['allocation'] ?? $fallbackAllocation, 50),
                'opening' => self::stringList($meeting['opening'] ?? null),
                'core_phases' => $phases,
                'closing' => self::stringList($meeting['closing'] ?? null),
            ];
        }

        return $meetings === [] ? [self::defaultMeeting($fallbackAllocation)] : $meetings;
    }

    private static function graduateProfile(mixed $value): array
    {
        $items = is_array($value) ? $value : [];

        return array_map(function (array $dimension) use ($items) {
            $matched = collect($items)->first(
                fn ($item) => is_array($item) && ($item['key'] ?? null) === $dimension['key']
            );

            return $dimension + [
                'selected' => (bool) ($matched['selected'] ?? false),
                'note' => self::text($matched['note'] ?? '', 1000),
            ];
        }, self::PROFILE_DIMENSIONS);
    }

    private static function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [''];
        }

        $items = [];
        foreach (array_slice($value, 0, 50) as $item) {
            if (is_scalar($item) || $item === null) {
                $items[] = self::text($item, 20000);
            }
        }

        return $items === [] ? [''] : $items;
    }

    private static function text(mixed $value, int $maxLength): string
    {
        if (! is_scalar($value) && $value !== null) {
            return '';
        }

        return mb_substr(trim((string) $value), 0, $maxLength);
    }
}
