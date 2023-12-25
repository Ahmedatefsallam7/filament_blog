<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Author;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use Filament\Forms\Components\Tabs;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\AuthorResource\Pages;
use App\Filament\Resources\AuthorResource\RelationManagers\PostsRelationManager;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 0;
    // protected static ?string $navigationLabel = 'Post Author';
    // protected static ?string $modelLabel = 'Post Author';
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationIcon = 'heroicon-s-users';
    protected static ?string $activeNavigationIcon = 'heroicon-s-check-badge';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->required()
                        ->maxLength(255)
                        ->email()
                        ->unique(Author::class, 'email', ignoreRecord: true),

                    TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(3),

            Tabs::make()->tabs([
                Tab::make('Bio')
                    ->schema([
                        MarkdownEditor::make('bio')->columnSpanFull(),

                    ]),
                Tab::make('Photo')
                    ->schema([
                        FileUpload::make('photo')
                            ->required()
                            ->directory('authorImgs')
                            ->preserveFilenames()
                            ->imageEditor()
                            ->image(),
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->alignLeft(),

                TextColumn::make('address')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->alignLeft()
                    ->toggleable(),

                TextColumn::make('bio')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->alignLeft()
                    ->toggleable(),

                TextColumn::make('posts_count')
                    ->counts('posts')
                    ->label('Total Posts')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [PostsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
