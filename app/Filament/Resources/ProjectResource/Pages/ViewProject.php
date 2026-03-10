<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Exports\ProjectExcelExport;
use App\Exports\ProjectPdfExport;
use App\Filament\Resources\ProjectResource\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\ProjectExpensesChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectPaymentsChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatsOverview;
use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addTask')
                ->label(__('app.actions.add_task'))
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->rows(2),
                    Select::make('category_id')
                        ->options(fn () => Category::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                    DatePicker::make('start_date')
                        ->native(false),
                    DatePicker::make('end_date')
                        ->native(false),
                    TextInput::make('final_total')
                        ->numeric()
                        ->prefix('EGP')
                        ->default(0),
                    TextInput::make('paid_total')
                        ->numeric()
                        ->prefix('EGP')
                        ->default(0),
                    TextInput::make('rest_total')
                        ->numeric()
                        ->prefix('EGP')
                        ->default(0),
                ])
                ->action(function (array $data): void {
                    $data['project_id'] = $this->record->id;
                    Task::create($data);
                }),

            Action::make('addExpense')
                ->label(__('app.actions.add_expense'))
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('description')
                        ->rows(2),
                    TextInput::make('value')
                        ->required()
                        ->numeric()
                        ->prefix('EGP'),
                    TextInput::make('date')
                        ->required()
                        ->maxLength(255),
                    Select::make('expense_category_id')
                        ->options(fn () => ExpenseCategory::pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                    Select::make('payment_method_id')
                        ->options(fn () => PaymentMethod::pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),
                ])
                ->action(function (array $data): void {
                    $data['project_id'] = $this->record->id;
                    $data['added_by'] = Auth::id();
                    $data['last_edit_by'] = Auth::id();
                    Expense::create($data);
                }),

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
