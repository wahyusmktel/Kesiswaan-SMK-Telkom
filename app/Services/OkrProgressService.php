<?php

namespace App\Services;

use App\Models\OkrPlan;

class OkrProgressService
{
    public function syncFromValue(OkrPlan $plan): void
    {
        if ($plan->target_value !== null && (float) $plan->target_value > 0 && $plan->current_value !== null) {
            $plan->progress_percent = min(100, max(0, ((float) $plan->current_value / (float) $plan->target_value) * 100));
        }

        $plan->status = $this->statusFor((float) $plan->progress_percent, $plan->status);
        $plan->completed_at = $plan->status === 'completed' ? ($plan->completed_at ?? now()) : null;
        $plan->save();
        $this->rollUp($plan->parent);
    }

    public function rollUp(?OkrPlan $plan): void
    {
        if (! $plan) {
            return;
        }

        $children = $plan->children()->where('status', '!=', 'cancelled')->get();
        if ($children->isNotEmpty()) {
            $weight = max(0.01, (float) $children->sum('weight'));
            $plan->progress_percent = round(
                $children->sum(fn (OkrPlan $child) => (float) $child->progress_percent * max(0.01, (float) $child->weight)) / $weight,
                2
            );
            $plan->status = $this->statusFor((float) $plan->progress_percent, $plan->status);
            $plan->completed_at = $plan->status === 'completed' ? ($plan->completed_at ?? now()) : null;
            $plan->save();
        }

        $this->rollUp($plan->parent);
    }

    private function statusFor(float $progress, string $currentStatus): string
    {
        if ($currentStatus === 'cancelled') {
            return $currentStatus;
        }

        return match (true) {
            $progress >= 100 => 'completed',
            $currentStatus === 'at_risk' => 'at_risk',
            $progress > 0 => 'in_progress',
            default => 'not_started',
        };
    }
}
