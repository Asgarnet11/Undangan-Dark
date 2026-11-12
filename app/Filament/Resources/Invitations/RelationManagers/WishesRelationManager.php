<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class WishesRelationManager extends RelationManager
{
    protected static string $relationship = 'wishes';

    protected static ?string $title = 'Moderasi Ucapan & Doa';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama')->required()->maxLength(100),
                TextInput::make('origin')->label('Domisili')->maxLength(100),
                Textarea::make('message')->label('Ucapan')->required()->maxLength(500)->rows(3)->columnSpanFull(),
                Toggle::make('is_visible')->label('Tampilkan di Halaman Publik')->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('origin')->label('Domisili')->searchable(),
                TextColumn::make('message')->label('Ucapan')->limit(55)->wrap(),
                ToggleColumn::make('is_visible')->label('Tampil'),
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_visible')->label('Status Tampil'),
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
