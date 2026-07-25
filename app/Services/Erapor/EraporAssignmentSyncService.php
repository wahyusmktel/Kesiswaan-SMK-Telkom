<?php

namespace App\Services\Erapor;

use App\Models\Erapor\EraporSubjectMapping;
use App\Models\Erapor\EraporTeachingAssignment;
use App\Models\JadwalPelajaran;
use App\Models\TahunPelajaran;
use Illuminate\Support\Facades\DB;

class EraporAssignmentSyncService
{
    /**
     * @return array{created: int, updated: int, reactivated: int, deactivated: int, total: int}
     */
    public function sync(TahunPelajaran $academicPeriod): array
    {
        $scheduleGroups = JadwalPelajaran::query()
            ->join('rombels', 'rombels.id', '=', 'jadwal_pelajarans.rombel_id')
            ->where('rombels.tahun_pelajaran_id', $academicPeriod->id)
            ->select([
                'jadwal_pelajarans.rombel_id',
                'jadwal_pelajarans.mata_pelajaran_id',
                'jadwal_pelajarans.master_guru_id',
            ])
            ->selectRaw('COUNT(*) as slot_count')
            ->groupBy([
                'jadwal_pelajarans.rombel_id',
                'jadwal_pelajarans.mata_pelajaran_id',
                'jadwal_pelajarans.master_guru_id',
            ])
            ->get();

        $mappingIds = EraporSubjectMapping::query()
            ->whereIn('mata_pelajaran_id', $scheduleGroups->pluck('mata_pelajaran_id'))
            ->pluck('id', 'mata_pelajaran_id');

        $stats = ['created' => 0, 'updated' => 0, 'reactivated' => 0, 'deactivated' => 0, 'total' => 0];

        DB::transaction(function () use ($academicPeriod, $scheduleGroups, $mappingIds, &$stats) {
            $sourceKeys = [];

            foreach ($scheduleGroups as $group) {
                $sourceKey = hash('sha256', implode('|', [
                    'schedule',
                    $academicPeriod->id,
                    $group->rombel_id,
                    $group->mata_pelajaran_id,
                    $group->master_guru_id,
                ]));
                $sourceKeys[] = $sourceKey;

                $assignment = EraporTeachingAssignment::query()
                    ->where('source_key', $sourceKey)
                    ->first();

                if (! $assignment) {
                    $assignment = new EraporTeachingAssignment;
                    $assignment->source_key = $sourceKey;
                    $assignment->source = 'schedule';
                    $stats['created']++;
                } else {
                    $stats['updated']++;

                    if (! $assignment->is_active) {
                        $stats['reactivated']++;
                    }
                }

                $assignment->fill([
                    'tahun_pelajaran_id' => $academicPeriod->id,
                    'rombel_id' => $group->rombel_id,
                    'mata_pelajaran_id' => $group->mata_pelajaran_id,
                    'master_guru_id' => $group->master_guru_id,
                    'erapor_subject_mapping_id' => $mappingIds->get($group->mata_pelajaran_id),
                    'is_active' => true,
                    'last_synced_at' => now(),
                    'sync_metadata' => ['schedule_slot_count' => (int) $group->slot_count],
                ])->save();
            }

            $stale = EraporTeachingAssignment::query()
                ->where('tahun_pelajaran_id', $academicPeriod->id)
                ->where('source', 'schedule')
                ->where('is_active', true);

            if ($sourceKeys !== []) {
                $stale->whereNotIn('source_key', $sourceKeys);
            }

            $stats['deactivated'] = $stale->update([
                'is_active' => false,
                'last_synced_at' => now(),
            ]);
        });

        $stats['total'] = $scheduleGroups->count();

        return $stats;
    }
}
