<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class RsvpsRelationManager extends RelationManager
{
    protected static string $relationship = 'rsvps';

    protected static ?string $title = 'Konfirmasi Kehadiran (RSVP)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label('Nama')->required()->maxLength(120),
                Toggle::make('attending')->label('Hadir'),
                TextInput::make('pax')->label('Jumlah Tamu')->numeric()->minValue(0)->maxValue(20),
                Textarea::make('message')->label('Pesan')->rows(3)->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('guest.code')->label('Kode Tamu')->badge()->placeholder('Umum'),
                IconColumn::make('attending')->label('Hadir')->boolean(),
                TextColumn::make('pax')->label('Jumlah')->sortable(),
                TextColumn::make('message')->label('Pesan')->limit(45),
                TextColumn::make('updated_at')->label('Diperbarui')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('attending')->label('Status Kehadiran'),
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
