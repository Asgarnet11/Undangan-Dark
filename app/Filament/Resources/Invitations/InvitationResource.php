<?php

namespace App\Filament\Resources\Invitations;

use App\Filament\Resources\Invitations\Pages\CreateInvitation;
use App\Filament\Resources\Invitations\Pages\EditInvitation;
use App\Filament\Resources\Invitations\Pages\ListInvitations;
use App\Filament\Resources\Invitations\RelationManagers\CoInvitersRelationManager;
use App\Filament\Resources\Invitations\RelationManagers\EventsRelationManager;
use App\Filament\Resources\Invitations\RelationManagers\GalleryPhotosRelationManager;
use App\Filament\Resources\Invitations\RelationManagers\GiftAccountsRelationManager;
use App\Filament\Resources\Invitations\RelationManagers\GiftAddressesRelationManager;
use App\Filament\Resources\Invitations\RelationManagers\GuestsRelationManager;
use App\Filament\Resources\Invitations\RelationManagers\RsvpsRelationManager;
use App\Filament\Resources\Invitations\RelationManagers\WishesRelationManager;
use App\Filament\Resources\Invitations\Schemas\InvitationForm;
use App\Filament\Resources\Invitations\Tables\InvitationsTable;
use App\Models\Invitation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static ?string $navigationLabel = 'Undangan';

    protected static ?string $modelLabel = 'Undangan';

    protected static ?string $pluralModelLabel = 'Daftar Undangan';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelopeOpen;

    public static function form(Schema $schema): Schema
    {
        return InvitationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvitationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EventsRelationManager::class,
            GalleryPhotosRelationManager::class,
            GiftAccountsRelationManager::class,
            GiftAddressesRelationManager::class,
            CoInvitersRelationManager::class,
            GuestsRelationManager::class,
            RsvpsRelationManager::class,
            WishesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvitations::route('/'),
            'create' => CreateInvitation::route('/create'),
            'edit' => EditInvitation::route('/{record}/edit'),
        ];
    }
}
