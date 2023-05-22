<?php

namespace App\Services;

use App\Models\Postamat;
use App\Models\Review;

class PostamatService {
    public static function Postamats () {
        return Postamat::all();
    }

    public static function PostamatsForMap ($filters = []) {
        $query = Postamat::with('reviews')->take(100);
        $where = ['city' => 'Москва'];
        if ($filters['rating_min'] != 0 || $filters['rating_max'] != 5) {
            $query->whereBetween('rating', [$filters['rating_min'], $filters['rating_max']]);
        }

        if (isset($filters['with_reviews'])) {
            if ($filters['with_reviews']) {
                $query->whereHas('reviews');
            }
            else {
                $query->doesntHave('reviews');
            }
        }
        //TODO по характеру отзывов
        if (isset($filters['type'])) {

        }
        //TODO категориям отзывов
        if (isset($filters['category'])) {

        }
        return $query->where($where)->get();
    }

    public static function PostamatsWithReviews () {
        return Postamat::with('reviews')->get();
    }

    public static function Reviews () {
        return Review::all();
    }
}
