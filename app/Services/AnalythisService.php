<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class AnalythisService {
    public const ANALYTHIC_SERVER_URL = 'http://192.168.77.176:5000';

    public static function moderateText (string $text) {
        $response = Http::post(self::ANALYTHIC_SERVER_URL . '/moderation/texts', [
            'data' => [$text]
        ]);
        return $response->json();
    }

    public static function defineEmotionText (string $text) {
        $response = Http::post(self::ANALYTHIC_SERVER_URL . '/color/texts', [
            'data' => [$text]
        ]);
        return $response->json();
    }

    public static function defineMarketplaceText (string $text) {
        $response = Http::post(self::ANALYTHIC_SERVER_URL . '/company/texts', [
            'data' => [$text]
        ]);
        return $response->json();
    }

    public static function defineCategoriesText (string $text) {
        $response = Http::post(self::ANALYTHIC_SERVER_URL . '/categories/texts', [
            'data' => [$text]
        ]);
        return $response->json();
    }

    public static function analyseText (string $text) {
        $result = [];
        $result['moderation'] = self::moderateText($text);
        $result['emotion'] = self::defineEmotionText($text);
        $result['marketplace'] = self::defineMarketplaceText($text);
        $result['categories'] = self::defineCategoriesText($text);
        return $result;
    }

    public static function calculateTotalTable($reviews = []) {
        return [
            [
                'Характеристика',
                'Всего',
                'Постамат',
                'Расположение',
                'Доставка'
            ],
            [
                'Нейтральных',
                $reviews->filter(function ($item) {
                    return $item->neutral;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->neutral && $item->theme?->id === 4;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->neutral && $item->thematic?->id === 11;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->neutral && $item->theme?->id === 5;
                })->count(),
            ],
            [
                'Негативных',
                $reviews->filter(function ($item) {
                    return $item->negative;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->negative && $item->theme?->id === 4;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->negative && $item->thematic?->id === 11;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->negative && $item->theme?->id === 5;
                })->count(),
            ],
            [
                'Положительных',
                $reviews->filter(function ($item) {
                    return $item->positive;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->positive && $item->theme?->id === 4;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->positive && $item->thematic?->id === 11;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->positive && $item->theme?->id === 5;
                })->count(),
            ],
            [
                'Не идентифицировано',
                $reviews->filter(function ($item) {
                    return $item->undefined;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->undefined && $item->theme?->id === 4;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->undefined && $item->thematic?->id === 11;
                })->count(),
                $reviews->filter(function ($item) {
                    return $item->undefined && $item->theme?->id === 5;
                })->count(),
            ]
        ];
    }
}
