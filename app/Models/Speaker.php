<?php

namespace App\Models;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Speaker extends Model
{
    use HasFactory;

    const QUALIFICATIONS = [
        'business' => 'Business',
        'Coding' => 'Coding',
        'first-time' => 'First Time',
        'open-source' => 'Open Source',
    ];

    protected $guarded = ['id'];
    protected  $casts = [
            'id' => 'integer',
            'qualifications' => 'array',
        ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks()
    {
        return $this->hasMany(Talk::class);
    }

    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->required(),
            TextInput::make('email')
                ->email()
                ->required(),
            FileUpload::make('avatar')
                ->imageEditor()
                ->avatar()
                ->directory('avatars' )
                ->maxSize(1024 * 1024 * 2),
            RichEditor::make('bio'),
            CheckboxList::make('qualifications')
                ->options([
                    'business' => 'Business',
                    'Coding' => 'Coding',
                    'first-time' => 'First Time',
                    'open-source' => 'Open Source',
                ])->columns(2)
                ->descriptions([
                    'business' => 'Business is this something great',
                    'Coding' => 'Coding is this something great',
                    'first-time' => 'First Time is this something great',
                    'open-source' => 'Open Source is this something great',
                ])
                ->searchable()
                ->bulkToggleable()
        ];
    }
}
