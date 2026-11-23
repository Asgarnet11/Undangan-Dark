<?php

namespace App\Filament\Resources\AdatEventTypes\Tables;

use App\Models\AdatEventType;
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

class AdatEventTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('label_display')
                    ->label('Nama Prosesi Adat')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('key')
                    ->label('Kunci')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status Verifikasi')
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
                TextColumn::make('verified_by')
                    ->label('Pengecek Adat')
                    ->placeholder('Belum diverifikasi')
                    ->limit(25),
                TextColumn::make('verified_at')
                    ->label('Tanggal Verifikasi')
                    ->dateTime('d M Y')
                    ->placeholder('-'),
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
                    ->visible(fn (AdatEventType $record): bool => $record->status !== 'diverifikasi')
                    ->modalHeading(fn (AdatEventType $record): string => 'Verifikasi Prosesi Adat: '.$record->label_display)
                    ->modalDescription('Pastikan entri ini telah ditinjau dan disetujui minimal dua orang pengecek adat/budayawan.')
                    ->modalSubmitActionLabel('Sahkan Verifikasi')
                    ->form([
                        TextInput::make('verified_by')
                            ->label('Nama Pengecek Adat / Saksi')
                            ->placeholder('Contoh: Puang Baso & Andi Tenri')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data, AdatEventType $record): void {
                        $record->update([
                            'status' => 'diverifikasi',
                            'verified_by' => $data['verified_by'],
                            'verified_at' => now(),
                            'is_published' => true,
                        ]);

                        Notification::make()
                            ->title('Entri berhasil diverifikasi dan dipublikasikan')
                            ->success()
                            ->send();
                    }),
                Action::make('revoke')
                    ->label('Tarik ke Draf')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Kembalikan Status ke Draf?')
                    ->modalDescription('Entri ini tidak akan lagi tampil di halaman publik sampai diverifikasi ulang.')
                    ->visible(fn (AdatEventType $record): bool => $record->status === 'diverifikasi')
                    ->action(function (AdatEventType $record): void {
                        $record->update([
                            'status' => 'draf',
                            'is_published' => false,
                        ]);

                        Notification::make()
                            ->title('Status entri dikembalikan ke draf')
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
