<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use AZPHP\AsyncGuzzle\Timer;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$timer = Timer::start();

$client = new Client(['base_uri' => 'https://httpbin.org']);
$promise = Promise\all([
    $client->getAsync('/delay/2'),
    $client->getAsync('/delay/3'),
]);
$promise->wait();

$timer->stop();
