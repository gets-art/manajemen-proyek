<?php

namespace App\Exports;

use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectPdfExport
{
    public static function download(Project $project)
    {
        $project->load(['client', 'category', 'tasks.category', 'expenses.expenseCategory', 'expenses.paymentMethod', 'payments.paymentMethod']);

        $pdf = Pdf::loadView('exports.project-pdf', [
            'project' => $project,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'project-' . $project->id . '.pdf'
        );
    }
}
