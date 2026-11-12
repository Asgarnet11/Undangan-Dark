<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use App\Models\Guest;
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
use Illuminate\Support\Str;

class GuestsRelationManager extends RelationManager
{
    protected static string $relationship = 'guests';

    protected static ?string $title = 'Daftar Tamu';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tamu')
                    ->required()
                    ->maxLength(160),
                TextInput::make('code')
                    ->label('Kode Unik (?to=KODE)')
                    ->default(fn (): string => strtoupper(Str::random(6)))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(32),
                TextInput::make('group')
                    ->label('Kategori / Grup')
                    ->maxLength(80),
                TextInput::make('max_pax')
                    ->label('Maksimal Tamu')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(20)
                    ->default(2)
                    ->required(),
                TextInput::make('phone')
                    ->label('Nomor WhatsApp (Opsional)')
                    ->maxLength(32),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Nama Tamu')->searchable(),
                TextColumn::make('code')->label('Kode')->badge()->copyable(),
                TextColumn::make('personal_url')
                    ->label('Tautan Undangan')
                    ->state(fn (Guest $record): string => route('invitation.show', ['slug' => $this->getOwnerRecord()->slug, 'to' => $record->code]))
                    ->copyable()
                    ->limit(36),
                TextColumn::make('group')->label('Grup')->searchable(),
                TextColumn::make('max_pax')->label('Maks. Tamu'),
                TextColumn::make('opened_at')->label('Dibuka')->dateTime('d M H:i')->placeholder('Belum'),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Tamu'),
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
