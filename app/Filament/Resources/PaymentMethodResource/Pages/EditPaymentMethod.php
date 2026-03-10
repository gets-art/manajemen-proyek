<?php

namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource\PaymentMethodResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentMethod extends EditRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotificationTitle(__('app.notifications.deleted', ['resource' => __('app.resources.payment_method.label')])),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('app.notifications.updated', ['resource' => __('app.resources.payment_method.label')]);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
