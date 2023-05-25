<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Theme extends Model
{
    use HasFactory;

    protected $table = 'themes';

    protected $fillable = [
        'name',
    ];

    public function thematics() {
        return $this->hasMany(Thematic::class);
    }

    public function toArray(){
        $array = parent::toArray();
        $array['label'] = $array['name'];
        $array['value'] = $array['id'];
        return $array;
    }
}
