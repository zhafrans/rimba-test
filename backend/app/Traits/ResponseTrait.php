<?php

namespace App\Traits;

use App\Enums\ResponseCode;

trait ResponseTrait
{
    protected function successResponse($message, $data = null, ResponseCode $responseCode = ResponseCode::SUCCESS)
    {
        $response = [
            'success' => true,
            'responseCode' => $responseCode->value,
            'message' => $message ?? $responseCode->message(),
            'data' => $data
        ];

        return response()->json($response, $responseCode->code());
    }

    protected function errorResponse($message, $errors = null, ResponseCode $responseCode = ResponseCode::BAD_REQUEST)
    {
        $response = [
            'success' => false,
            'responseCode' => $responseCode->value,
            'message' => $message ?? $responseCode->message()
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $responseCode->code());
    }
}