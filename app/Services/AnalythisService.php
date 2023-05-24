<?php

namespace App\Services;

class AnalythisService {
    public static function analyseText (string $text) {
        //TODO запрос в аналитическую часть
        return $text;
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
