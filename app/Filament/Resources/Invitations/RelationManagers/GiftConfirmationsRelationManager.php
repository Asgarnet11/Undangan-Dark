<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GiftConfirmationsRelationManager extends RelationManager
{
    protected static string $relationship = 'giftConfirmations';

    protected static ?string $title = 'Konfirmasi Tanda Kasih';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sender_name')
                    ->label('Nama Pengirim')
                    ->required()
                    ->maxLength(120),
                TextInput::make('target_account')
                    ->label('Tujuan Hadiah / Rekening')
                    ->maxLength(120),
                TextInput::make('amount')
                    ->label('Nominal (Opsional)')
                    ->maxLength(60),
                Textarea::make('notes')
                    ->label('Ucapan / Catatan')
                    ->rows(3)
                    ->columnSpanFull(),
                FileUpload::make('proof_file')
                    ->label('Bukti Transfer')
                    ->disk('public')
                    ->directory('gift-proofs')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sender_name')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('sender_name')
                    ->label('Nama Pengirim')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('target_account')
                    ->label('Tujuan Transfer / Bank')
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->placeholder('-'),
                ImageColumn::make('proof_file')
                    ->label('Bukti')
                    ->disk('public'),
                TextColumn::make('notes')
                    ->label('Ucapan / Catatan')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Waktu Konfirmasi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
