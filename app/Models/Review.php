<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'postamat_id',
        'user_id',
        'user_fio',
        'text',
        'score'
    ];

    public function postamat() : BelongsTo {
        return $this->belongsTo(Postamat::class);
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }
}
