<?php

declare(strict_types=1);

namespace App\Util\Normalization;

final class StreetNormalizer
{
    /**
     * Normalize street name with consistent formatting.
     */
    public static function normalize(string $value): string
    {
        // Remove leading and trailing whitespace
        $value = trim($value);

        // Normalize case: if there are no lowercase letters, convert to title case
        if (!preg_match('/\p{Ll}/u', $value)) {
            $value = mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        }

        // Normalize different street suffix spellings to "Str./str."
        $value = preg_replace('/\b(straße|strasse|str\.?)\s*$/iu', 'Str.', $value) ?? $value;
        $value = preg_replace('/(?<=\pL)(straße|strasse|str)\s*$/iu', 'str.', $value) ?? $value;

        // Normalize multiple spaces to a single space
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return $value;
    }
}
