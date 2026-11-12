<?php

namespace App\Filament\Resources\Invitations\Pages;

use App\Filament\Resources\Invitations\InvitationResource;
use App\Support\ThemeRegistry;
use Filament\Resources\Pages\CreateRecord;

class CreateInvitation extends CreateRecord
{
    protected static string $resource = InvitationResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
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
