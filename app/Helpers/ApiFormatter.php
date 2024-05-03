<?php

namespace App\Helpers;

class ApiFormatter
{
public static function sendResponse($status = NULL, $success = false, $message =NULL, $data = [], $relatedData = [])
{
    return response()-> json([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'related_data' => $relatedData,
    ], $status);
}
}