<?php

namespace App\Filament\Resources\AdatGlossaryEntries\Pages;

use App\Filament\Resources\AdatGlossaryEntries\AdatGlossaryEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdatGlossaryEntries extends ListRecords
{
    protected static string $resource = AdatGlossaryEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
