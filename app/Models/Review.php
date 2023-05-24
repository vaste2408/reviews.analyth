<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $table = 'reviews';

    protected $fillable = [
        'postamat_id',
        'user_id',
        'user_fio',
        'user_phone',
        'text',
        'score',
        'confirmed',
        'thematic_id',
        'source_id',
        'need_reaction',
        'closed'
    ];

    public function postamat() : BelongsTo {
        return $this->belongsTo(Postamat::class);
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function thematic() : BelongsTo {
        return $this->belongsTo(Thematic::class);
    }

    public function source() : BelongsTo {
        return $this->belongsTo(Source::class);
    }

    public function marketplace() : BelongsTo {
        return $this->belongsTo(Marketplace::class);
    }

    public function theme() {
        return $this->belongsToThrough(Theme::class, Thematic::class);
    }

    public function emotion() : BelongsTo {
        return $this->belongsTo(Emotion::class);
    }

    public function getNegativeAttribute()
    {
        return $this->emotion_id === -1;
    }

    public function getNeutralAttribute()
    {
        return $this->emotion_id === 0;
    }

    public function getPositiveAttribute()
    {
        return $this->emotion_id === 1;
    }

    public function getUndefinedAttribute()
    {
        return $this->emotion_id == null;
    }
}
