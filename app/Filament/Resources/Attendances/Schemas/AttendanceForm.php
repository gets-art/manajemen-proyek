<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('app.sections.attendance_info'))
                    ->columns(2)
                    ->schema([
                        Select::make('project_id')
                            ->label(__('app.fields.project'))
                            ->relationship('project', 'name')
                            ->searchable()
                            ->required(),
                        DatePicker::make('date')
                            ->label(__('app.fields.date'))
                            ->default(now())
                            ->required(),
                        FileUpload::make('image')
                            ->label(__('app.fields.image_proof'))
                            ->image()
                            ->directory('attendances')
                            ->openable()
                            ->required()
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Catatan Pekerjaan Hari Ini')
                            ->rows(3)
                            ->columnSpanFull(),
                        Select::make('mandor_id')
                            ->label('Kepala Tukang / Mandor')
                            ->options(
                                fn () => \App\Models\Worker::where('type', 'harian')->where('active', true)->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->live()
                            ->columnSpanFull(),
                        Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                    ]),
                Section::make(__('app.sections.workers_present'))
                    ->schema([
                        CheckboxList::make('workers')
                            ->label(__('app.fields.workers'))
                            ->relationship(
                                'workers', 
                                'name',
                                fn (\Illuminate\Database\Eloquent\Builder $query, $get) => 
                                    $query->where('type', 'harian')
                                          ->where('active', true)
                                          ->where('mandor_id', $get('mandor_id'))
                            )
                            ->columns(3)
                            ->bulkToggleable()
                            ->required()
                            ->disabled(fn ($get) => empty($get('mandor_id'))),
                    ]),
            ]);
    }
}
