<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Exports\ProjectExcelExport;
use App\Exports\ProjectPdfExport;
use App\Filament\Resources\ProjectResource\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\ProjectExpensesChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectPaymentsChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatsOverview;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label(__('app.actions.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(fn () => ProjectPdfExport::download($this->record)),

            Action::make('exportExcel')
                ->label(__('app.actions.export_excel'))
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action(fn () => ProjectExcelExport::download($this->record)),

            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProjectStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ProjectPaymentsChart::class,
            ProjectExpensesChart::class,
        ];
    }
}
