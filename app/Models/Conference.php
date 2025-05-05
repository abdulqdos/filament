<?php

namespace App\Models;

use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Filament\Forms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Markdown;
//use App\Filament\Resources\ConferenceResource\RelationManagers;

class Conference extends Model
{
    use HasFactory;

    protected  $casts = [
            'id' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'venue_id' => 'integer',
            'region' => Region::class,
        ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm()
    {
        return [
            Tabs::make()
                ->columnSpanFull()
            ->tabs([
               Tabs\Tab::make('Conferences Details')
                   ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->helperText('The Name Of THe conference')
                        ->maxLength(60)
                        ->default('My Conference')
                        ->required(),
                    MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->disableToolbarButtons(['italic' , 'underline'])
                        ->helperText('This is description')
                        ->required(),
                    DateTimePicker::make('start_date')
                        ->native(false)
                        ->required(),
                    DateTimePicker::make('end_date')
                        ->native(false)
                        ->required(),

                    Fieldset::make('Status')
                        ->columns(1)
                        ->schema([
                            Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                    'rejected' => 'Rejected',
                                ])
                                ->required(),

                            Toggle::make('is_published')
                                ->default(true)
                                ->required(),

                        ]),


                    CheckboxList::make('speakers')
                        ->options(Speaker::all()->pluck('id' , 'name')->toArray())
                        ->relationship('speakers', 'name'),
                ]),

                        Tabs\Tab::make('Location')
                        ->schema([
                            Select::make('region')
                                ->live()
                                ->enum(Region::class)
                                ->options(Region::class),

                            Select::make('venue_id')
                                ->searchable()
                                ->preload()
                                ->editOptionForm(Venue::getForm())
                                ->createOptionForm(Venue::getForm())
                                ->relationship('venue', 'name' , modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                    return $query->where('region', $get('region'));
                                }),
                    ]),
            ]),


            Actions::make([
                Action::make('star')
                    ->label('Fill with Factory Data')
                    ->icon('heroicon-m-star')
                    ->visible(function (string $operation) {
                        if ($operation !== 'create') {
                            return false;
                        }

                        if (!app()->environment('local')) {
                            return false;
                        }
                        return true;
                    })
                    ->action(function ($livewire) {
                        $data = Conference::factory()->make()->toArray();
                        unset($data['venue_id']); // Better Then Set Null  in Factory
                        $livewire->form->fill($data );
                    }),
            ]),

//            Section::make('Conference Details')
//                ->collapsible()
//                ->icon('heroicon-s-calendar')
//                ->description('Details about the conference')
//                ->columns(2)
//                ->schema([
//
//            ]),

//            Section::make('location')
//                ->columns(2)
//                ->schema([
//                    Select::make('region')
//                        ->live()
//                        ->enum(Region::class)
//                        ->options(Region::class),
//
//                    Select::make('venue_id')
//                        ->searchable()
//                        ->preload()
//                        ->editOptionForm(Venue::getForm())
//                        ->createOptionForm(Venue::getForm())
//                        ->relationship('venue', 'name' , modifyQueryUsing: function (Builder $query, Forms\Get $get) {
//                            return $query->where('region', $get('region'));
//                        }),
//            ]),
        ];
    }
}
