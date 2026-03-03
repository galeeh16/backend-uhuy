<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class AlreadyAppliedException extends Exception
{
    public $message;

    public function __construct(string $message = "You have already applied for this job", int $code = 0, Throwable|null $previous = null)
    {
        return parent::__construct($message, $code, $previous);
    }

    public function report(): bool
    {
        // false = jangan dilog sama sekali
        return false;
    }
    
    public function render($request)
    {
        return response()->json([
            'message' => $this->message
        ], 409);
    }
}
