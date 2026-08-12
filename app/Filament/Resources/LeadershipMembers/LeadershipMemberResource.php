<?php

namespace App\Filament\Resources\LeadershipMembers;

use App\Core\Filament\BaseResource;
use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\LeadershipMembers\Pages\ManageLeadershipMembers;
use App\Models\Company;
use App\Models\LeadershipMember;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeadershipMemberResource extends BaseResource
{
    protected static ?string $model = LeadershipMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Leadership';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')->default(fn () => Company::query()->value('id')),
                TextInput::make('name')->required(),
                TextInput::make('position')->required(),
                Textarea::make('biography')->rows(4)->columnSpanFull(),
                TextInput::make('photo_url')->url(),
                TextInput::make('email')->email(),
                TextInput::make('linkedin_url')->url(),
                Toggle::make('is_visible')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('position'),
                IconColumn::make('is_visible')->boolean(),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(fn () => app(CompanyService::class)->forget()),
                DeleteAction::make()
                    ->after(fn () => app(CompanyService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLeadershipMembers::route('/'),
        ];
    }
}
