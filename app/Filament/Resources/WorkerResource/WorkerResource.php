<?php

namespace App\Filament\Resources\WorkerResource;

use App\Filament\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\WorkerResource\Pages\CreateWorker;
use App\Filament\Resources\WorkerResource\Pages\EditWorker;
use App\Filament\Resources\WorkerResource\Pages\ListWorkers;
use App\Filament\Resources\WorkerResource\Pages\ViewWorker;
use App\Filament\Resources\WorkerResource\RelationManagers\TasksRelationManager;
use App\Filament\Resources\WorkerResource\Schemas\WorkerForm;
use App\Filament\Resources\WorkerResource\Schemas\WorkerInfolist;
use App\Filament\Resources\WorkerResource\Tables\WorkersTable;
use App\Filament\Resources\WorkerResource\Widgets\AllWorkersChart;
use App\Filament\Resources\WorkerResource\Widgets\AllWorkersStatsOverview;
use App\Filament\Resources\WorkerResource\Widgets\WorkerPaymentsChart;
use App\Filament\Resources\WorkerResource\Widgets\WorkerStatsOverview;
use App\Models\Worker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static string | \UnitEnum | null $navigationGroup = 'HR Management';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string { return __('app.nav_groups.hr_management'); }
    public static function getModelLabel(): string { return __('app.resources.worker.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.worker.plural'); }

    public static function form(Schema $schema): Schema
    {
        return WorkerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
            PaymentsRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            WorkerStatsOverview::class,
            WorkerPaymentsChart::class,
            AllWorkersStatsOverview::class,
            AllWorkersChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkers::route('/'),
            'create' => CreateWorker::route('/create'),
            'view' => ViewWorker::route('/{record}'),
            'edit' => EditWorker::route('/{record}/edit'),
        ];
    }
}
