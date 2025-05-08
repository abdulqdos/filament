<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Filament\Resources\AttendeeResource\Pages\ListAttendees;
use App\Models\Attendee;
use App\Models\Speaker;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AttendeeChartWidget extends ChartWidget
{
    use InteractsWithPageTable ;
    protected static ?string $heading = 'Attendee Signups';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '200px';

    public ?String $filter = '3months';
    protected static ?string $pollingInterval = '1s';
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

        $query = $this->getPageTableQuery() ;
        match ($filter) {
            'week' => $data = Trend::query($query)
            ->between(
                now()->subWeek(),
                now(),
            )->perDay()
            ->count(),

            'month' => $data = Trend::query($query)
                ->between(
                    now()->subMonth(),
                    now(),
                )->perDay()
                ->count(),

            '3months' => $data = Trend::query($query)
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

    public function getTablePage()
    {
        return ListAttendees::class;
    }
    protected function getType(): string
    {
        return 'line';
    }
}
