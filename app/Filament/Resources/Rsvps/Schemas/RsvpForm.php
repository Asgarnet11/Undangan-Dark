<?php

namespace App\Filament\Resources\Rsvps\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RsvpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invitation_id')
                    ->label('Undangan')
                    ->relationship('invitation', 'title')
                    ->required(),
                Select::make('guest_id')
                    ->label('Tamu Terdaftar (Opsional)')
                    ->relationship('guest', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(120),
                Toggle::make('attending')
                    ->label('Hadir')
                    ->default(true),
                TextInput::make('pax')
                    ->label('Jumlah Tamu')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(20)
                    ->default(1)
                    ->required(),
                Textarea::make('message')
                    ->label('Pesan Singkat')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
