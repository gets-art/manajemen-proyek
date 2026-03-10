<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource\WorkerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorker extends CreateRecord
{
    protected static string $resource = WorkerResource::class;

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('app.notifications.created', ['resource' => __('app.resources.worker.label')]);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
