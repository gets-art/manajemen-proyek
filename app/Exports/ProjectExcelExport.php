<?php

namespace App\Exports;

use App\Models\Project;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProjectExcelExport
{
    public static function download(Project $project)
    {
        $project->load(['client', 'category', 'tasks.category', 'expenses.expenseCategory', 'expenses.paymentMethod', 'payments.paymentMethod']);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        self::addDetailsSheet($spreadsheet, $project);
        self::addTasksSheet($spreadsheet, $project);
        self::addExpensesSheet($spreadsheet, $project);
        self::addPaymentsSheet($spreadsheet, $project);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            'project-' . $project->id . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    protected static function addDetailsSheet(Spreadsheet $spreadsheet, Project $project): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Details');

        // Header
        $sheet->setCellValue('A1', 'Reflect Construction System');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A2', 'Project Report: ' . $project->name);
        $sheet->mergeCells('A2:B2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue('A3', 'Generated: ' . now()->format('Y-m-d H:i'));
        $sheet->mergeCells('A3:B3');

        $row = 5;
        $details = [
            'Project Name' => $project->name,
            'Client' => $project->client?->name ?? '—',
            'Category' => $project->category?->name ?? '—',
            'Status' => match ($project->status) {
                0 => 'Pending', 1 => 'In Progress', 2 => 'Completed', 3 => 'Cancelled', default => 'Unknown'
            },
            'Start Date' => $project->start_date ?? '—',
            'End Date' => $project->end_date ?? '—',
            'Final Total' => number_format((float) $project->final_total, 2) . ' IDR',
            'Paid Total' => number_format((float) $project->paid_total, 2) . ' IDR',
            'Remaining' => number_format((float) $project->rest_total, 2) . ' IDR',
            'Observation' => number_format((float) $project->observation, 2) . ' IDR',
        ];

        foreach ($details as $label => $value) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $value);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
    }

    protected static function addTasksSheet(Spreadsheet $spreadsheet, Project $project): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Tasks');

        $headers = ['#', 'Name', 'Category', 'Final Total', 'Paid Total', 'Remaining', 'Start Date', 'End Date'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $header);
        }
        self::styleHeaderRow($sheet, 'A1', chr(65 + count($headers) - 1) . '1');

        $row = 2;
        foreach ($project->tasks as $index => $task) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $task->name);
            $sheet->setCellValue("C{$row}", $task->category?->name ?? '—');
            $sheet->setCellValue("D{$row}", number_format((float) $task->final_total, 2));
            $sheet->setCellValue("E{$row}", number_format((float) $task->paid_total, 2));
            $sheet->setCellValue("F{$row}", number_format((float) $task->rest_total, 2));
            $sheet->setCellValue("G{$row}", $task->start_date ?? '—');
            $sheet->setCellValue("H{$row}", $task->end_date ?? '—');
            $row++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    protected static function addExpensesSheet(Spreadsheet $spreadsheet, Project $project): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Expenses');

        $headers = ['#', 'Name', 'Category', 'Payment Method', 'Value', 'Date'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $header);
        }
        self::styleHeaderRow($sheet, 'A1', chr(65 + count($headers) - 1) . '1');

        $row = 2;
        foreach ($project->expenses as $index => $expense) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $expense->name);
            $sheet->setCellValue("C{$row}", $expense->expenseCategory?->name ?? '—');
            $sheet->setCellValue("D{$row}", $expense->paymentMethod?->name ?? '—');
            $sheet->setCellValue("E{$row}", number_format((float) $expense->value, 2));
            $sheet->setCellValue("F{$row}", $expense->date ?? '—');
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    protected static function addPaymentsSheet(Spreadsheet $spreadsheet, Project $project): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Payments');

        $headers = ['#', 'Amount', 'Payment Method', 'Code', 'Date'];
        foreach ($headers as $i => $header) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $header);
        }
        self::styleHeaderRow($sheet, 'A1', chr(65 + count($headers) - 1) . '1');

        $row = 2;
        foreach ($project->payments as $index => $payment) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", number_format((float) $payment->paid, 2));
            $sheet->setCellValue("C{$row}", $payment->paymentMethod?->name ?? '—');
            $sheet->setCellValue("D{$row}", $payment->payment_code ?? '—');
            $sheet->setCellValue("E{$row}", $payment->created_at?->format('Y-m-d') ?? '—');
            $row++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    protected static function styleHeaderRow($sheet, string $from, string $to): void
    {
        $sheet->getStyle("{$from}:{$to}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
    }
}
