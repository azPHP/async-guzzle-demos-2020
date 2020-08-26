<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use AZPHP\AsyncGuzzle\Timer;
use GuzzleHttp\Client;

$timer = Timer::start();

$client = new Client(['base_uri' => 'https://httpbin.org']);
$client->get('/delay/2');
$client->get('/delay/3');
echo "Done.\n";

$timer->stop();
