<?php

namespace App\Models;

use App\Enums\TalkLength;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use TalkStatus;

class Talk extends Model
{
    use HasFactory;

    protected  $casts = [
            'id' => 'integer',
            'speaker_id' => 'integer',
            'status' => TalkStatus::class,
            'length' => TalkLength::class,
        ];

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }
}
