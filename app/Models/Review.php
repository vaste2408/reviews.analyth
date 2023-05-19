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
        'score',
        'confirmed'
    ];

    public function postamat() : BelongsTo {
        return $this->belongsTo(Postamat::class);
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function getCharacteristicAttribute()
    {
        if ($this->score > 3)
            return 'Положительный';
        if ($this->score < 3)
            return 'Негативный';
        return 'Нейтральный';
    }

    public function getNegativeAttribute()
    {
        return $this->characteristic === 'Негативный';
    }

    public function getNeutralAttribute()
    {
        return $this->characteristic === 'Нейтральный';
    }

    public function getPositiveAttribute()
    {
        return $this->characteristic === 'Положительный';
    }

    public function getCategoryAttribute()
    {
        return 'Общая';
    }
}
