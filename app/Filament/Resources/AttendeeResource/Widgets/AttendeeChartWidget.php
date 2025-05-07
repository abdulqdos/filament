<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Models\Attendee;
use App\Models\Speaker;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AttendeeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Attendee Signups';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '250px';

    public ?String $filter = '3months';
    protected function getFilters(): ?array
    {
        return [
           'week' => 'Last Week',
           'month' => 'Last Month',
           '3months' => 'Last 3 Months',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        match ($filter) {
            'week' => $data = Trend::model(Attendee::class)
            ->between(
                now()->subWeek(),
                now(),
            )->perDay()
            ->count(),

            'month' => $data = Trend::model(Attendee::class)
                ->between(
                    now()->subMonth(),
                    now(),
                )->perDay()
                ->count(),

            '3months' => $data = Trend::model(Attendee::class)
                ->between(
                    now()->subMonths(3),
                    now(),
                )->perDay()
                ->count(),
        };


        return [
            'datasets' => [
                [
                    'label' => 'Attendees',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
