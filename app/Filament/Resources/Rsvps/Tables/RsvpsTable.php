<?php

namespace App\Filament\Resources\Rsvps\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RsvpsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('invitation.title')
                    ->label('Undangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('guest.code')
                    ->label('Kode Tamu')
                    ->badge()
                    ->placeholder('Umum'),
                IconColumn::make('attending')
                    ->label('Hadir')
                    ->boolean(),
                TextColumn::make('pax')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('message')
                    ->label('Pesan')
                    ->limit(45),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('invitation_id')
                    ->label('Undangan')
                    ->relationship('invitation', 'title'),
                TernaryFilter::make('attending')
                    ->label('Status Kehadiran'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
