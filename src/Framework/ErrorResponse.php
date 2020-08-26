<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle\Framework;

class ErrorResponse extends JsonResponse
{
    public function __construct(string $message, int $status = 200)
    {
        parent::__construct(['error' => compact('status', 'message')], $status);
    }
}
