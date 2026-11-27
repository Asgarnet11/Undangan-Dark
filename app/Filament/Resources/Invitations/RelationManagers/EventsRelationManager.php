<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $title = 'Rangkaian Acara';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Nama Acara')
                    ->required()
                    ->maxLength(120),
                Select::make('adat_event_type_id')
                    ->label('Kategori Prosesi Adat (Opsional)')
                    ->relationship('adatEventType', 'label_display')
                    ->searchable()
                    ->preload()
                    ->helperText('Pilih jika acara ini merupakan bagian dari katalog prosesi adat Bugis.'),
                TextInput::make('display_label_override')
                    ->label('Keterangan Label Tampilan (Opsional)')
                    ->placeholder('Contoh: Mappacci (Malam Pacar)')
                    ->helperText('Kustomisasi nama tampilan acara yang tampil ke tamu undangan.'),
                DateTimePicker::make('starts_at')
                    ->label('Waktu Mulai')
                    ->seconds(false)
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->label('Waktu Selesai (Opsional)')
                    ->seconds(false)
                    ->afterOrEqual('starts_at')
                    ->validationMessages([
                        'after_or_equal' => 'Waktu selesai tidak boleh lebih awal dari waktu mulai.',
                    ]),
                TextInput::make('venue_name')
                    ->label('Nama Tempat')
                    ->required()
                    ->maxLength(160),
                Textarea::make('address')
                    ->label('Alamat Lengkap')
                    ->rows(2)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('maps_url')
                    ->label('Tautan Google Maps Khusus Acara (Opsional)')
                    ->url()
                    ->maxLength(500)
                    ->placeholder('https://maps.google.com/...')
                    ->helperText('Jika diisi, tombol Buka Peta acara ini mengarah langsung ke tautan ini.'),
                TextInput::make('maps_query')
                    ->label('Kueri Peta Google Maps (Opsional)')
                    ->maxLength(255),
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
            ->columns([
                TextColumn::make('title')->label('Nama Acara')->searchable(),
                TextColumn::make('adatEventType.label_display')
                    ->label('Kategori Adat')
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),
                TextColumn::make('starts_at')->label('Mulai')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('venue_name')->label('Tempat')->searchable(),
                TextColumn::make('sort_order')->label('Urutan')->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Acara'),
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
