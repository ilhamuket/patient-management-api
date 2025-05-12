<?php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Return a successful API response.
     */
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return an error API response.
     */
    protected function errorResponse($message, $code)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }

    /**
     * Return a successful API response with no content.
     */
    protected function noContentResponse($message = 'No content')
    {
        return $this->successResponse(null, $message, 204);
    }
}