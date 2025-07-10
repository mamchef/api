<?php

namespace App\Http\Controllers;

abstract class Controller
{
    const PAGINATION = 25;

    public function validPagination(int $default = 10, int $min = 1, int $max = 25 , string $paginationName ='per_page'): int
    {
        $per_pge = request($paginationName);
        if (! $per_pge or ! is_numeric($per_pge) or $per_pge < $min or $per_pge > $max) {
            $per_pge = $default;
        }

        return $per_pge;
    }


    public static function sanitizeString($string): string
    {
        // Remove HTML tags
        $string = strip_tags($string);

        // Remove extra whitespace
        $string = trim(preg_replace('/\s+/', ' ', $string));

        // Encode special characters
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

        return $string;
    }
}
