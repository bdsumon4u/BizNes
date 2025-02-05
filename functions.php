<?php

use Illuminate\Support\Str;

if (! function_exists('domain')) {
    function domain($url = ''): string
    {
        return parse_url($url ?: config('app.url'), PHP_URL_HOST);
    }
}

if (! function_exists('is_tld')) {
    function is_tld($value): bool
    {
        if (! filter_var($value, FILTER_VALIDATE_DOMAIN)) {
            return false;
        }

        if (! Str::contains($value, '.')) {
            return false;
        }

        return true;
    }
}

if (! function_exists('subdomain')) {
    function subdomain($prefix = ''): string
    {
        return $prefix.'.'.domain();
    }
}
