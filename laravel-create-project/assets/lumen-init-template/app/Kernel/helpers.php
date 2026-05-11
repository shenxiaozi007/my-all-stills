<?php

if (!function_exists('get_page_size')) {
    function get_page_size(int $default = 20): int
    {
        $pageSize = (int) app('request')->get('page_size', 0);

        return $pageSize > 0 ? $pageSize : $default;
    }
}

if (!function_exists('get_real_ip')) {
    function get_real_ip(): string
    {
        return app('request')->getClientIp() ?: '';
    }
}

if (!function_exists('get_user_agent')) {
    function get_user_agent(): string
    {
        return app('request')->userAgent() ?: '';
    }
}

if (!function_exists('get_file_ext')) {
    function get_file_ext(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION) ?: '';
    }
}
