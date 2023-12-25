<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\Pages\ViewPost;
use App\Filament\Resources\PostResource\Pages\ManagePostComments;
use App\Filament\Resources\PostResource\RelationManagers\AuthorRelationManager;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static ?string $activeNavigationIcon = 'heroicon-s-check-badge';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Create Post')
                ->description('Create a new post over here')
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->live(onBlur: true)
                        ->maxLength(255),

                    Forms\Components\Select::make('author_id')
                        ->relationship('author', 'name')
                        ->required(),

                    Forms\Components\DatePicker::make('published_at')
                        ->label('Published Date')
                        ->default(date('Y-m-d')),
                ])
                ->columns(3),

            Tabs::make()
                ->tabs([
                    Tab::make('Content')->schema([MarkdownEditor::make('content')->columnSpanFull()]),
                    Tab::make('Photo')->schema([
                        FileUpload::make('photo')
                            ->required()
                            ->directory('postImgs')
                            ->preserveFilenames()
                            ->imageEditor()
                            ->image(),
                    ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')->label('Image'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('content')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->getStateUsing(fn (Post $record): string => $record->published_at ? 'Published' : 'Draft')
                    ->colors([
                        'success' => 'Published',
                    ]),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published Date')
                    ->date()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('comments.content')
                    ->label('Comments')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label('Total Comments')
                    ->toggleable(),
            ])
            ->filters([
                // SelectFilter::make('status')->options([
                //     'Published',
                //     'Draft',
                // ])->multiple(),
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')->placeholder(
                            fn ($state): string => 'Dec 18, ' .
                                now()
                                ->subYear()
                                ->format('Y'),
                        ),
                        Forms\Components\DatePicker::make('published_until')->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['published_from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date))->when($data['published_until'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['published_from'] ?? null) {
                            $indicators['published_from'] = 'Published from ' . Carbon::parse($data['published_from'])->toFormattedDateString();
                        }
                        if ($data['published_until'] ?? null) {
                            $indicators['published_until'] = 'Published until ' . Carbon::parse($data['published_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->successNotificationTitle('Post deleted successfully')
                ])
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }



    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([ViewPost::class, Pages\EditPost::class, ManagePostComments::class]);
    }



    public static function getRelations(): array
    {
        return [AuthorRelationManager::class, CommentsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'comments' => ManagePostComments::route('/{record}/comments'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
            'view' => ViewPost::route('/{record}'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['author']);
    }
    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'author.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Post $record */
        $details = [];

        if ($record->author) {
            $details['Author'] = $record->author->name;
        }

        return $details;
    }
}
