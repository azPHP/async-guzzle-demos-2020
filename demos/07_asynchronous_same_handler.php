<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use AZPHP\AsyncGuzzle\Timer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise;

$timer = Timer::start();

$handler = HandlerStack::create();
$client1 = new Client([
    'handler' => $handler,
    'base_uri' => 'https://httpbin.org',
]);
$client2 = new Client([
    'handler' => $handler,
    'base_uri' => 'https://httpbin.org',
]);
$promise = Promise\all([
    $client1->getAsync('/delay/2'),
    $client2->getAsync('/delay/3'),
]);
$promise->wait();

$timer->stop();
