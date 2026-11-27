<?php

namespace App\Filament\Resources\Invitations\RelationManagers;

use App\Models\Guest;
use App\Models\Invitation;
use App\Models\SeatingTable;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class SeatingTablesRelationManager extends RelationManager
{
    protected static string $relationship = 'seatingTables';

    protected static ?string $title = 'Meja & Tempat Duduk';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama / Nomor Meja')
                    ->required()
                    ->maxLength(80)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('invitation_id', $this->getOwnerRecord()->id)
                    ),
                TextInput::make('capacity')
                    ->label('Kapasitas (Orang)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100)
                    ->default(10)
                    ->required(),
                Toggle::make('allow_over_capacity')
                    ->label('Izinkan Melebihi Kapasitas')
                    ->default(false)
                    ->helperText('Secara bawaan nonaktif agar kapasitas meja terjaga.'),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(1),
                Textarea::make('notes')
                    ->label('Catatan Meja (Opsional)')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->description(function (): string {
                /** @var Invitation $invitation */
                $invitation = $this->getOwnerRecord();
                $unassignedCount = $invitation->guests()->whereNull('seating_table_id')->count();
                $emptyTablesCount = $invitation->seatingTables()->doesntHave('guests')->count();

                return "Rekap Tempat Duduk: {$unassignedCount} tamu belum mendapat meja | {$emptyTablesCount} meja masih kosong.";
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Meja')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('occupancy')
                    ->label('Terisi / Kapasitas')
                    ->state(fn (SeatingTable $record): string => $record->occupiedSeatsCount().' / '.$record->capacity.' org')
                    ->badge()
                    ->color(function (SeatingTable $record): string {
                        $occupied = $record->occupiedSeatsCount();
                        if ($occupied > $record->capacity) {
                            return 'danger';
                        }
                        if ($occupied === $record->capacity) {
                            return 'warning';
                        }

                        return 'success';
                    }),
                IconColumn::make('allow_over_capacity')
                    ->label('Boleh Lebih')
                    ->boolean(),
                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(40)
                    ->placeholder('-'),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Meja'),
                Action::make('export_seating_csv')
                    ->label('Ekspor Tempat Duduk (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        /** @var Invitation $invitation */
                        $invitation = $this->getOwnerRecord();
                        $fileName = 'tempat-duduk-'.Str::slug($invitation->slug).'-'.now()->format('Ymd-His').'.csv';

                        return response()->stream(function () use ($invitation) {
                            $handle = fopen('php://output', 'w');
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                            fputcsv($handle, ['No', 'Nama Tamu', 'Grup', 'Jumlah Orang (Pax)', 'Meja', 'Kursi']);

                            $guests = $invitation->guests()->with('seatingTable')->orderBy('seating_table_id')->orderBy('name')->get();
                            foreach ($guests as $index => $guest) {
                                /** @var Guest $guest */
                                fputcsv($handle, [
                                    $index + 1,
                                    $guest->name,
                                    $guest->group ?: 'Umum',
                                    $guest->max_pax,
                                    $guest->seatingTable?->name ?: 'Belum Ditetapkan',
                                    $guest->seat_label ?: '-',
                                ]);
                            }

                            fclose($handle);
                        }, 200, [
                            'Content-Type' => 'text/csv; charset=utf-8',
                            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
                        ]);
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
