<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle\Framework;

use GuzzleHttp\Psr7\Response;

class JsonResponse extends Response
{
    public function __construct($data = [], int $status = 200)
    {
        parent::__construct($status, [
            'Content-Type' => 'application/json',
        ], json_encode($data));
    }
}
