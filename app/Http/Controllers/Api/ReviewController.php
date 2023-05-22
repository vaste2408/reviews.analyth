<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Requests\ProcessReviewRequest;
use App\Models\Marketplace;
use App\Models\Postamat;
use App\Models\Review;
use App\Services\ReviewsService;
use App\Services\AnalythisService;

class ReviewController extends Controller
{
    /**
     * Все отзывы
     */
    public function index()
    {
        return ReviewsService::full();
    }

    /**
     * Отзывы по постамату
     */
    public function postamatReviews(Postamat $postamat)
    {
        return ReviewsService::byPostamatFull($postamat);
    }

    /**
     * Отзывы по маркетплейсу
     */
    public function marketplaceReviews(Marketplace $marketplace)
    {
        return ReviewsService::byMarketplaceFull($marketplace);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request)
    {
        $data = $request->validated();
        Review::create($data);
        return response()->json(['success' => true], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $data = $request->validated();
        $review->update($data);
        return response()->json(['success' => true], 201);
    }

    /**
     * Анализ текста
     */
    public function process(ProcessReviewRequest $request) {
        $data = $request->validated();
        $result = AnalythisService::analyseText($data['text']);
        return response()->json($result, 200);
    }

    /**
     * Анализ отзыва
     */
    public function processReview(Review $review) {
        $result = AnalythisService::analyseText($review->text);
        //TODO обработка результата
        return response()->json($result, 200);
    }
}
