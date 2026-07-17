<?php

if (!function_exists('inr')) {
    /**
     * Indian-style number formatting: 2,30,151.00
     */
    function inr($n, int $decimals = 2): string
    {
        $n = round((float) $n, $decimals);
        $neg = $n < 0 ? '-' : '';
        $n = abs($n);

        $int = (string) floor($n);
        $dec = $decimals > 0 ? substr(number_format($n - floor($n), $decimals), 1) : '';

        if (strlen($int) > 3) {
            $last3 = substr($int, -3);
            $rest = substr($int, 0, -3);
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $int = $rest . ',' . $last3;
        }

        return $neg . $int . $dec;
    }
}