<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GiftRegistryLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'giftRegistryLinks';

    protected static ?string $title = 'Daftar Kado (Gift Registry)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Nama / Label Tautan')
                    ->placeholder('Contoh: Daftar Kado di Wegiftry')
                    ->required()
                    ->maxLength(120),
                TextInput::make('url')
                    ->label('Tautan URL (Wajib https://)')
                    ->placeholder('https://...')
                    ->url()
                    ->required()
                    ->maxLength(500)
                    ->startsWith(['https://'])
                    ->validationMessages([
                        'starts_with' => 'Tautan daftar kado wajib diawali dengan https://.',
                    ]),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('URL')
                    ->limit(45)
                    ->url(fn ($record): string => $record->url, shouldOpenInNewTab: true),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Daftar Kado'),
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
