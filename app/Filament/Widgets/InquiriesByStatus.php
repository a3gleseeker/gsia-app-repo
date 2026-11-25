<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PageView; 

class InquiriesByStatus extends ChartWidget
{
    protected ?string $heading = 'Inquiries By Status';

    protected function getData(): array
    {   
        $counts = Inquiry::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return [
            'datasets' => [['data' => $counts->values()]], 'labels' => $counts->keys()
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public static function canView(): bool {
        return auth()->user()->isManager();
    }
}
