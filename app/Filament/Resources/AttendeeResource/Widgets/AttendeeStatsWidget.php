<?php

namespace App\Filament\Resources\AttendeeResource\Widgets;

use App\Models\Attendee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendeeStatsWidget extends BaseWidget
{

    protected function getColumns(): int
    {
        return 2;
    }
    protected function getStats(): array
    {
        return [
            Stat::make('Attendees Count' , Attendee::count())
            ->description('Total number of attendees')
            ->descriptionIcon('heroicon-o-user-group')
            ->color('success')
            ->chart([1 ,3,4,2,7,3]),
            Stat::make('Total Revenue' , Attendee::sum('ticket_cost') / 100)
                ->description('Total revenue'),
        ];
    }
}
