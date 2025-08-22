<?php

function successResponseHandler($msg, $data=[]){
    return response()->json([
        'success' => true,
        'message' => $msg,
        'data' => $data
    ], 200);
}

function errorResponseHandler($msg, $errors=[])
{
    return response()->json([
        'success' => false,
        'message' => $msg,
        'errors' => $errors
    ], 500);
}

function errorValidationResponseHandler($msg, $errors=[])
{
    return response()->json([
        'success' => false,
        'message' => $msg,
        'errors' => $errors
    ], 400);
}
function unauthorizedResponseHandler($msg=null, $errors=[])
{
    return response()->json([
        'success' => false,
        'message' => $msg??'Unauthorized',
        'errors' => $errors
    ], 401);
}
function forbiddenResponseHandler($msg=null, $errors=[]): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'success' => false,
        'message' => $msg??'Forbidden',
        'errors' => $errors
    ], 403);
}
function notFoundResponseHandler($msg, $errors=[])
{
    return response()->json([
        'success' => false,
        'message' => $msg,
        'errors' => $errors
    ], 404);
}

function  noResponseHandler($msg): \Illuminate\Http\JsonResponse
{
    return response()->json([
        'success' => true,
        'message' => $msg,
    ], 201);
}
