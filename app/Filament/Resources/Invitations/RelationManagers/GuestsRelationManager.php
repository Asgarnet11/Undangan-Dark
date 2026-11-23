<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
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
                    ->label('Maksimal Tamu (Pax)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(20)
                    ->default(2)
                    ->required(),
                TextInput::make('phone')
                    ->label('Nomor WhatsApp (Contoh: 0812xxxx atau 62812xxxx)')
                    ->tel()
                    ->maxLength(32),
                Select::make('allowed_event_ids')
                    ->label('Akses Acara Tertentu')
                    ->options(fn () => Event::where('invitation_id', $this->getOwnerRecord()->id)->pluck('title', 'id')->toArray())
                    ->multiple()
                    ->placeholder('Semua Acara (Default)')
                    ->helperText('Kosongkan bila tamu dapat melihat seluruh acara. Pilih sesi jika khusus (misal: Resepsi saja).'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Tamu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->copyable(),
                TextColumn::make('personal_url')
                    ->label('Tautan Undangan')
                    ->state(fn (Guest $record): string => route('invitation.show', ['slug' => $this->getOwnerRecord()->slug, 'to' => $record->code]))
                    ->url(fn (Guest $record): string => route('invitation.show', ['slug' => $this->getOwnerRecord()->slug, 'to' => $record->code]), shouldOpenInNewTab: true)
                    ->copyable()
                    ->copyMessage('Tautan undangan berhasil disalin')
                    ->copyableState(fn (Guest $record): string => route('invitation.show', ['slug' => $this->getOwnerRecord()->slug, 'to' => $record->code]))
                    ->wrap(),
                TextColumn::make('group')
                    ->label('Grup')
                    ->badge()
                    ->placeholder('Umum')
                    ->searchable(),
                TextColumn::make('max_pax')
                    ->label('Maks. Pax')
                    ->sortable(),
                TextColumn::make('allowed_events')
                    ->label('Akses Acara')
                    ->state(fn (Guest $record): string => empty($record->allowed_event_ids) ? 'Semua Acara' : count($record->allowed_event_ids).' Acara')
                    ->badge()
                    ->color(fn (Guest $record): string => empty($record->allowed_event_ids) ? 'gray' : 'info'),
                TextColumn::make('checked_in_at')
                    ->label('Status Check-In')
                    ->state(fn (Guest $record): string => $record->checked_in_at ? $record->checked_in_at->format('d M H:i') : 'Belum Check-In')
                    ->badge()
                    ->color(fn (Guest $record): string => $record->checked_in_at ? 'success' : 'gray'),
                TextColumn::make('opened_at')
                    ->label('Status Buka')
                    ->state(fn (Guest $record): string => $record->opened_at ? $record->opened_at->format('d M H:i') : 'Belum Dibuka')
                    ->badge()
                    ->color(fn (Guest $record): string => $record->opened_at ? 'success' : 'gray'),
                TextColumn::make('rsvp_status')
                    ->label('Status RSVP')
                    ->state(function (Guest $record): string {
                        if ($record->rsvp === null) {
                            return 'Belum Menjawab';
                        }

                        return $record->rsvp->attending
                            ? "Hadir ({$record->rsvp->pax} pax)"
                            : 'Tidak Hadir';
                    })
                    ->badge()
                    ->color(function (Guest $record): string {
                        if ($record->rsvp === null) {
                            return 'gray';
                        }

                        return $record->rsvp->attending ? 'success' : 'danger';
                    }),
            ])
            ->filters([
                SelectFilter::make('rsvp_status')
                    ->label('Status RSVP')
                    ->options([
                        'attending' => 'Hadir',
                        'not_attending' => 'Tidak Hadir',
                        'unanswered' => 'Belum Menjawab',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'attending' => $query->whereHas('rsvp', fn ($q) => $q->where('attending', true)),
                            'not_attending' => $query->whereHas('rsvp', fn ($q) => $q->where('attending', false)),
                            'unanswered' => $query->whereDoesntHave('rsvp'),
                            default => $query,
                        };
                    }),
                TernaryFilter::make('opened')
                    ->label('Status Buka')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Dibuka')
                    ->falseLabel('Belum Dibuka')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('opened_at'),
                        false: fn ($query) => $query->whereNull('opened_at'),
                    ),
                TernaryFilter::make('checked_in')
                    ->label('Status Check-In')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Check-In')
                    ->falseLabel('Belum Check-In')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('checked_in_at'),
                        false: fn ($query) => $query->whereNull('checked_in_at'),
                    ),
                SelectFilter::make('group')
                    ->label('Kategori / Grup')
                    ->options(fn () => Guest::query()
                        ->where('invitation_id', $this->getOwnerRecord()->id)
                        ->whereNotNull('group')
                        ->where('group', '!=', '')
                        ->pluck('group', 'group')
                        ->toArray()
                    ),
            ])
            ->headerActions([
                Action::make('open_reception')
                    ->label('Meja Resepsi (Check-In)')
                    ->icon('heroicon-o-qr-code')
                    ->color('primary')
                    ->url(fn (): string => route('invitation.check-in', ['slug' => $this->getOwnerRecord()->slug]), shouldOpenInNewTab: true),
                CreateAction::make()->label('Tambah Tamu'),
                Action::make('import_csv')
                    ->label('Impor CSV')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->modalHeading('Impor Daftar Tamu dari Berkas CSV')
                    ->modalDescription('Unggah berkas CSV dengan susunan kolom: Nama, Grup, Maksimal Tamu, Nomor WhatsApp. Baris pertama (header) akan otomatis dilewati jika berupa judul kolom.')
                    ->form([
                        FileUpload::make('file')
                            ->label('Pilih Berkas CSV')
                            ->disk('local')
                            ->directory('temp-guest-imports')
                            ->acceptedFileTypes(['text/csv', 'text/plain', 'text/x-csv', 'application/vnd.ms-excel'])
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $fileKey = is_array($data['file'] ?? null) ? (reset($data['file']) ?: '') : (string) ($data['file'] ?? '');
                        $path = Storage::disk('local')->path($fileKey);
                        if ($fileKey === '' || ! file_exists($path)) {
                            Notification::make()->title('Berkas CSV tidak ditemukan')->danger()->send();

                            return;
                        }

                        $handle = fopen($path, 'r');
                        if (! $handle) {
                            Notification::make()->title('Gagal membaca berkas CSV')->danger()->send();

                            return;
                        }

                        /** @var Invitation $invitation */
                        $invitation = $this->getOwnerRecord();
                        $importedCount = 0;
                        $rowNumber = 0;

                        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                            $rowNumber++;
                            if (count($row) === 1 && str_contains($row[0], ';')) {
                                $row = str_getcsv($row[0], ';');
                            }

                            if (empty($row[0])) {
                                continue;
                            }

                            $firstCol = strtolower(trim((string) $row[0]));
                            if ($rowNumber === 1 && in_array($firstCol, ['nama', 'name', 'nama tamu', 'guest name'], true)) {
                                continue;
                            }

                            $name = trim((string) $row[0]);
                            $group = isset($row[1]) && trim((string) $row[1]) !== '' ? trim((string) $row[1]) : null;
                            $maxPax = isset($row[2]) && is_numeric(trim((string) $row[2])) ? max(1, (int) $row[2]) : 2;
                            $phone = isset($row[3]) && trim((string) $row[3]) !== '' ? trim((string) $row[3]) : null;

                            do {
                                $code = strtoupper(Str::random(6));
                            } while (Guest::where('code', $code)->exists());

                            $invitation->guests()->create([
                                'name' => $name,
                                'code' => $code,
                                'group' => $group,
                                'max_pax' => $maxPax,
                                'phone' => $phone,
                            ]);

                            $importedCount++;
                        }

                        fclose($handle);
                        Storage::disk('local')->delete($fileKey);

                        Notification::make()
                            ->title("Berhasil mengimpor {$importedCount} tamu baru")
                            ->success()
                            ->send();
                    }),
                Action::make('download_sample_csv')
                    ->label('Format CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function () {
                        $headers = [
                            'Content-Type' => 'text/csv; charset=utf-8',
                            'Content-Disposition' => 'attachment; filename="format-impor-tamu.csv"',
                        ];

                        return response()->stream(function () {
                            $handle = fopen('php://output', 'w');
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                            fputcsv($handle, ['nama', 'grup', 'maks_tamu', 'no_whatsapp']);
                            fputcsv($handle, ['Bapak Hendra Wijaya & Keluarga', 'Keluarga', '4', '08123456789']);
                            fputcsv($handle, ['Siti Nurhaliza', 'Sahabat', '2', '08987654321']);
                            fputcsv($handle, ['Rizky Pratama', 'Rekan Kerja', '2', '085211223344']);
                            fclose($handle);
                        }, 200, $headers);
                    }),
                Action::make('export_csv')
                    ->label('Ekspor Tautan (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        /** @var Invitation $invitation */
                        $invitation = $this->getOwnerRecord();
                        $fileName = 'daftar-tautan-tamu-'.Str::slug($invitation->slug).'-'.now()->format('Ymd-His').'.csv';

                        $headers = [
                            'Content-Type' => 'text/csv; charset=utf-8',
                            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
                        ];

                        return response()->stream(function () use ($invitation) {
                            $handle = fopen('php://output', 'w');
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                            fputcsv($handle, [
                                'No',
                                'Nama Tamu',
                                'Kode Tamu',
                                'Kategori / Grup',
                                'Maks Pax',
                                'Nomor WhatsApp',
                                'Tautan Undangan Lengkap',
                                'Status Dibuka',
                                'Status RSVP',
                                'Draft Pesan WhatsApp',
                            ]);

                            $index = 1;
                            foreach ($invitation->guests()->with('rsvp')->get() as $guest) {
                                $url = route('invitation.show', ['slug' => $invitation->slug, 'to' => $guest->code]);
                                $rsvpText = $guest->rsvp === null
                                    ? 'Belum Menjawab'
                                    : ($guest->rsvp->attending ? "Hadir ({$guest->rsvp->pax} pax)" : 'Tidak Hadir');
                                $openedText = $guest->opened_at ? $guest->opened_at->format('d/m/Y H:i') : 'Belum Dibuka';
                                $waDraft = $invitation->generateWaMessage($guest);

                                fputcsv($handle, [
                                    $index++,
                                    $guest->name,
                                    $guest->code,
                                    $guest->group ?: 'Umum',
                                    $guest->max_pax,
                                    $guest->phone ?: '-',
                                    $url,
                                    $openedText,
                                    $rsvpText,
                                    $waDraft,
                                ]);
                            }

                            fclose($handle);
                        }, 200, $headers);
                    }),
            ])
            ->recordActions([
                Action::make('toggle_check_in')
                    ->label(fn (Guest $record): string => $record->checked_in_at ? 'Batal Hadir' : 'Tandai Hadir')
                    ->icon(fn (Guest $record): string => $record->checked_in_at ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Guest $record): string => $record->checked_in_at ? 'warning' : 'success')
                    ->action(function (Guest $record): void {
                        $record->update([
                            'checked_in_at' => $record->checked_in_at ? null : now(),
                        ]);
                        Notification::make()
                            ->title($record->checked_in_at ? "Tamu {$record->name} berhasil ditandai hadir" : "Status kehadiran {$record->name} dibatalkan")
                            ->success()
                            ->send();
                    }),
                Action::make('open_invitation')
                    ->label('Buka')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Guest $record): string => route('invitation.show', ['slug' => $this->getOwnerRecord()->slug, 'to' => $record->code]), shouldOpenInNewTab: true),
                Action::make('send_whatsapp')
                    ->label('Kirim WA')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->modalHeading(fn (Guest $record): string => 'Kirim Undangan WhatsApp: '.$record->name)
                    ->modalDescription('Sesuaikan pesan atau nomor sebelum mengirim tautan undangan.')
                    ->modalSubmitActionLabel('Kirim via WhatsApp')
                    ->fillForm(function (Guest $record): array {
                        /** @var Invitation $invitation */
                        $invitation = $this->getOwnerRecord();
                        $defaultMessage = $invitation->generateWaMessage($record);

                        return [
                            'phone' => (string) $record->phone,
                            'message' => $defaultMessage,
                        ];
                    })
                    ->form([
                        TextInput::make('phone')
                            ->label('Nomor WhatsApp (Contoh: 0812xxxx atau 62812xxxx)')
                            ->tel()
                            ->maxLength(32),
                        Textarea::make('message')
                            ->label('Template Pesan Undangan')
                            ->rows(8)
                            ->required(),
                    ])
                    ->action(function (array $data, Guest $record): void {
                        if (! empty($data['phone']) && $data['phone'] !== $record->phone) {
                            $record->update(['phone' => $data['phone']]);
                        }

                        $phone = preg_replace('/[^0-9]/', '', (string) ($data['phone'] ?? ''));
                        if ($phone !== '' && str_starts_with($phone, '0')) {
                            $phone = '62'.substr($phone, 1);
                        }

                        $encodedText = rawurlencode((string) $data['message']);
                        $waUrl = $phone !== ''
                            ? "https://api.whatsapp.com/send?phone={$phone}&text={$encodedText}"
                            : "https://api.whatsapp.com/send?text={$encodedText}";

                        Notification::make()
                            ->title('Tautan WhatsApp siap dibuka')
                            ->actions([
                                Action::make('open')
                                    ->label('Buka WhatsApp Sekarang')
                                    ->url($waUrl, shouldOpenInNewTab: true),
                            ])
                            ->success()
                            ->send();
                    }),
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
