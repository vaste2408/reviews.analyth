<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use App\Models\Thematic;
use App\Models\Theme;
use App\Models\Emotion;

class ApiController extends BaseController
{
    //use AuthorizesRequests, ValidatesRequests;
    public function categories() {
        return Theme::all()->toArray();
    }

    public function thematics() {
        return Thematic::all()->toArray();
    }

    public function withReviews() {
        return [['label' => 'С отзывами', 'value' => 1], ['label' => 'Без отзывов', 'value' => 0]];
    }

    public function typeReviews() {
        return Emotion::all()->toArray();
    }
}
