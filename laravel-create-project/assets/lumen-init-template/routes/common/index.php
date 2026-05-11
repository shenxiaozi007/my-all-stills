<?php

$router->get('/health', function () {
    return response()->json([
        'code' => 0,
        'message' => 'success!',
        'data' => [
            'service' => config('service.name'),
            'time' => time(),
        ],
    ]);
});
