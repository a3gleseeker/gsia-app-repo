<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PageView; 

class ViewsOverTime extends ChartWidget
{   

    protected static ?int $sort = 3;

    protected ?string $heading = 'Views Over Time';

    protected function getData(): array
    {   
        $rows = PageView::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at',  '>=', now()->subDays(14))
            ->groupBy('day')    
            ->orderBy('day')
            ->pluck('total', 'day');

        return [
            'datasets' => [['label' =>  'views', 'data' => $rows->values()]],
            'labels' => $rows->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
