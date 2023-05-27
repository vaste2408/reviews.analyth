<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Requests\ProcessReviewRequest;
use App\Models\Marketplace;
use App\Models\Postamat;
use App\Models\Review;
use App\Services\SourcesService;
use App\Services\ReviewsService;
use App\Services\AnalythisService;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *      path="/reviews",
     *      operationId="reviews",
     *      tags={"Отзывы"},
     *      summary="Получить все Отзывы",
     *      description="Получить все Отзывы",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function index()
    {
        return ReviewsService::full();
    }

    /**
     * @OA\Get(
     *      path="/postamats/{id}/reviews",
     *      operationId="postamatReviews",
     *      tags={"Постаматы"},
     *      summary="Получить все отзывы Постамата",
     *      description="Получить все отзывы Постамата",
     *      @OA\Parameter(
     *          name="id",
     *          description="id постамата",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function postamatReviews(Postamat $postamat)
    {
        return ReviewsService::byPostamatFull($postamat);
    }

    /**
     * @OA\Get(
     *      path="/marketplaces/{id}/reviews",
     *      operationId="marketplaceReviews",
     *      tags={"Маркетплейсы"},
     *      summary="Получить все отзывы Маркетплейса",
     *      description="Получить все отзывы Маркетплейса",
     *      @OA\Parameter(
     *          name="id",
     *          description="id маркетплейса",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function marketplaceReviews(Marketplace $marketplace)
    {
        return ReviewsService::byMarketplaceFull($marketplace);
    }

    /**
     * @OA\POST(
     *      path="/reviews",
     *      operationId="storeReview",
     *      tags={"Отзывы"},
     *      summary="Создать Отзыв",
     *      description="Создать Отзыв",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                  required={"text", "user_fio", "user_phone", "score"},
     *                  @OA\Property(
     *                     property="text",
     *                     type="string",
     *                     description="Текст отзыва",
     *                 ),
     *                  @OA\Property(
     *                     property="user_fio",
     *                     type="string",
     *                     description="ФИО автора",
     *                 ),
     *                  @OA\Property(
     *                     property="user_phone",
     *                     type="string",
     *                     description="Телефон автора",
     *                 ),
     *                  @OA\Property(
     *                     property="score",
     *                     type="float",
     *                     description="Оценка",
     *                 ),
     *                 @OA\Property(
     *                     property="postamat_id",
     *                     type="integer",
     *                     description="id Постамата",
     *                 ),
     *                  @OA\Property(
     *                     property="thematic_id",
     *                     type="integer",
     *                     description="id Тематики",
     *                 ),
     *                 @OA\Property(
     *                     property="source_id",
     *                     type="integer",
     *                     description="id Источника",
     *                 ),
     *                 @OA\Property(
     *                     property="marketplace_id",
     *                     type="integer",
     *                     description="id Маркетплейса",
     *                 ),
     *                  @OA\Property(
     *                     property="emotion_id",
     *                     type="integer",
     *                     description="id Эмоциональной окраски",
     *                 ),
     *                 @OA\Property(
     *                     property="confirmed",
     *                     type="boolean",
     *                     description="Отметка о проверке модерации",
     *                 ),
     *                  @OA\Property(
     *                     property="need_reaction",
     *                     type="boolean",
     *                     description="Отметка о необходимости устранения",
     *                 ),
     *                  @OA\Property(
     *                     property="closed",
     *                     type="boolean",
     *                     description="Отметка об устранении",
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();
        Review::create($data);
        return response()->json(['success' => true], 201);
    }

    /**
     * @OA\POST(
     *      path="/reviews/{id}",
     *      operationId="updateReview",
     *      tags={"Отзывы"},
     *      summary="Обновить Отзыв",
     *      description="Обновить Отзыв",
     *      @OA\Parameter(
     *          name="id",
     *          description="id отзыва",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="score",
     *                     type="float",
     *                     description="Оценка",
     *                 ),
     *                 @OA\Property(
     *                     property="postamat_id",
     *                     type="integer",
     *                     description="id Постамата",
     *                 ),
     *                  @OA\Property(
     *                     property="thematic_id",
     *                     type="integer",
     *                     description="id Тематики",
     *                 ),
     *                 @OA\Property(
     *                     property="source_id",
     *                     type="integer",
     *                     description="id Источника",
     *                 ),
     *                 @OA\Property(
     *                     property="marketplace_id",
     *                     type="integer",
     *                     description="id Маркетплейса",
     *                 ),
     *                 @OA\Property(
     *                     property="confirmed",
     *                     type="boolean",
     *                     description="Отметка о проверке модерации",
     *                 ),
     *                  @OA\Property(
     *                     property="need_reaction",
     *                     type="boolean",
     *                     description="Отметка о необходимости устранения",
     *                 ),
     *                  @OA\Property(
     *                     property="closed",
     *                     type="boolean",
     *                     description="Отметка об устранении",
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $data = $request->validated();
        $review->update($data);
        return response()->json(['success' => true], 201);
    }

    /**
     * @OA\POST(
     *      path="/reviews/process",
     *      operationId="process",
     *      tags={"Аналитика"},
     *      summary="Проанализировать текстовое сообщение",
     *      description="Проанализировать текстовое сообщение",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="text",
     *                     type="string",
     *                     description="Текст для анализа",
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function process(ProcessReviewRequest $request) {
        $data = $request->validated();
        $result = AnalythisService::analyseText($data['text']);
        return response()->json($result, 200);
    }

    /**
     * @OA\POST(
     *      path="/reviews/{id}/process",
     *      operationId="processReview",
     *      tags={"Аналитика"},
     *      summary="Проанализировать Отзыв",
     *      description="Проанализировать Отзыв",
     *      @OA\Parameter(
     *          name="id",
     *          description="id отзыва",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function processReview(Review $review) {
        $result = AnalythisService::analyseText($review->text);
        $review->analythis = json_encode($result);
        if ($result['moderation'] && $result['moderation']['result'] == 0) { // 0 - успех
            $value = $result['moderation']['data'][0]['Статус'];
        }
        if ($result['emotion'] && $result['emotion']['result'] == 0) {
            $value = $result['emotion']['data'][0]['Статус'];
            $review->emotion_id = $value;
            if ($value == -1) {
                $review->need_reaction = true;
            }
        }
        if ($result['marketplace'] && $result['marketplace']['result'] == 0) {
            if (isset($result['marketplace']['data'][0]['Код'])) {
                $value = $result['marketplace']['data'][0]['Код'];
                if ($value != -1) {
                    $review->marketplace_id = $value;
                }
            }
        }
        if ($result['categories'] && $result['categories']['result'] == 0) {
            $review->thematic_id = 1;
            if (isset($result['categories']['data'][0]['Код'])) {
                $value = $result['categories']['data'][0]['Код'];
                if ($value != -1) {
                    $review->thematic_id = $value;
                }
            }
        }
        $review->save();
        return response()->json($review, 200);
    }

    /**
     * @OA\POST(
     *      path="/reviews/import",
     *      operationId="importReview",
     *      tags={"Отзывы"},
     *      summary="Импорт Отзывов",
     *      description="Импорт Отзывов",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                  required={"source", "list"},
     *                  @OA\Property(
     *                     property="source",
     *                     type="string",
     *                     description="Название Источника данных",
     *                 ),
     *                  @OA\Property(
     *                     property="list",
     *                     type="string",
     *                     description="json массив отзывов. Структуру см. Отзывы / Создать Отзыв",
     *                 ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function import (Request $request) {
        $_source = $request->input('source');
        $source = SourcesService::findOrCreate($_source);
        $data = $request->input('list');
        $list = json_decode($data, true);
        $collection = Review::hydrate($list);
        $collection = $collection->flatten();
        $failed = ReviewsService::import($collection);
        return response()->json(['success' => true, 'failed' => $failed], 201);
    }

    public function import_xls (Request $request) {
        $files = $request->file();
        if (sizeof($files)) {
            foreach ($files as $file) { //файл всегда будет один
                if ($file->extension() === "xlsx") {
                    $filename = $file->getClientOriginalName();
                    $file->storeAs('public/excel', $filename);
                    $result = ReviewsService::process_file($filename);
                    return response()->json(['success' => $result['success'], 'failed' => $result['failed'], 'total' => $result['total']], 201);
                }
            }
        }
        return response()->json(['success' => false, 'message' => 'Ошибка при загрузке файла'], 400);
    }
}
