<?php

namespace App\Filament\Resources\AdatEventTypes\Pages;

use App\Filament\Resources\AdatEventTypes\AdatEventTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdatEventType extends EditRecord
{
    protected static string $resource = AdatEventTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
