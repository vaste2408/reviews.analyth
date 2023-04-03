<?php

namespace App\Services;

use App\Models\Postamat;
use App\Models\Review;

class PostamatService {
    public static function Postamats () {
        return Postamat::all();
    }

    public static function PostamatsForMap () {
        return Postamat::with('reviews')->where(['city' => 'Москва'])->take(100)->get();
    }

    public static function PostamatsWithReviews () {
        return Postamat::with('reviews')->get();
    }

    public static function Reviews () {
        return Review::all();
    }

    public static function ReviewsFull () {
        return Review::with('postamat')->with('user')->get();
    }
}
