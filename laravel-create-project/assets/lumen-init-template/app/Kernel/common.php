<?php

if (!function_exists('trim_any')) {
    function trim_any($data, string $charList = " \t\n\r\0\x0B")
    {
        if (is_string($data)) {
            return trim($data, $charList);
        }

        if (is_array($data) || is_object($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = trim_any($value, $charList);
            }
        }

        return $data;
    }
}

if (!function_exists('get_now')) {
    function get_now(): int
    {
        return time();
    }
}

if (!function_exists('get_format_now')) {
    function get_format_now(): string
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('get_http_host')) {
    function get_http_host(): string
    {
        return app('request')->server('HTTP_HOST') ?: array_get($_SERVER, 'HTTP_HOST', '');
    }
}
