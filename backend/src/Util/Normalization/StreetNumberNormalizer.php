<?php

declare(strict_types=1);

namespace App\Util\Normalization;

final class StreetNumberNormalizer
{
    /**
     * Normalize street number with consistent formatting.
     */
    public static function normalize(string $value): string
    {
        // 0. Trim
        $normalized = trim($value);

        // 1. Convert all letters to lowercase
        $normalized = strtolower($normalized);

        // 2. Convert different hyphen types to normal hyphen
        $normalized = str_replace(['–', '—', '−'], '-', $normalized);

        // 3. Remove whitespace around '-' and '/'
        $normalized = preg_replace('/\s*-\s*/', '-', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s*\/\s*/', '/', $normalized) ?? $normalized;

        // 4. Remove multiple special characters and letters in a row
        $normalized = preg_replace('/([a-zA-Z\/\-])\1+/', '$1', $normalized) ?? $normalized;

        // 5. Remove whitespace between numbers and letters
        $normalized = preg_replace('/(\d)\s+([a-zA-Z])/', '$1$2', $normalized) ?? $normalized;

        // 6. Replace multiple spaces with one space
        $normalized = preg_replace('/\s{2,}/', ' ', $normalized) ?? $normalized;

        return $normalized;
    }
}
