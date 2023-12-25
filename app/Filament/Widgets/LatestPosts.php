<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\PostResource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ViewPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Filament\Resources\PostResource\Pages\CreatePost;

class LatestPosts extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    public function table(Table $table): Table
    {
        return $table
            ->query(PostResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->description('Latest posts created')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Image')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),


                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->getStateUsing(fn (Post $record): string => $record->published_at ? 'Published' : 'Draft')
                    ->toggleable()
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
                    ->toggleable()
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('comments_count')
                    ->label('Total Comments')
                    ->toggleable()
                    ->counts('comments'),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                ])
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
            'view' => ViewPost::route('/{record}'),
        ];
    }
}
