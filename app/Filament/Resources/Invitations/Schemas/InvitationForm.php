<?php

namespace App\Filament\Resources\Invitations\Schemas;

use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        $presets = collect(ThemeRegistry::presets('dark-boho'))
            ->mapWithKeys(fn (array $preset, string $key): array => [$key => (string) ($preset['label'] ?? $key)])
            ->all();

        return $schema
            ->columns(1)
            ->components([
                Tabs::make('Pengaturan Undangan')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Tema & Pratinjau')
                            ->schema([
                                Grid::make(['default' => 1, 'lg' => 12])
                                    ->schema([
                                        Section::make('Kustomisasi Tampilan')
                                            ->columnSpan(['default' => 1, 'lg' => 7])
                                            ->schema([
                                                Grid::make(2)->schema([
                                                    Select::make('theme')
                                                        ->label('Tema')
                                                        ->options(ThemeRegistry::options())
                                                        ->default('dark-boho')
                                                        ->required()
                                                        ->live(),
                                                    Select::make('theme_config.preset')
                                                        ->label('Preset Warna')
                                                        ->options(function ($get) {
                                                            $theme = $get('theme') ?: 'dark-boho';

                                                            return collect(ThemeRegistry::presets($theme))
                                                                ->mapWithKeys(fn (array $preset, string $key): array => [$key => (string) ($preset['label'] ?? $key)])
                                                                ->all();
                                                        })
                                                        ->default(fn ($get) => ($get('theme') ?: 'dark-boho') === 'sunny-meadow' ? 'sunny' : 'terracotta')
                                                        ->required()
                                                        ->live(),
                                                ]),

                                                Section::make('Pengaturan Cover')
                                                    ->compact()
                                                    ->columns(2)
                                                    ->schema([
                                                        Select::make('theme_config.cover.style')
                                                            ->label('Gaya Cover')
                                                            ->options(function ($get) {
                                                                $theme = $get('theme') ?: 'dark-boho';
                                                                if ($theme === 'sunny-meadow') {
                                                                    return [
                                                                        'default' => 'Komposisi Tengah (Default)',
                                                                        'top' => 'Teks di Atas',
                                                                        'space-between' => 'Teks Terpisah Atas-Bawah',
                                                                        'frame' => 'Bingkai Bunga Ceria',
                                                                    ];
                                                                }

                                                                return [
                                                                    'default' => 'Klasik Terracotta',
                                                                    'new' => 'Bingkai Emas Modern',
                                                                ];
                                                            })
                                                            ->default('default')
                                                            ->live(),
                                                        TextInput::make('theme_config.cover.overlayOpacity')
                                                            ->label('Opasitas Overlay (0 - 0.95)')
                                                            ->numeric()
                                                            ->minValue(0)
                                                            ->maxValue(0.95)
                                                            ->step(0.05)
                                                            ->default(0.55)
                                                            ->live(onBlur: true),
                                                        ColorPicker::make('theme_config.cover.overlayColor')
                                                            ->label('Warna Overlay Cover')
                                                            ->default(fn ($get) => ($get('theme') ?: 'dark-boho') === 'sunny-meadow' ? '#fff8ec' : '#1d1d1b')
                                                            ->live(),
                                                        ColorPicker::make('theme_config.cover.textColor')
                                                            ->label('Warna Teks Cover')
                                                            ->default(fn ($get) => ($get('theme') ?: 'dark-boho') === 'sunny-meadow' ? '#3b2f2a' : '#efe6d8')
                                                            ->live(),
                                                        Toggle::make('theme_config.cover.usephoto')
                                                            ->label('Tampilkan Foto Latar Cover')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('theme_config.cover.showGuestName')
                                                            ->label('Tampilkan Nama Tamu di Cover')
                                                            ->default(true)
                                                            ->live(),
                                                    ]),

                                                Section::make('Tata Letak & Ilustrasi')
                                                    ->compact()
                                                    ->columns(2)
                                                    ->schema([
                                                        Select::make('theme_config.layout.illustrationMode')
                                                            ->label('Mode Ilustrasi')
                                                            ->options([
                                                                'illustrated' => 'Ilustrasi Pampas Penuh',
                                                                'minimal' => 'Minimalis',
                                                            ])
                                                            ->default('illustrated')
                                                            ->visible(fn ($get) => ($get('theme') ?: 'dark-boho') === 'dark-boho')
                                                            ->live(),
                                                        Select::make('theme_config.layout.photoFrame')
                                                            ->label('Bentuk Bingkai Foto')
                                                            ->options([
                                                                'flower' => 'Bunga Ceria (Flower)',
                                                                'blob' => 'Blob Organik (Blob)',
                                                                'arch' => 'Lengkung Elegan (Arch)',
                                                            ])
                                                            ->default('flower')
                                                            ->visible(fn ($get) => ($get('theme') ?: 'dark-boho') === 'sunny-meadow')
                                                            ->live(),
                                                        Toggle::make('theme_config.layout.brideFirst')
                                                            ->label('Tampilkan Mempelai Wanita Terlebih Dahulu')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('theme_config.layout.giftInline')
                                                            ->label('Tanda Kasih di Alur Utama (Matikan = Tombol Melayang)')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('theme_config.layout.mapsAndRsvpBelowEvents')
                                                            ->label('Peta Utama & RSVP di Bawah Rangkaian Acara')
                                                            ->default(true)
                                                            ->live(),
                                                    ]),

                                                Section::make('Animasi & Gerak (Sunny Meadow)')
                                                    ->compact()
                                                    ->columns(2)
                                                    ->visible(fn ($get) => ($get('theme') ?: 'dark-boho') === 'sunny-meadow')
                                                    ->schema([
                                                        Toggle::make('theme_config.motion.confetti')
                                                            ->label('Efek Ledakan Konfeti')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('theme_config.motion.floatingDecor')
                                                            ->label('Animasi Bunga Bobbing & Awan')
                                                            ->default(true)
                                                            ->live(),
                                                    ]),

                                                Section::make('Visibilitas Section')
                                                    ->compact()
                                                    ->columns(3)
                                                    ->schema([
                                                        Toggle::make('theme_config.sections.verse')
                                                            ->label('Ayat / Kutipan')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('theme_config.sections.video')
                                                            ->label('Video YouTube')
                                                            ->default(false)
                                                            ->live(),
                                                        Toggle::make('theme_config.sections.loveStory')
                                                            ->label('Kisah Cinta')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('theme_config.sections.gallery')
                                                            ->label('Galeri Foto')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('theme_config.sections.livestream')
                                                            ->label('Live Streaming')
                                                            ->default(false)
                                                            ->live(),
                                                        Toggle::make('theme_config.sections.gift')
                                                            ->label('Tanda Kasih')
                                                            ->default(true)
                                                            ->live(),
                                                        Toggle::make('theme_config.sections.wishes')
                                                            ->label('Ucapan & Doa')
                                                            ->default(true)
                                                            ->live(),
                                                    ]),
                                            ]),

                                        View::make('filament.components.dark-boho-live-preview')
                                            ->columnSpan(['default' => 1, 'lg' => 5]),
                                    ]),
                            ]),

                        Tab::make('Informasi Utama')
                            ->schema([
                                Section::make('Identitas Undangan')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Pemilik Undangan')
                                            ->relationship('user', 'name')
                                            ->default(fn (): ?int => Auth::id())
                                            ->required(),
                                        TextInput::make('title')
                                            ->label('Judul Undangan')
                                            ->required()
                                            ->maxLength(160),
                                        TextInput::make('slug')
                                            ->label('Slug Tautan')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->regex('/^[a-z0-9-]+$/')
                                            ->maxLength(120),
                                        Select::make('timezone')
                                            ->label('Zona Waktu Acara')
                                            ->options([
                                                'Asia/Jakarta' => 'WIB (Asia/Jakarta)',
                                                'Asia/Makassar' => 'WITA (Asia/Makassar)',
                                                'Asia/Jayapura' => 'WIT (Asia/Jayapura)',
                                            ])
                                            ->default('Asia/Jakarta')
                                            ->required(),
                                        Toggle::make('is_published')
                                            ->label('Publikasikan Undangan')
                                            ->default(true),
                                        Toggle::make('no_index')
                                            ->label('Sembunyikan dari Mesin Pencari (noindex)')
                                            ->helperText('Jika aktif, Google dan mesin pencari lainnya tidak akan mengindeks undangan.')
                                            ->default(false),
                                        TextInput::make('active_days_after_event')
                                            ->label('Masa Aktif Setelah Acara (Hari)')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(365)
                                            ->default(30)
                                            ->helperText('Undangan tetap aktif diakses publik selama N hari setelah acara terakhir selesai.')
                                            ->required(),
                                        Textarea::make('opening_text')
                                            ->label('Kalimat Pembuka')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Mempelai & Ayat')
                            ->schema([
                                Section::make('Mempelai Wanita')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('bride_name')->label('Nama Lengkap & Gelar')->required(),
                                        TextInput::make('bride_nickname')->label('Nama Panggilan')->required(),
                                        TextInput::make('bride_order_label')->label('Putri Ke-'),
                                        TextInput::make('bride_origin')->label('Asal / Domisili'),
                                        TextInput::make('bride_father')->label('Nama Ayah'),
                                        TextInput::make('bride_mother')->label('Nama Ibu'),
                                        TextInput::make('bride_instagram')->label('Akun Instagram'),
                                        SpatieMediaLibraryFileUpload::make('bride_photo_media')
                                            ->label('Unggah Foto Mempelai Wanita')
                                            ->collection('bride_photo')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->image()
                                            ->maxSize(5120),
                                    ]),

                                Section::make('Mempelai Pria')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('groom_name')->label('Nama Lengkap & Gelar')->required(),
                                        TextInput::make('groom_nickname')->label('Nama Panggilan')->required(),
                                        TextInput::make('groom_order_label')->label('Putra Ke-'),
                                        TextInput::make('groom_origin')->label('Asal / Domisili'),
                                        TextInput::make('groom_father')->label('Nama Ayah'),
                                        TextInput::make('groom_mother')->label('Nama Ibu'),
                                        TextInput::make('groom_instagram')->label('Akun Instagram'),
                                        SpatieMediaLibraryFileUpload::make('groom_photo_media')
                                            ->label('Unggah Foto Mempelai Pria')
                                            ->collection('groom_photo')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->image()
                                            ->maxSize(5120),
                                    ]),

                                Section::make('Ayat Suci / Kutipan')
                                    ->schema([
                                        Textarea::make('verse_arabic')
                                            ->label('Teks Arab (Harakat)')
                                            ->rows(3)
                                            ->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar']),
                                        Textarea::make('verse_translation')
                                            ->label('Terjemahan / Kutipan')
                                            ->rows(3),
                                        TextInput::make('verse_source')
                                            ->label('Sumber Ayat / Kutipan'),
                                    ]),
                            ]),

                        Tab::make('Media & Penutup')
                            ->schema([
                                Section::make('Cover & Musik Latar')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('preset_music')
                                            ->label('Pilihan Musik Bawaan (Royalty-Free)')
                                            ->options(collect(config('invitation_music.presets', []))->mapWithKeys(fn ($item, $key) => [$key => $item['label'] ?? $key])->all())
                                            ->placeholder('Pilih dari pustaka musik...')
                                            ->helperText('Jika berkas musik kustom diunggah di bawah, berkas kustom akan diutamakan.')
                                            ->columnSpanFull(),
                                        SpatieMediaLibraryFileUpload::make('cover_media')
                                            ->label('Foto Cover Undangan')
                                            ->collection('cover')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->image()
                                            ->maxSize(5120),
                                        SpatieMediaLibraryFileUpload::make('music_media')
                                            ->label('Berkas Musik Latar (MP3/M4A/OGG/WAV)')
                                            ->collection('music')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->acceptedFileTypes(['audio/mpeg', 'audio/mp3', 'audio/ogg', 'audio/wav', 'audio/x-wav', 'audio/mp4', 'audio/x-m4a', 'audio/aac', 'video/mp4'])
                                            ->maxSize(15360),
                                        TextInput::make('music_title')
                                            ->label('Judul Lagu Latar')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Video & Live Streaming')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('video_url')
                                            ->label('Tautan Video YouTube')
                                            ->url(),
                                        TextInput::make('livestream_title')
                                            ->label('Judul Siaran Langsung'),
                                        TextInput::make('livestream_url')
                                            ->label('Tautan Live Streaming')
                                            ->url()
                                            ->columnSpanFull(),
                                        Textarea::make('livestream_note')
                                            ->label('Catatan Siaran Langsung')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Galeri, Hadiah, & Penutup')
                                    ->schema([
                                        TextInput::make('gallery_title')->label('Judul Galeri'),
                                        Textarea::make('gallery_quote')->label('Kutipan Galeri')->rows(2),
                                        Textarea::make('thank_you_gift_note')->label('Catatan Terima Kasih Hadiah')->rows(2),
                                        Textarea::make('closing_text')->label('Kalimat Penutup')->rows(3),
                                    ]),
                            ]),

                        Tab::make('Pesan WhatsApp')
                            ->schema([
                                Section::make('Format Draft Pesan WhatsApp')
                                    ->description('Kustomisasi template pesan WhatsApp yang akan dikirimkan kepada para tamu undangan.')
                                    ->schema([
                                        Textarea::make('wa_template')
                                            ->label('Template Pesan WhatsApp')
                                            ->rows(8)
                                            ->placeholder(fn ($record) => $record?->defaultWaTemplate() ?? (new Invitation)->defaultWaTemplate())
                                            ->helperText('Gunakan variabel: {nama} (Nama Tamu), {link} (Tautan Undangan), {mempelai} (Nama Mempelai), {tanggal} (Tanggal Acara), {lokasi} (Lokasi Acara). Kosongkan untuk menggunakan format bawaan sistem.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
