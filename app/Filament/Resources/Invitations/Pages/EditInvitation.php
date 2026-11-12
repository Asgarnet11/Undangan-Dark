<?php

namespace App\Filament\Resources\Invitations\Pages;

use App\Filament\Resources\Invitations\InvitationResource;
use App\Support\ThemeRegistry;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvitation extends EditRecord
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview_public')
                ->label('Buka Undangan')
                ->url(fn (): string => route('invitation.show', ['slug' => $this->record->slug]), shouldOpenInNewTab: true),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['theme_config'] = ThemeRegistry::resolveConfig(
            (string) ($data['theme'] ?? 'dark-boho'),
            is_array($data['theme_config'] ?? null) ? $data['theme_config'] : []
        );

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $theme = (string) ($data['theme'] ?? 'dark-boho');
        $config = is_array($data['theme_config'] ?? null) ? $data['theme_config'] : [];

        if (isset($config['cover']['overlayOpacity'])) {
            $config['cover']['overlayOpacity'] = (float) $config['cover']['overlayOpacity'];
        }

        $config['theme'] = $theme;
        $data['theme_config'] = ThemeRegistry::resolveConfig($theme, $config);

        return $data;
    }
}
