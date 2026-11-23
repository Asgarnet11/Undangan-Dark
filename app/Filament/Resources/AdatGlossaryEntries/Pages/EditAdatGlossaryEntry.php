<?php

namespace App\Filament\Resources\AdatGlossaryEntries\Pages;

use App\Filament\Resources\AdatGlossaryEntries\AdatGlossaryEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAdatGlossaryEntry extends EditRecord
{
    protected static string $resource = AdatGlossaryEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
