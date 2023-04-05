<?php

use App\Http\Controllers\Api\PostamatController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('categories', function () {
    return [['label' => '1', 'value' => '2']];
})->name('api.categories');
Route::get('with_reviews', function () {
    return [['label' => 'С отзывами', 'value' => 1], ['label' => 'Без отзывов', 'value' => 0]];
})->name('api.with_reviews');
Route::get('type_reviews', function () {
    return [['label' => 'Позитивные', 'value' => 1], ['label' => 'Негативные', 'value' => -1], ['label' => 'Нейтральные', 'value' => 0]];
})->name('api.type_reviews');
Route::get('postamats', [PostamatController::class, 'index'])->name('api.postamats'); //все постаматы
Route::get('postamats_for_map', [PostamatController::class, 'forMap'])->name('map.postamats'); //ограниченные для карты
Route::get('postamat/{postamat}/reviews', [ReviewController::class, 'index'])->name('api.postamat.reviews'); //отзывы постамата
Route::get('reviews', [ReviewController::class, 'index'])->name('api.reviews'); //все отзывы
Route::post('reviews', [ReviewController::class, 'store'])->name('api.reviews.create');
