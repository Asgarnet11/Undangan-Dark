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

class GiftAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'giftAccounts';

    protected static ?string $title = 'Rekening Hadiah';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('bank_name')
                    ->label('Nama Bank / Dompet Digital')
                    ->required()
                    ->maxLength(100),
                TextInput::make('account_number')
                    ->label('Nomor Rekening')
                    ->required()
                    ->maxLength(64),
                TextInput::make('account_name')
                    ->label('Atas Nama')
                    ->required()
                    ->maxLength(120),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bank_name')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('bank_name')->label('Bank')->searchable(),
                TextColumn::make('account_number')->label('Nomor Rekening')->copyable(),
                TextColumn::make('account_name')->label('Atas Nama'),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Rekening'),
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
