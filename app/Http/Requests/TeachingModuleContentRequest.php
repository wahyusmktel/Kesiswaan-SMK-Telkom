<?php

namespace App\Http\Requests;

use App\Models\TeachingModule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use JsonException;

class TeachingModuleContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $module = $this->route('teachingModule');

        return $this->user() !== null
            && $module instanceof TeachingModule
            && $module->teacher_id === $this->user()->id;
    }

    protected function prepareForValidation(): void
    {
        $content = null;

        try {
            $content = json_decode(
                (string) $this->input('content_json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            // Validation below returns a readable error instead of an exception page.
        }

        $this->merge(['content' => $content]);
    }

    public function rules(): array
    {
        $listRules = ['nullable', 'string', 'max:20000'];

        return [
            'status' => ['required', Rule::in(['draft', 'complete'])],
            'content_json' => ['required', 'string', 'max:2000000'],
            'content' => ['required', 'array'],

            'content.identification' => ['required', 'array'],
            'content.identification.students' => ['required', 'array', 'min:1', 'max:50'],
            'content.identification.students.*' => $listRules,
            'content.identification.materials' => ['required', 'array', 'min:1', 'max:50'],
            'content.identification.materials.*' => $listRules,
            'content.identification.graduate_profile' => ['required', 'array', 'size:8'],
            'content.identification.graduate_profile.*.key' => ['required', 'string', 'max:50'],
            'content.identification.graduate_profile.*.selected' => ['required', 'boolean'],
            'content.identification.graduate_profile.*.note' => ['nullable', 'string', 'max:1000'],

            'content.design' => ['required', 'array'],
            'content.design.learning_outcomes' => ['required', 'array', 'min:1', 'max:50'],
            'content.design.learning_outcomes.*' => $listRules,
            'content.design.learning_objectives' => ['required', 'array', 'min:1', 'max:50'],
            'content.design.learning_objectives.*' => $listRules,
            'content.design.learning_topics' => ['required', 'array', 'min:1', 'max:50'],
            'content.design.learning_topics.*' => $listRules,
            'content.design.pedagogical_practices' => ['required', 'array', 'min:1', 'max:50'],
            'content.design.pedagogical_practices.*' => $listRules,
            'content.design.learning_partners' => ['required', 'array', 'min:1', 'max:50'],
            'content.design.learning_partners.*' => $listRules,
            'content.design.learning_environment' => ['required', 'array', 'min:1', 'max:50'],
            'content.design.learning_environment.*' => $listRules,
            'content.design.digital_use' => ['required', 'array', 'min:1', 'max:50'],
            'content.design.digital_use.*' => $listRules,

            'content.experiences' => ['required', 'array', 'min:1', 'max:20'],
            'content.experiences.*.title' => ['nullable', 'string', 'max:255'],
            'content.experiences.*.allocation' => ['required', 'string', 'max:50'],
            'content.experiences.*.opening' => ['required', 'array', 'min:1', 'max:50'],
            'content.experiences.*.opening.*' => $listRules,
            'content.experiences.*.core_phases' => ['required', 'array', 'min:1', 'max:20'],
            'content.experiences.*.core_phases.*.title' => ['required', 'string', 'max:255'],
            'content.experiences.*.core_phases.*.principles' => ['nullable', 'string', 'max:500'],
            'content.experiences.*.core_phases.*.teacher_activities' => ['required', 'array', 'min:1', 'max:50'],
            'content.experiences.*.core_phases.*.teacher_activities.*' => $listRules,
            'content.experiences.*.core_phases.*.student_activities' => ['required', 'array', 'min:1', 'max:50'],
            'content.experiences.*.core_phases.*.student_activities.*' => $listRules,
            'content.experiences.*.core_phases.*.outputs' => ['required', 'array', 'min:1', 'max:50'],
            'content.experiences.*.core_phases.*.outputs.*' => $listRules,
            'content.experiences.*.closing' => ['required', 'array', 'min:1', 'max:50'],
            'content.experiences.*.closing.*' => $listRules,

            'content.assessment' => ['required', 'array'],
            'content.assessment.initial' => ['required', 'array', 'min:1', 'max:50'],
            'content.assessment.initial.*' => $listRules,
            'content.assessment.process' => ['required', 'array', 'min:1', 'max:50'],
            'content.assessment.process.*' => $listRules,
            'content.assessment.final' => ['required', 'array', 'min:1', 'max:50'],
            'content.assessment.final.*' => $listRules,
            'content.assessment.criteria' => ['required', 'array', 'min:1', 'max:50'],
            'content.assessment.criteria.*' => $listRules,

            'content.supporting.trigger_questions' => ['required', 'array', 'min:1', 'max:50'],
            'content.supporting.trigger_questions.*' => $listRules,
            'content.supporting.differentiation' => ['required', 'array', 'min:1', 'max:50'],
            'content.supporting.differentiation.*' => $listRules,
            'content.supporting.enrichment' => ['required', 'array', 'min:1', 'max:50'],
            'content.supporting.enrichment.*' => $listRules,
            'content.supporting.remedial' => ['required', 'array', 'min:1', 'max:50'],
            'content.supporting.remedial.*' => $listRules,

            'content.attachments.teaching_materials' => ['required', 'array', 'min:1', 'max:50'],
            'content.attachments.teaching_materials.*' => $listRules,
            'content.attachments.worksheets' => ['required', 'array', 'min:1', 'max:50'],
            'content.attachments.worksheets.*' => $listRules,
            'content.attachments.assessments' => ['required', 'array', 'min:1', 'max:50'],
            'content.attachments.assessments.*' => $listRules,

            'content.approval.location' => ['required', 'string', 'max:100'],
            'content.approval.date' => ['required', 'date_format:Y-m-d'],
            'content.approval.validator_title' => ['required', 'string', 'max:255'],
            'content.approval.validator_name' => ['nullable', 'string', 'max:255'],
            'content.approval.validator_nip' => ['nullable', 'string', 'max:100'],
            'content.approval.teacher_title' => ['required', 'string', 'max:255'],
            'content.approval.teacher_name' => ['required', 'string', 'max:255'],
            'content.approval.teacher_nip' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->input('status') !== 'complete' || ! is_array($this->input('content'))) {
                return;
            }

            $content = $this->input('content');

            if (! $this->hasText(data_get($content, 'identification.students'))
                || ! $this->hasText(data_get($content, 'identification.materials'))) {
                $validator->errors()->add('content_json', 'Lengkapi identifikasi peserta didik dan materi sebelum menandai modul sebagai lengkap.');
            }

            if (! $this->hasText(data_get($content, 'design.learning_outcomes'))
                || ! $this->hasText(data_get($content, 'design.learning_objectives'))) {
                $validator->errors()->add('content_json', 'Lengkapi capaian dan tujuan pembelajaran sebelum menandai modul sebagai lengkap.');
            }

            if (! $this->hasLearningExperience(data_get($content, 'experiences', []))) {
                $validator->errors()->add('content_json', 'Isi sekurangnya satu aktivitas pada pengalaman belajar.');
            }

            if (! $this->hasText(data_get($content, 'assessment.initial'))
                && ! $this->hasText(data_get($content, 'assessment.process'))
                && ! $this->hasText(data_get($content, 'assessment.final'))) {
                $validator->errors()->add('content_json', 'Isi sekurangnya satu bentuk asesmen pembelajaran.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'content_json.required' => 'Isi modul tidak ditemukan. Muat ulang halaman lalu coba simpan kembali.',
            'content.required' => 'Isi modul tidak dapat dibaca. Pastikan halaman selesai dimuat sebelum menyimpan.',
            'content_json.max' => 'Isi modul terlalu besar. Kurangi panjang atau jumlah subbagian.',
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute melebihi batas yang diperbolehkan.',
        ];
    }

    private function hasLearningExperience(mixed $experiences): bool
    {
        if (! is_array($experiences)) {
            return false;
        }

        foreach ($experiences as $meeting) {
            if (! is_array($meeting)) {
                continue;
            }

            if ($this->hasText($meeting['opening'] ?? null) || $this->hasText($meeting['closing'] ?? null)) {
                return true;
            }

            foreach (($meeting['core_phases'] ?? []) as $phase) {
                if (is_array($phase) && (
                    $this->hasText($phase['teacher_activities'] ?? null)
                    || $this->hasText($phase['student_activities'] ?? null)
                    || $this->hasText($phase['outputs'] ?? null)
                )) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasText(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if ($this->hasText($item)) {
                return true;
            }
        }

        return false;
    }
}
