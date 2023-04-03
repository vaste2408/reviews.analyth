<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PostamatService;

class PostamatController extends Controller
{
    public function index()
    {
        return PostamatService::Postamats();
    }

    public function forMap()
    {
        return PostamatService::PostamatsForMap();
    }
}
