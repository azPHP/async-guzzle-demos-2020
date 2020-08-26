<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use AZPHP\AsyncGuzzle\Timer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise;

$timer = Timer::start();

$handler = HandlerStack::create();
$orgsClient = new Client(['base_uri' => 'http://localhost:9001', 'handler' => $handler]);
$peopleClient = new Client(['base_uri' => 'http://localhost:9002', 'handler' => $handler]);

$teachersPromise = Promise\coroutine(function() use ($orgsClient, $peopleClient) {
    $response = yield $orgsClient->requestAsync('GET', "/v1/orgs/urn:lms:org:1");
    $result = json_decode($response->getBody()->getContents(), true);
    $schoolUrns = $result['org']['children'] ?? [];
    if (empty($schoolUrns)) {
        throw new Exception('No schools');
    }

    $response = yield $peopleClient->requestAsync('POST', '/v1/people/search', [
        'form_params' => [
            'orgUrns' => $schoolUrns,
            'role' => 'teacher',
            'query' => 'Summers',
        ],
    ]);
    $result = json_decode($response->getBody()->getContents(), true);
    yield array_values($result['people']);
});

$teachers = $teachersPromise->wait();
echo json_encode($teachers, JSON_PRETTY_PRINT) . "\n";

$timer->stop();
