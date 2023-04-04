<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Postamat extends Model
{
    use HasFactory;

    protected $table = 'postamats';

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'postamat_id')->orderBy('created_at', 'desc')->orderBy('id', 'desc');
    }
}
