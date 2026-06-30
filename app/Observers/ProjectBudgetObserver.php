<?php

namespace App\Observers;

use App\Models\ProjectBudget;

class ProjectBudgetObserver
{
    public function created(ProjectBudget $projectBudget): void
    {
        $this->recalculate($projectBudget);
    }

    public function updated(ProjectBudget $projectBudget): void
    {
        $this->recalculate($projectBudget);
    }

    public function deleted(ProjectBudget $projectBudget): void
    {
        $this->recalculate($projectBudget);
    }

    public function restored(ProjectBudget $projectBudget): void
    {
        $this->recalculate($projectBudget);
    }

    public function forceDeleted(ProjectBudget $projectBudget): void
    {
        $this->recalculate($projectBudget);
    }

    private function recalculate(ProjectBudget $projectBudget): void
    {
        if ($projectBudget->project && $projectBudget->project->is_rab_auto_calculated) {
            $projectBudget->project->recalculateFinalTotal();
        }
    }
}
