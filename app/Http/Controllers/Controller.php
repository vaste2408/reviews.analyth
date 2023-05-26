<?php

namespace App\Http\Controllers;

use App\Models\Postamat;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/**
 * @OA\OpenApi(
 *  @OA\Info(
 *      title="Библиотека доступных API",
 *      version="1.0.0",
 *      description="Описание API для сервиса ЛЦТ 4 DeCODE-HS",
 *      @OA\Contact(
 *          url="https://t.me/andrew_vaste"
 *      )
 *  ),
 *  @OA\Server(
 *      description="Получить список API",
 *      url="http://lct4.decodehs/api/"
 *  ),
 *  @OA\PathItem(
 *      path="/"
 *  )
 * )
 */
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
        return Inertia::render('Dashboard copy', [
            'postamat' => $postamat
        ]);
    }

    public function info(Postamat $postamat) {
        return Inertia::render('Card', [
            'postamat' => $postamat
        ]);
    }
}
