<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use function GuzzleHttp\Promise\promise_for;

$promise = promise_for(random_int(0, 9))
    ->then(function (int $num) {
        if ($num >= 5) {
            return 'hi';
        } else {
            throw new Exception('Too low');
        }
    })->otherwise(function (Exception $ex) {
        echo "Error: {$ex->getMessage()}.\n";
        return 'low';
    })->then(function (string $highOrLow) {
        return strtoupper($highOrLow);
    });

$result = $promise->wait();
echo "Result: {$result}\n"; // 'HIGH' or 'LOW'
