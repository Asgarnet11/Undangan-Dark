<?php

namespace App\Filament\Resources\AdatEventTypes\Pages;

use App\Filament\Resources\AdatEventTypes\AdatEventTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdatEventTypes extends ListRecords
{
    protected static string $resource = AdatEventTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
