<?php

namespace App\Filament\Resources\AdatEventTypes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdatEventTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label('ID Kunci (Key)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(64)
                    ->helperText('Identitas teknis unik (contoh: akad_nikah, mappacci).'),
                TextInput::make('label_display')
                    ->label('Ejaan Baku / Label')
                    ->required()
                    ->maxLength(128)
                    ->helperText('Nama resmi prosesi sesuai Kamus Konten Adat (contoh: Mappacci).'),
                TagsInput::make('alt_spellings')
                    ->label('Varian Ejaan / Alias')
                    ->placeholder('Tambah alias ejaan')
                    ->helperText('Varian ejaan daerah lain (contoh: mappaci, tudang penni).'),
                Textarea::make('description_id')
                    ->label('Penjelasan Singkat')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Deskripsi netral dan santun mengenai prosesi ini.'),
                Textarea::make('regional_note')
                    ->label('Catatan Variasi Daerah')
                    ->rows(2)
                    ->columnSpanFull()
                    ->helperText('Catatan bila tata cara atau penamaannya bervariasi antar daerah Bugis.'),
                Select::make('status')
                    ->label('Status Verifikasi Budaya')
                    ->options([
                        'draf' => 'Draf (Belum Diverifikasi)',
                        'diverifikasi' => 'Diverifikasi (Disetujui Budayawan)',
                        'ditolak' => 'Ditolak (Tidak Layak Tampil)',
                    ])
                    ->default('draf')
                    ->required()
                    ->helperText('Hanya entri berstatus Diverifikasi yang dapat ditampilkan di undangan publik.'),
                Toggle::make('is_published')
                    ->label('Terbitkan ke Publik')
                    ->helperText('Aktifkan agar dapat dipilih dan dibaca oleh tamu undangan.')
                    ->default(false),
                TextInput::make('verified_by')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('Nama minimal 2 budayawan/pengecek adat')
                    ->maxLength(255),
                DateTimePicker::make('verified_at')
                    ->label('Waktu Verifikasi'),
                TextInput::make('sort_order')
                    ->label('Urutan Tampilan')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
