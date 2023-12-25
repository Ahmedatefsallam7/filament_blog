<?php

namespace App\Filament\Resources\AuthorResource\RelationManagers;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255),

                        Forms\Components\Select::make('author_id')
                            ->relationship('author', 'name')
                            // ->searchable()
                            ->required(),

                        Forms\Components\DatePicker::make('published_at')
                            ->label('Published Date')
                            ->default(date('Y-m-d')),

                        Forms\Components\MarkdownEditor::make('content')
                            ->required()
                            ->columnSpan('full'),



                    ])
                    ->columns(3),

                Forms\Components\Section::make('Image')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('postImgs')
                            ->preserveFilenames()
                            ->imageEditor()
                            ->hiddenLabel(),
                    ])
                    ->collapsible(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Image'),

                Tables\Columns\TextColumn::make('title')
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
                    ->date(),

                Tables\Columns\TextColumn::make('comments.content')
                    ->label('Comments')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
