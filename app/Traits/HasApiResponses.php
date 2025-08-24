<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HasApiResponses
{

    public function successResponseHandler($msg, $data = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => $data
        ], 200);
    }

    public function errorResponseHandler($msg, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
            'errors' => $errors
        ], 500);
    }

    public function errorValidationResponseHandler($msg, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
            'errors' => $errors
        ], 400);
    }

    public function unauthorizedResponseHandler($msg = null, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $msg ?? 'Unauthorized',
            'errors' => $errors
        ], 401);
    }

    public function forbiddenResponseHandler($msg = null, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $msg ?? 'Forbidden',
            'errors' => $errors
        ], 403);
    }

    function notFoundResponseHandler($msg, $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $msg,
            'errors' => $errors
        ], 404);
    }

    function createdResponseHandler($msg, $data = []): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => $data
        ], 201);
    }

    function noContentResponseHandler($msg): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $msg,
        ], 204);
    }


    function unprocessableContentResponseHandler($msg): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $msg,
        ], 422);
    }


}
