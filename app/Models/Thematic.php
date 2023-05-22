<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thematic extends Model
{
    use HasFactory;

    protected $table = 'thematics';

    protected $fillable = [
        'name',
        'theme_id',
    ];

    public function theme() : BelongsTo {
        return $this->belongsTo(Theme::class);
    }

    public function toArray(){
        $array = parent::toArray();
        $array['label'] = $array['name'];
        $array['value'] = $array['id'];
        return $array;
    }
}
