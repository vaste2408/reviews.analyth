<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PostamatController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ExcelController;
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

Route::get('categories', [ApiController::class, 'categories'])->name('api.categories');
Route::get('thematics', [ApiController::class, 'thematics'])->name('api.thematics');
Route::get('categories/{theme}/thematics', [ApiController::class, 'thematics'])->name('api.category.thematics');
Route::get('with_reviews', [ApiController::class, 'withReviews'])->name('api.with_reviews');
Route::get('emotions', [ApiController::class, 'typeReviews'])->name('api.type_reviews');
Route::get('marketplaces', [ApiController::class, 'marketplaces'])->name('api.marketplaces');

Route::get('postamats', [PostamatController::class, 'index'])->name('api.postamats'); //все постаматы
Route::get('postamats_for_map', [PostamatController::class, 'forMap'])->name('map.postamats'); //ограниченные для карты

Route::get('reviews', [ReviewController::class, 'index'])->name('api.reviews'); //все отзывы
Route::post('reviews', [ReviewController::class, 'store'])->name('api.reviews.create');
Route::post('reviews/{review}', [ReviewController::class, 'update'])->name('api.reviews.update');
Route::get('postamats/{postamat}/reviews', [ReviewController::class, 'postamatReviews'])->name('api.postamat.reviews'); //отзывы постамата
Route::get('marketplaces/{marketplace}/reviews', [ReviewController::class, 'marketplaceReviews'])->name('api.marketplace.reviews'); //отзывы постамата
Route::post('reviews/process', [ReviewController::class, 'process'])->name('api.reviews.process');
Route::post('reviews/{review}/process', [ReviewController::class, 'processReview'])->name('api.review.process');
Route::post('reviews/import', [ReviewController::class, 'import'])->name('api.reviews.import');

Route::post('excel/dashboard', [ExcelController::class, 'dashboard_xls'])->name('api.excel.dashboard'); //экспортим аналитику в эксель
Route::post('excel/postamats/{postamat}/dashboard', [ExcelController::class, 'dashboard_postamat'])->name('api.excel.dashboard.postamat'); //экспортим аналитику в эксель
Route::post('excel/marketplaces/{marketplace}/dashboard', [ExcelController::class, 'dashboard_marketplace'])->name('api.excel.dashboard.marketplace'); //экспортим аналитику в эксель

Route::post('/import',[ReviewController::class, 'import_xls'])->name('api.import');
