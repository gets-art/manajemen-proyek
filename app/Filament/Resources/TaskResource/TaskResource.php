<?php

namespace App\Filament\Resources\TaskResource;

use App\Filament\NavigationGroup;
use App\Filament\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\TaskResource\Pages\EditTask;
use App\Filament\Resources\TaskResource\Pages\ListTasks;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Filament\Resources\TaskResource\RelationManagers\PurchaseTasksRelationManager;
use App\Filament\Resources\TaskResource\RelationManagers\WorkersRelationManager;
use App\Filament\Resources\TaskResource\Schemas\TaskForm;
use App\Filament\Resources\TaskResource\Schemas\TaskInfolist;
use App\Filament\Resources\TaskResource\Tables\TasksTable;
use App\Models\Task;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::ProjectManagement;
    protected static ?int $navigationSort = 2;
    public static function getModelLabel(): string { return __('app.resources.task.label'); }
    public static function getPluralModelLabel(): string { return __('app.resources.task.plural'); }

    public static function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaskInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TasksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            WorkersRelationManager::class,
            PurchaseTasksRelationManager::class,
            ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'view' => ViewTask::route('/{record}'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}
