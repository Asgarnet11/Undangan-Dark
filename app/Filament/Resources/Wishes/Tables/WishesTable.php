<?php

namespace App\Filament\Resources\Wishes\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class WishesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('invitation.title')
                    ->label('Undangan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('origin')
                    ->label('Domisili')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('message')
                    ->label('Ucapan')
                    ->limit(60)
                    ->wrap(),
                ToggleColumn::make('is_visible')
                    ->label('Tampil'),
                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('invitation_id')
                    ->label('Undangan')
                    ->relationship('invitation', 'title'),
                TernaryFilter::make('is_visible')
                    ->label('Status Tampil'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('hide_selected')
                        ->label('Sembunyikan Terpilih')
                        ->action(fn (Collection $records) => $records->each->update(['is_visible' => false])),
                    BulkAction::make('show_selected')
                        ->label('Tampilkan Terpilih')
                        ->action(fn (Collection $records) => $records->each->update(['is_visible' => true])),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
