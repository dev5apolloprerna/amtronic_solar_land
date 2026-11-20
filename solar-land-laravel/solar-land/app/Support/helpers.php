<?php

use Carbon\CarbonInterface;
use Illuminate\Support\HtmlString;

/* View helpers. Loaded from AppServiceProvider::register(). */

if (! function_exists('inr')) {
    function inr($n, int $dec = 0): string
    {
        if ($n === null || $n === '') {
            return '—';
        }
        $n = (float) $n;
        $neg = $n < 0;
        $n = abs($n);
        $int = (string) floor($n);
        $frac = $dec > 0 ? '.'.str_pad((string) round(($n - floor($n)) * (10 ** $dec)), $dec, '0', STR_PAD_LEFT) : '';
        if ($dec === 0) {
            $int = (string) round($n);
        }
        // Indian grouping: 12,34,56,789
        $last3 = substr($int, -3);
        $rest = substr($int, 0, -3);
        if ($rest !== '' && $rest !== false) {
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
            $int = $rest.','.$last3;
        }

        return ($neg ? '-' : '').'₹'.$int.$frac;
    }
}

if (! function_exists('fmt_num')) {
    function fmt_num($n, int $max = 2): string
    {
        if ($n === null || $n === '') {
            return '—';
        }
        $s = number_format((float) $n, $max, '.', ',');

        return str_contains($s, '.') ? rtrim(rtrim($s, '0'), '.') : $s;
    }
}

if (! function_exists('fmt_mw')) {
    function fmt_mw($n): string
    {
        return fmt_num($n, 3).' MW';
    }
}

if (! function_exists('fmt_date')) {
    function fmt_date($d): string
    {
        if (! $d) {
            return '—';
        }

        return ($d instanceof CarbonInterface ? $d : \Carbon\Carbon::parse($d))->format('d M Y');
    }
}

if (! function_exists('fmt_ts')) {
    function fmt_ts($d): string
    {
        return $d ? \Carbon\Carbon::parse($d)->format('d M Y, H:i') : '—';
    }
}

if (! function_exists('chip')) {
    function chip(?string $text, ?string $tone = null): HtmlString
    {
        if ($text === null || $text === '') {
            return new HtmlString('—');
        }
        $tone ??= config('solar.tones.'.$text, '');

        return new HtmlString('<span class="chip '.e($tone).'">'.e($text).'</span>');
    }
}

if (! function_exists('can_do')) {
    function can_do(string $module, string $action = 'view'): bool
    {
        return (bool) auth()->user()?->hasPermission($module, $action);
    }
}

if (! function_exists('opts')) {
    /** Option list from config/solar.php as [value => label]. */
    function opts(string $key): array
    {
        $list = config('solar.options.'.$key, []);

        return array_combine($list, $list);
    }
}
