<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoInvitersRelationManager extends RelationManager
{
    protected static string $relationship = 'coInviters';

    protected static ?string $title = 'Turut Mengundang';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama / Keluarga')
                    ->required()
                    ->maxLength(160),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')->label('Nama / Keluarga')->searchable(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah'),
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
