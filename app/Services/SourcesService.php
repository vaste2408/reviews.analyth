<?php

namespace App\Services;

use App\Models\Source;

class SourcesService {
    public static function findOrCreate ($name) {
        $source = Source::firstOrCreate(['name' => $name]);
        return $source;
    }
}
