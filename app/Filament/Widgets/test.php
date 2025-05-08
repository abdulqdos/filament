<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AttendeeResource;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class test extends Widget implements HasActions , HasForms
{
    use InteractsWithForms , InteractsWithActions ;
    protected static string $view = 'filament.widgets.test';
    protected int | string | array $columnSpan = 'full';

    public function callNotification(): Action
    {
        return Action::make('callNotification')
            ->button()
            ->color('warning')
            ->label('Send a notification')
            ->action(fn () => Notification::make()->warning()->title('You sent a notification')->body('This is a Notification')->persistent()->actions([
                \Filament\Notifications\Actions\Action::make('Go To Attendee')->button()->color('primary')->url(AttendeeResource::getUrl('index')),
            ])->send());
    }
}
