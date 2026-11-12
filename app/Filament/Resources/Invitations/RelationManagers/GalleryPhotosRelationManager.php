<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GalleryPhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'galleryPhotos';

    protected static ?string $title = 'Galeri Foto (Maks. 12 tampil)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('path')
                    ->label('Berkas Foto')
                    ->disk('public')
                    ->directory('gallery')
                    ->image()
                    ->maxSize(5120)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->label('Keterangan (Opsional)')
                    ->maxLength(200),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('path')->label('Foto')->disk('public'),
                TextColumn::make('caption')->label('Keterangan')->searchable(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Foto'),
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
