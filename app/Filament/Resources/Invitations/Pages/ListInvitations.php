<?php

namespace App\Filament\Resources\Invitations\Pages;

use App\Filament\Resources\Invitations\InvitationResource;
use App\Support\DatabaseBackup;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvitations extends ListRecords
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_backup')
                ->label('Cadangan Database')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => DatabaseBackup::download()),
            CreateAction::make(),
        ];
    }
}
