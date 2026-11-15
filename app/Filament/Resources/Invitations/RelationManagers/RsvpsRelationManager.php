<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use App\Models\Invitation;
use App\Models\Rsvp;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                TextInput::make('pax')->label('Jumlah Tamu (Pax)')->numeric()->minValue(0)->maxValue(20),
                Textarea::make('message')->label('Pesan / Doa')->rows(3)->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('updated_at', 'desc')
            ->description(function (): string {
                /** @var Invitation $invitation */
                $invitation = $this->getOwnerRecord();
                $totalGuests = $invitation->guests()->count();
                $rsvps = $invitation->rsvps()->get();
                $attendingCount = $rsvps->where('attending', true)->count();
                $attendingPax = (int) $rsvps->where('attending', true)->sum('pax');
                $declinedCount = $rsvps->where('attending', false)->count();
                $unanswered = max(0, $totalGuests - $rsvps->whereNotNull('guest_id')->count());

                return "Rekap Kehadiran: {$attendingCount} hadir ({$attendingPax} pax) | {$declinedCount} tidak hadir | {$unanswered} belum menjawab dari {$totalGuests} tamu terdaftar.";
            })
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('guest.code')->label('Kode Tamu')->badge()->placeholder('Tamu Umum'),
                TextColumn::make('attending')
                    ->label('Status Kehadiran')
                    ->state(fn (Rsvp $record): string => $record->attending ? "Hadir ({$record->pax} pax)" : 'Tidak Hadir')
                    ->badge()
                    ->color(fn (Rsvp $record): string => $record->attending ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('pax')->label('Pax')->sortable(),
                TextColumn::make('message')->label('Pesan / Doa')->limit(60)->wrap(),
                TextColumn::make('created_at')->label('Waktu Konfirmasi')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('attending')
                    ->label('Status Kehadiran')
                    ->placeholder('Semua')
                    ->trueLabel('Hadir')
                    ->falseLabel('Tidak Hadir'),
            ])
            ->headerActions([
                Action::make('export_csv')
                    ->label('Ekspor Rekap RSVP (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        /** @var Invitation $invitation */
                        $invitation = $this->getOwnerRecord();
                        $fileName = 'rekap-rsvp-'.Str::slug($invitation->slug).'-'.now()->format('Ymd-His').'.csv';

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
                                'Status Kehadiran',
                                'Jumlah Pax',
                                'Pesan / Doa',
                                'Waktu Konfirmasi',
                            ]);

                            $index = 1;
                            foreach ($invitation->rsvps()->with('guest')->latest('updated_at')->get() as $rsvp) {
                                fputcsv($handle, [
                                    $index++,
                                    $rsvp->name,
                                    $rsvp->guest?->code ?: 'Tamu Umum',
                                    $rsvp->guest?->group ?: '-',
                                    $rsvp->attending ? 'Hadir' : 'Tidak Hadir',
                                    $rsvp->pax,
                                    $rsvp->message ?: '-',
                                    $rsvp->created_at ? $rsvp->created_at->format('d/m/Y H:i') : '-',
                                ]);
                            }

                            fclose($handle);
                        }, 200, $headers);
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
