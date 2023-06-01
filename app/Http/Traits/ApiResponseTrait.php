<?php

namespace App\Http\Traits;

use Illuminate\Http\Response;

trait ApiResponseTrait
{
    /**
     * Response success with data
     * @param $data
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $message = '', $statusCode = Response::HTTP_OK)
    {
        $response = [
            'status' => 'Success',
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * Response error with message
     * @param $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $statusCode)
    {
        $response = [
            'status' => 'Error',
            'message' => $message,
            'data' => []
        ];

        return response()->json($response, $statusCode);
    }
}
