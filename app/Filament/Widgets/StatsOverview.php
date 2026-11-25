<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PageView;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('properties', Property::count()),
            Stat::make('Pending Inquiries', Inquiry::where('status', 'pending')->count())->color('warning'),
            Stat::make('Total Views', PageView::count()),
        ];
    }

    public static function canView(): bool{
        return auth()->user()->isManager();
    }
}

