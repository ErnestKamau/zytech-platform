<?php

namespace App\Filament\Resources\Projects;

use App\Core\Enums\ConstructionStage;
use App\Core\Enums\MilestoneStatus;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\VisibilityStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Project\Actions\ArchiveProject;
use App\Domains\Project\Actions\FeatureProject;
use App\Domains\Project\Actions\PublishProject;
use App\Domains\Project\Services\ProjectService;
use App\Filament\Resources\Projects\Pages\ManageProjects;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectResource extends BaseResource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|\UnitEnum|null $navigationGroup = 'Projects';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required()->maxLength(255),
                Select::make('project_category_id')->relationship('category', 'name')->required()->searchable(),
                Select::make('type')->options(collect(ProjectType::cases())->mapWithKeys(
                    fn (ProjectType $type): array => [$type->value => $type->label()]
                ))->required(),
                Select::make('status')->options(collect(ProjectStatus::cases())->mapWithKeys(
                    fn (ProjectStatus $status): array => [$status->value => $status->label()]
                ))->required()->default(ProjectStatus::Draft->value),
                Select::make('visibility')->options(collect(VisibilityStatus::cases())->mapWithKeys(
                    fn (VisibilityStatus $status): array => [$status->value => $status->label()]
                ))->required()->default(VisibilityStatus::Public->value),
                Select::make('construction_stage')->options(collect(ConstructionStage::cases())->mapWithKeys(
                    fn (ConstructionStage $stage): array => [$stage->value => $stage->label()]
                ))->required(),
                TextInput::make('progress_percent')->numeric()->minValue(0)->maxValue(100)->default(0),
                Toggle::make('is_featured'),
                TextInput::make('sort_order')->numeric()->default(0),
                Textarea::make('excerpt')->rows(2)->columnSpanFull(),
                Textarea::make('body')->rows(4)->columnSpanFull(),
                Textarea::make('case_study')->rows(5)->columnSpanFull(),
                Select::make('image_key')->options(self::mediaImageOptions())->searchable(),
                Select::make('video_key')->options(self::mediaVideoOptions())->searchable(),
                TextInput::make('client_name'),
                TextInput::make('completion_year')->numeric(),
                DatePicker::make('started_on'),
                DatePicker::make('completed_on'),
                TextInput::make('county'),
                TextInput::make('city'),
                TextInput::make('location_label'),
                TextInput::make('latitude')->numeric(),
                TextInput::make('longitude')->numeric(),
                TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
                Textarea::make('meta_description')->rows(2)->columnSpanFull(),
                Select::make('og_image_key')->options(self::mediaImageOptions())->searchable(),
                CheckboxList::make('services')->relationship('services', 'title')->columns(2)->columnSpanFull(),
                Repeater::make('galleryItems')->relationship()->schema([
                    Select::make('image_key')->options(self::mediaImageOptions())->required(),
                    TextInput::make('caption'),
                ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
                Repeater::make('milestones')->relationship()->schema([
                    TextInput::make('title')->required(),
                    Textarea::make('description')->rows(2),
                    Select::make('status')->options(collect(MilestoneStatus::cases())->mapWithKeys(
                        fn (MilestoneStatus $status): array => [$status->value => $status->label()]
                    ))->default(MilestoneStatus::Pending->value),
                    DatePicker::make('completed_on'),
                ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
                Repeater::make('statistics')->relationship()->schema([
                    TextInput::make('label')->required(),
                    TextInput::make('value')->required(),
                    Toggle::make('is_visible')->default(true),
                ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
                Repeater::make('beforeAfter')->relationship()->schema([
                    Select::make('before_image_key')->options(self::mediaImageOptions())->required(),
                    Select::make('after_image_key')->options(self::mediaImageOptions())->required(),
                    TextInput::make('caption'),
                    Textarea::make('description')->rows(2),
                ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
                Repeater::make('progressUpdates')->relationship()->schema([
                    TextInput::make('title')->required(),
                    Textarea::make('body')->rows(2),
                    TextInput::make('progress_percent')->numeric()->minValue(0)->maxValue(100),
                    Select::make('image_key')->options(self::mediaImageOptions()),
                    Toggle::make('is_published')->default(true),
                ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('construction_stage')->badge()->formatStateUsing(
                    fn (ConstructionStage $state): string => $state->label()
                ),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (ProjectStatus $state): string => $state->label()
                ),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('progress_percent')->suffix('%'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('publish')->visible(fn (Project $record): bool => $record->status !== ProjectStatus::Published)
                    ->requiresConfirmation()->action(fn (Project $record) => app(PublishProject::class)->handle($record)),
                Action::make('feature')->visible(fn (Project $record): bool => ! $record->is_featured && $record->isPublished())
                    ->action(fn (Project $record) => app(FeatureProject::class)->handle($record, true)),
                Action::make('unfeature')->visible(fn (Project $record): bool => $record->is_featured)
                    ->action(fn (Project $record) => app(FeatureProject::class)->handle($record, false)),
                Action::make('archive')->visible(fn (Project $record): bool => $record->status !== ProjectStatus::Archived)
                    ->color('danger')->requiresConfirmation()
                    ->action(fn (Project $record) => app(ArchiveProject::class)->handle($record)),
                EditAction::make()->after(fn (Project $record) => app(ProjectService::class)->persisted($record)),
                DeleteAction::make()->after(fn () => app(ProjectService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->after(fn () => app(ProjectService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageProjects::route('/')];
    }

    /** @return array<string, string> */
    public static function mediaImageOptions(): array
    {
        return collect(config('zyntech-media.images', []))
            ->mapWithKeys(fn (array $image, string $key): array => [$key => str_replace('_', ' ', $key)])
            ->all();
    }

    /** @return array<string, string> */
    public static function mediaVideoOptions(): array
    {
        return collect(config('zyntech-media.videos', []))
            ->mapWithKeys(fn (array $video, string $key): array => [$key => str_replace('_', ' ', $key)])
            ->all();
    }
}
