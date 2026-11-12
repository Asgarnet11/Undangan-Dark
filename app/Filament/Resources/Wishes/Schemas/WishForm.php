<?php

namespace App\Filament\Resources\Wishes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WishForm
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
                    ->label('Tamu Terkait (Opsional)')
                    ->relationship('guest', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->label('Nama Pengirim')
                    ->required()
                    ->maxLength(100),
                TextInput::make('origin')
                    ->label('Domisili')
                    ->maxLength(100),
                Textarea::make('message')
                    ->label('Ucapan dan Doa')
                    ->required()
                    ->maxLength(500)
                    ->rows(4)
                    ->columnSpanFull(),
                Toggle::make('is_visible')
                    ->label('Tampilkan di Halaman Publik')
                    ->default(true),
            ]);
    }
}
