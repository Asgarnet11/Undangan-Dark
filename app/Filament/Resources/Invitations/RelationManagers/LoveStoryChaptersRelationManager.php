<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use App\Models\Invitation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoveStoryChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'loveStoryChapters';

    protected static ?string $title = 'Kisah Cinta';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Bab')
                    ->required()
                    ->maxLength(80),
                TextInput::make('time_label')
                    ->label('Label Waktu (Opsional)')
                    ->placeholder('Contoh: 2019 atau Maret 2024')
                    ->maxLength(60),
                Textarea::make('body')
                    ->label('Cerita Bab')
                    ->required()
                    ->maxLength(1200)
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('photo_path')
                    ->label('Foto Bab (Opsional, Maks. 2 MB)')
                    ->disk('public')
                    ->directory('love-story-photos')
                    ->image()
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->maxSize(2048)
                    ->columnSpanFull(),
                Toggle::make('is_visible')
                    ->label('Tampilkan Bab')
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
            ->recordTitleAttribute('title')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->description('Maksimal 8 bab Kisah Cinta per undangan.')
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('title')
                    ->label('Judul Bab')
                    ->searchable(),
                TextColumn::make('time_label')
                    ->label('Waktu')
                    ->badge()
                    ->placeholder('-'),
                IconColumn::make('is_visible')
                    ->label('Tampil')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Bab')
                    ->visible(function (): bool {
                        /** @var Invitation $invitation */
                        $invitation = $this->getOwnerRecord();

                        return $invitation->loveStoryChapters()->count() < 8;
                    }),
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
