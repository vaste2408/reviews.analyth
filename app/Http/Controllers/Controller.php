<?php

namespace App\Http\Controllers;

use App\Models\Postamat;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index() {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register')
        ]);
    }

    public function dashboard(Postamat $postamat = null) {
        return Inertia::render('Dashboard', [
            'postamat' => $postamat
        ]);
    }

    public function info(Postamat $postamat) {
        return Inertia::render('Postamat', [
            'postamat' => $postamat
        ]);
    }
}
