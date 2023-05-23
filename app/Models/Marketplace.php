<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marketplace extends Model
{
    use HasFactory;

    protected $table = 'marketplaces';

    protected $fillable = [
        'name',
        'href',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'marketplace_id')->orderBy('created_at', 'desc')->orderBy('id', 'desc');
    }

    public function toArray(){
        $array = parent::toArray();
        $array['label'] = $array['name'];
        $array['value'] = $array['id'];
        return $array;
    }
}
