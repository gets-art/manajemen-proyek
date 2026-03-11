<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Filament\NavigationGroup;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.manage-settings';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string | \UnitEnum | null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 1;
    public static function getNavigationLabel(): string { return __('app.settings.nav_label'); }

    protected static ?string $title = 'App Settings';

    public function getTitle(): string { return __('app.settings.title'); }

    public ?array $data = [];

    public function mount(): void
    {
        $setting = Setting::first();

        if ($setting) {
            $this->form->fill($setting->toArray());
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn () => __('app.settings.sections.general'))
                    ->schema([
                        TextInput::make('app_name')
                            ->required()
                            ->maxLength(255)
                            ->label(fn () => __('app.settings.fields.app_name')),
                        TextInput::make('contact_email')
                            ->email()
                            ->maxLength(255)
                            ->label(fn () => __('app.settings.fields.contact_email')),
                        TextInput::make('contact_phone')
                            ->maxLength(255)
                            ->label(fn () => __('app.settings.fields.contact_phone')),
                        TextInput::make('default_lang')
                            ->maxLength(10)
                            ->label(fn () => __('app.settings.fields.default_lang')),
                    ])
                    ->columns(2),
                Section::make(fn () => __('app.settings.sections.social_media'))
                    ->schema([
                        TextInput::make('whatsapp')
                            ->maxLength(255)
                            ->label(fn () => __('app.settings.fields.whatsapp')),
                        TextInput::make('facebook')
                            ->maxLength(255)
                            ->label(fn () => __('app.settings.fields.facebook')),
                        TextInput::make('instagram')
                            ->maxLength(255)
                            ->label(fn () => __('app.settings.fields.instagram')),
                    ])
                    ->columns(2),
                Section::make(fn () => __('app.settings.sections.images'))
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->directory('settings')
                            ->label(fn () => __('app.settings.fields.logo')),
                        FileUpload::make('fav_ico')
                            ->image()
                            ->directory('settings')
                            ->label(fn () => __('app.settings.fields.favicon')),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(fn () => __('app.settings.actions.save'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = Setting::first();

        if ($setting) {
            $setting->update($data);
        } else {
            Setting::create($data);
        }

        Notification::make()
            ->title(__('app.settings.notifications.saved'))
            ->success()
            ->send();
    }
}
