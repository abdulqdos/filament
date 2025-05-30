<?php

namespace App\Filament\Resources\SpeakerResource\Pages;

use App\Filament\Resources\SpeakerResource;
use App\Models\Speaker;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSpeaker extends ViewRecord
{
    protected static string $resource = SpeakerResource::class;

    public function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->form(Speaker::getForm())
        ];
    }
}
