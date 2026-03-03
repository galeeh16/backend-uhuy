<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Format exception
     */
    protected function formatError(Exception $e): array 
    {
        return [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ];
    }

    /**
     * Return response json error to client
     */
    protected function responseInternalServerError(): JsonResponse 
    {
        return response()->json([
            'message' => 'Whoops, something went wrong.'
        ], 500);
    }
}
