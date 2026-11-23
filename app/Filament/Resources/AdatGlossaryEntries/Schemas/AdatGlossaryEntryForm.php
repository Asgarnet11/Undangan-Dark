<?php

namespace App\Filament\Resources\AdatGlossaryEntries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdatGlossaryEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('term')
                    ->label('Istilah Adat')
                    ->required()
                    ->maxLength(128)
                    ->helperText('Contoh: Sulapa Eppa, Walasuji, Lipa\' Sabbe.'),
                TextInput::make('latin_reading')
                    ->label('Bacaan Latin')
                    ->required()
                    ->maxLength(128)
                    ->helperText('Pelafalan teks Latin resmi.'),
                TextInput::make('lontara_text')
                    ->label('Aksara Lontara (Unicode)')
                    ->maxLength(128)
                    ->helperText('Teks aksara Bugis Lontara asli (contoh: ᨔᨘᨒᨄ ᨕᨙᨄ). Dilarang transliterasi otomatis tanpa verifikasi.'),
                Textarea::make('meaning_id')
                    ->label('Makna / Arti Istilah')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Penjelasan singkat dan netral sesuai literatur dan konfirmasi budayawan.'),
                Textarea::make('regional_note')
                    ->label('Catatan Variasi Daerah')
                    ->rows(2)
                    ->columnSpanFull()
                    ->helperText('Tuliskan jika terdapat perbedaan makna atau penafsiran antar daerah (Bone, Wajo, Soppeng, dll).'),
                Textarea::make('source_note')
                    ->label('Catatan Rujukan / Literatur')
                    ->rows(2)
                    ->columnSpanFull()
                    ->helperText('Bahan pustaka rujukan awal.'),
                Select::make('status')
                    ->label('Status Verifikasi')
                    ->options([
                        'draf' => 'Draf (Belum Diverifikasi)',
                        'diverifikasi' => 'Diverifikasi (Disetujui Budayawan)',
                        'ditolak' => 'Ditolak (Tidak Layak Tampil)',
                    ])
                    ->default('draf')
                    ->required(),
                Toggle::make('is_published')
                    ->label('Terbitkan ke Publik')
                    ->default(false)
                    ->helperText('Hanya aktif dan tampil pada modal glosarium bila berstatus diverifikasi.'),
                TextInput::make('verified_by')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('Nama minimal 2 budayawan/pengecek adat')
                    ->maxLength(255),
                DateTimePicker::make('verified_at')
                    ->label('Waktu Verifikasi'),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
