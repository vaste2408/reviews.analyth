<?php

namespace App\Services;

use App\Models\Postamat;
use App\Models\Marketplace;
use App\Models\Review;

class ReviewsService {
    public static function all () {
        return Review::all();
    }

    public static function byPostamatFull (Postamat $postamat) {
        return $postamat->reviews()->with('postamat')->with('thematic')->with('theme')->with('source')->with('emotion')->with('marketplace')->limit(100)->get() ;
    }

    public static function byMarketplaceFull (Marketplace $marketplace) {
        return $marketplace->reviews()->with('postamat')->with('thematic')->with('theme')->with('source')->with('emotion')->with('marketplace')->limit(100)->get() ;
    }

    public static function full () {
        return Review::with('postamat')->with('user')->with('thematic')->with('theme')->with('source')->with('emotion')->with('marketplace')->limit(100)->get();
    }
}
