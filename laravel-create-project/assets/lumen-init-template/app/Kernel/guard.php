<?php

if (!function_exists('management_auth')) {
    function management_auth()
    {
        return app('auth')->guard('management');
    }
}

if (!function_exists('management_auth_info')) {
    function management_auth_info(string $column = '', $default = null)
    {
        $user = optional(management_auth()->user())->toArray() ?: [];

        return $column ? array_get($user, $column, $default) : $user;
    }
}
