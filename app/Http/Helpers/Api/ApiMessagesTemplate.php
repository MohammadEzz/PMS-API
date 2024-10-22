<?php

namespace App\Http\Helpers\Api;

class ApiMessagesTemplate {

    public static function apiResponseDefaultMessage($is_success, $code, $message, $data = null) {
        $result["success"] = $is_success;
        $result["code"] = $code;
        $result["message"] = $message;

        if ($data || is_array($data)) { 
            $result["data"] = $data;
        }

        return $result;
    }

    public static function createResponse($is_success, $code, $message = null, $data = null) {
        $response = static::apiResponseDefaultMessage($is_success, $code, $message, $data);
        return response()->json($response, $code);
    }
}
