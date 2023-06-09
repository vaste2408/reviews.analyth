<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emotion extends Model
{
    use HasFactory;

    protected $table = 'emotions';

    protected $fillable = [
        'name',
    ];

    public function toArray(){
        $array = parent::toArray();
        $array['label'] = $array['name'];
        $array['value'] = $array['id'];
        return $array;
    }
}
