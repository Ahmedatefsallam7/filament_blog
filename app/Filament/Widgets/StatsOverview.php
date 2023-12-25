<?php

namespace App\Filament\Widgets;

use App\Models\Author;
use App\Models\Comment;
use App\Models\Customer;
use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    // protected static ?string $pollingInterval = '5s';danger

    protected function getStats(): array
    {
        return [
            Stat::make('Total Posts', Post::count())
                ->description('Total number of Posts')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([5, 4, 6, 5, 6, 4, 8, 4, 10]),

            Stat::make('Total Comments', Comment::count())
                ->description('Total number of Comments')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([5, 4, 6, 5, 6, 4, 8, 4, 10]),

            Stat::make('Total Authors', Author::count())
                ->description('Total number of Authors')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([5, 4, 6, 5, 6, 4, 8, 4, 10]),

            Stat::make('Total Users', Customer::count())
                ->description('Total number of Users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([5, 4, 6, 5, 6, 4, 8, 4, 10]),
        ];
    }
}
