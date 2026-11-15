<?php

namespace App\Filament\Resources\Invitations\Tables;

use App\Models\Invitation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Undangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('theme')
                    ->label('Tema')
                    ->badge(),
                TextColumn::make('timezone')
                    ->label('Zona Waktu'),
                IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('preview_draft')
                    ->label('Pratinjau')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (Invitation $record): string => route('invitation.preview', ['invitation' => $record]), shouldOpenInNewTab: true),
                Action::make('toggle_publish')
                    ->label(fn (Invitation $record): string => $record->is_published ? 'Nonaktifkan' : 'Publikasikan')
                    ->icon(fn (Invitation $record): string => $record->is_published ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Invitation $record): string => $record->is_published ? 'warning' : 'success')
                    ->action(function (Invitation $record): void {
                        $newStatus = ! $record->is_published;
                        $record->update(['is_published' => $newStatus]);
                        Notification::make()
                            ->title($newStatus ? 'Undangan dipublikasikan' : 'Undangan dinonaktifkan')
                            ->success()
                            ->send();
                    }),
                Action::make('open_public')
                    ->label('Lihat')
                    ->visible(fn (Invitation $record): bool => (bool) $record->is_published)
                    ->url(fn (Invitation $record): string => route('invitation.show', ['slug' => $record->slug]), shouldOpenInNewTab: true),
                EditAction::make()
                    ->label('Kelola'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
