<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /**
     * @param array|object $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonResponse(array|object $data, int $status = Response::HTTP_OK)
    {
        return response()->json($data, $status);
    }

    /**
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(string $message, int $status = Response::HTTP_BAD_REQUEST)
    {
        return response()->json(['error' => $message], $status);
    }

    /**
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse(string $message, int $status = Response::HTTP_OK)
    {
        return response()->json(['message' => $message], $status);
    }
}
