<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use App\Models\Payment;
use App\Models\Project;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    protected static ?string $title = null;

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('app.resources.payment.plural');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.payment_details'))
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('paymentable.name')->label(__('app.fields.project')),
                        TextEntry::make('paymentMethod.name')->label(__('app.fields.method')),
                        TextEntry::make('paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.amount')),
                        TextEntry::make('payment_code')->label(__('app.fields.code'))->placeholder('—'),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('paymentable_id')
                    ->label(__('app.fields.project'))
                    ->options(fn () => Project::where('client_id', $this->ownerRecord->id)->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('paid')
                    ->required()
                    ->numeric()
                    ->label(__('app.fields.amount')),
                TextInput::make('payment_code')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Payment::whereHasMorph('paymentable', [Project::class], function ($query) {
                $query->where('client_id', $this->ownerRecord->id);
            })->with(['paymentMethod', 'paymentable']))
            ->columns([
                TextColumn::make('paymentable.name')->label(__('app.fields.project')),
                TextColumn::make('paymentMethod.name')->label(__('app.fields.method')),
                TextColumn::make('paid')->formatStateUsing(fn ($state) => number_format((float) $state, 2) . ' EGP')->label(__('app.fields.amount')),
                TextColumn::make('payment_code')->label(__('app.fields.code'))->placeholder('—'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['paymentable_type'] = Project::class;
                        return $data;
                    }),
            ]);
    }
}
