<?php

namespace App\Filament\Resources\Invitations\Pages;

use App\Filament\Resources\Invitations\InvitationResource;
use App\Support\ThemeRegistry;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Artisan;

class EditInvitation extends EditRecord
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview_draft')
                ->label('Pratinjau (Draft)')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn (): string => route('invitation.preview', ['invitation' => $this->record]), shouldOpenInNewTab: true),
            Action::make('toggle_publish')
                ->label(fn (): string => $this->record->is_published ? 'Nonaktifkan Undangan' : 'Publikasikan Undangan')
                ->icon(fn (): string => $this->record->is_published ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color(fn (): string => $this->record->is_published ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading(fn (): string => $this->record->is_published ? 'Nonaktifkan Undangan Publik?' : 'Publikasikan Undangan?')
                ->modalDescription(fn (): string => $this->record->is_published
                    ? 'Undangan tidak akan dapat diakses publik oleh tamu sampai diaktifkan kembali. Anda tetap dapat membukanya lewat Pratinjau (Draft).'
                    : 'Undangan akan dapat diakses publik oleh seluruh tamu undangan.')
                ->action(function (): void {
                    $newStatus = ! $this->record->is_published;
                    $this->record->update(['is_published' => $newStatus]);
                    Notification::make()
                        ->title($newStatus ? 'Undangan berhasil dipublikasikan' : 'Undangan berhasil dinonaktifkan')
                        ->success()
                        ->send();
                }),
            Action::make('preview_public')
                ->label('Buka Undangan')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->visible(fn (): bool => (bool) $this->record->is_published)
                ->url(fn (): string => route('invitation.show', ['slug' => $this->record->slug]), shouldOpenInNewTab: true),
            Action::make('backup_database')
                ->label('Backup DB')
                ->icon('heroicon-o-circle-stack')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Cadangkan Database Sekarang')
                ->modalDescription('Proses ini akan membuat salinan snapshot database lengkap dan menyimpannya di server.')
                ->action(function (): void {
                    Artisan::call('app:backup-database');
                    Notification::make()
                        ->title('Database berhasil dicadangkan')
                        ->success()
                        ->send();
                }),
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
