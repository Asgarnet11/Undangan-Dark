<?php

namespace App\Filament\Resources\AdatGlossaryEntries\Tables;

use App\Models\AdatGlossaryEntry;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AdatGlossaryEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('term')
                    ->label('Istilah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('lontara_text')
                    ->label('Lontara')
                    ->fontFamily('serif')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'diverifikasi' => 'success',
                        'ditolak' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diverifikasi' => 'Diverifikasi',
                        'ditolak' => 'Ditolak',
                        default => 'Draf',
                    }),
                IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean(),
                TextColumn::make('meaning_id')
                    ->label('Penjelasan')
                    ->limit(40)
                    ->wrap(),
                TextColumn::make('verified_by')
                    ->label('Pengecek Adat')
                    ->placeholder('Belum diverifikasi')
                    ->limit(25),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'draf' => 'Draf',
                        'diverifikasi' => 'Diverifikasi',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (AdatGlossaryEntry $record): bool => $record->status !== 'diverifikasi')
                    ->modalHeading(fn (AdatGlossaryEntry $record): string => 'Verifikasi Istilah: '.$record->term)
                    ->modalDescription('Pastikan istilah dan arti ini telah disetujui minimal dua budayawan/pengecek adat.')
                    ->modalSubmitActionLabel('Sahkan Verifikasi')
                    ->form([
                        TextInput::make('verified_by')
                            ->label('Nama Pengecek Adat / Saksi')
                            ->placeholder('Contoh: Puang Baso & Andi Tenri')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data, AdatGlossaryEntry $record): void {
                        $record->update([
                            'status' => 'diverifikasi',
                            'verified_by' => $data['verified_by'],
                            'verified_at' => now(),
                            'is_published' => true,
                        ]);

                        Notification::make()
                            ->title('Istilah berhasil diverifikasi dan dipublikasikan')
                            ->success()
                            ->send();
                    }),
                Action::make('revoke')
                    ->label('Tarik ke Draf')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Kembalikan Status ke Draf?')
                    ->modalDescription('Istilah ini tidak akan lagi tampil pada modal glosarium publik.')
                    ->visible(fn (AdatGlossaryEntry $record): bool => $record->status === 'diverifikasi')
                    ->action(function (AdatGlossaryEntry $record): void {
                        $record->update([
                            'status' => 'draf',
                            'is_published' => false,
                        ]);

                        Notification::make()
                            ->title('Status istilah dikembalikan ke draf')
                            ->warning()
                            ->send();
                    }),
                EditAction::make()
                    ->label('Kelola'),
                DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
