<?php

namespace App\Services;

use App\Models\Postamat;
use App\Models\Marketplace;
use App\Models\Review;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ReviewsService {
    public static function all () {
        return Review::all();
    }

    public static function byPostamatFull (Postamat $postamat) {
        return $postamat->reviews()->with('postamat')->with('thematic')->with('theme')->with('source')->with('emotion')->with('marketplace')->limit(100)->get() ;
    }

    public static function byMarketplaceFull (Marketplace $marketplace) {
        return $marketplace->reviews()->with('postamat')->with('thematic')->with('theme')->with('source')->with('emotion')->with('marketplace')->limit(100)->get() ;
    }

    public static function full () {
        return Review::with('postamat')->with('user')->with('thematic')->with('theme')->with('source')->with('emotion')->with('marketplace')->limit(100)->get();
    }

    public static function import ($collection) {
        $failed = [];
        foreach ($collection as $review) {
            Review::create($review->toArray());
        }
        return $failed;
    }

    public static function process_file($filename) {
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(public_path('/storage/excel/'.$filename)); //public_path('/storage/excel/'.$file_name.'.xlsx') //__DIR__ . '/' . $filename . '.xlsx'
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $list = $sheet->toArray();
        $collection = [];
        $failed = [];
        foreach ($list as $ind => $element) {
            if ($ind == 0)
                continue;
            $collection[] = [
                'text' => $element[0], //Комментарий
                'created_at' => $element[1], //Дата
                'score' => $element[2], //Оценка
                'order_price' => $element[3], //Сумма заказа
            ];
        }
        $collection = Review::hydrate($collection);
        $failed = self::import($collection);
        return ['failed' => sizeof($failed), 'total' => sizeof($collection), 'success' => true];
    }
}
