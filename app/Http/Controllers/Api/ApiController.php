<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use App\Models\Thematic;
use App\Models\Theme;
use App\Models\Emotion;
use App\Models\Marketplace;

/**
 *
 */
class ApiController extends BaseController
{
    /**
     * @OA\Get(
     *      path="/categories",
     *      operationId="categories",
     *      tags={"Словари"},
     *      summary="Получить словарь Категории отзывов",
     *      description="Получить словарь Категории отзывов",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function categories() {
        return Theme::all()->toArray();
    }

    /**
     * @OA\Get(
     *      path="/thematics",
     *      operationId="thematics",
     *      tags={"Словари"},
     *      summary="Получить словарь Темы отзывов",
     *      description="Получить словарь Темы отзывов",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function thematics(Theme $theme = null) {
        return $theme ? $theme->thematics : Thematic::all()->toArray();
    }

    public function withReviews() {
        return [['label' => 'С отзывами', 'value' => 1], ['label' => 'Без отзывов', 'value' => 0]];
    }

    /**
     * @OA\Get(
     *      path="/emotions",
     *      operationId="emotions",
     *      tags={"Словари"},
     *      summary="Получить словарь Типы Эмоциональной окраски",
     *      description="Получить словарь Типы Эмоциональной окраски",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function typeReviews() {
        return Emotion::all()->toArray();
    }


    /**
     * @OA\Get(
     *      path="/marketplaces",
     *      operationId="marketplaces",
     *      tags={"Маркетплейсы"},
     *      summary="Получить все Маркетплейсы",
     *      description="Получить все Маркетплейсы",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function marketplaces() {
        return Marketplace::all()->toArray();
    }
}
