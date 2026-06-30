<?php

namespace App\Filament\Resources\ProjectResource;

use App\Filament\NavigationGroup;
use App\Filament\RelationManagers\ImagesRelationManager;
use App\Filament\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Filament\Resources\ProjectResource\Pages\ViewProject;
use App\Filament\Resources\ProjectResource\RelationManagers\ExpensesRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\ChangeOrdersRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\PaymentTermsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\ProjectBudgetsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\TasksRelationManager;
use App\Filament\Resources\ProjectResource\Schemas\ProjectForm;
use App\Filament\Resources\ProjectResource\Schemas\ProjectInfolist;
use App\Filament\Resources\ProjectResource\Tables\ProjectsTable;
use App\Filament\Resources\ProjectResource\Widgets\AllProjectsChart;
use App\Filament\Resources\ProjectResource\Widgets\AllProjectsStatsOverview;
use App\Filament\Resources\ProjectResource\Widgets\AllProjectsStatusChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectExpensesChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectPaymentsChart;
use App\Filament\Resources\ProjectResource\Widgets\ProjectStatsOverview;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-office-2';
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::ProjectManagement;
    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string { return __('app.resources.project.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.project.plural'); }

    public static function form(Schema $schema): Schema
    {
        return ProjectForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
            ProjectBudgetsRelationManager::class,
            ChangeOrdersRelationManager::class,
            PaymentTermsRelationManager::class,
            PaymentsRelationManager::class,
            ExpensesRelationManager::class,
            ImagesRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProjectStatsOverview::class,
            ProjectPaymentsChart::class,
            ProjectExpensesChart::class,
            AllProjectsStatsOverview::class,
            AllProjectsChart::class,
            AllProjectsStatusChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'view' => ViewProject::route('/{record}'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
