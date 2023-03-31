<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostamatRequest;
use App\Http\Requests\UpdatePostamatRequest;
use App\Models\Postamat;

class PostamatController extends Controller
{
    public function index()
    {
        return Postamat::where(['city' => 'Москва'])->take(100)->get();
    }
}
