<?php

namespace App\Filament\Resources\AdatEventTypes;

use App\Filament\Resources\AdatEventTypes\Pages\CreateAdatEventType;
use App\Filament\Resources\AdatEventTypes\Pages\EditAdatEventType;
use App\Filament\Resources\AdatEventTypes\Pages\ListAdatEventTypes;
use App\Filament\Resources\AdatEventTypes\Schemas\AdatEventTypeForm;
use App\Filament\Resources\AdatEventTypes\Tables\AdatEventTypesTable;
use App\Models\AdatEventType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AdatEventTypeResource extends Resource
{
    protected static ?string $model = AdatEventType::class;

    protected static UnitEnum|string|null $navigationGroup = 'Adat & Budaya';

    protected static ?string $navigationLabel = 'Katalog Acara Adat';

    protected static ?string $modelLabel = 'Jenis Acara Adat';

    protected static ?string $pluralModelLabel = 'Katalog Acara Adat';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    public static function form(Schema $schema): Schema
    {
        return AdatEventTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdatEventTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdatEventTypes::route('/'),
            'create' => CreateAdatEventType::route('/create'),
            'edit' => EditAdatEventType::route('/{record}/edit'),
        ];
    }
}
