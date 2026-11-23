<?php

namespace App\Filament\Resources\AdatGlossaryEntries;

use App\Filament\Resources\AdatGlossaryEntries\Pages\CreateAdatGlossaryEntry;
use App\Filament\Resources\AdatGlossaryEntries\Pages\EditAdatGlossaryEntry;
use App\Filament\Resources\AdatGlossaryEntries\Pages\ListAdatGlossaryEntries;
use App\Filament\Resources\AdatGlossaryEntries\Schemas\AdatGlossaryEntryForm;
use App\Filament\Resources\AdatGlossaryEntries\Tables\AdatGlossaryEntriesTable;
use App\Models\AdatGlossaryEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AdatGlossaryEntryResource extends Resource
{
    protected static ?string $model = AdatGlossaryEntry::class;

    protected static UnitEnum|string|null $navigationGroup = 'Adat & Budaya';

    protected static ?string $navigationLabel = 'Glosarium Istilah';

    protected static ?string $modelLabel = 'Istilah Adat';

    protected static ?string $pluralModelLabel = 'Glosarium Istilah Adat';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public static function form(Schema $schema): Schema
    {
        return AdatGlossaryEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdatGlossaryEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdatGlossaryEntries::route('/'),
            'create' => CreateAdatGlossaryEntry::route('/create'),
            'edit' => EditAdatGlossaryEntry::route('/{record}/edit'),
        ];
    }
}
