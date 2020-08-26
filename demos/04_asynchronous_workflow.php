<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use AZPHP\AsyncGuzzle\Timer;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface as Response;

$timer = Timer::start();

// Create clients
$handler = HandlerStack::create();
// $handler->setHandler(new GuzzleHttp\Handler\CurlHandler()); // Sync cURL handler
$orgsClient = new Client(['base_uri' => 'http://localhost:9001', 'handler' => $handler]);
$peopleClient = new Client(['base_uri' => 'http://localhost:9002', 'handler' => $handler]);
$sectionsClient = new Client(['base_uri' => 'http://localhost:9003', 'handler' => $handler]);

// Create inputs
$districtUrn = 'urn:lms:org:1';
$courseUrn = 'urn:lms:course:1';
$teacherQuery = 'Summers';

// Step 1: Get Schools in District.
$schoolUrnsPromise = $orgsClient->requestAsync('GET', "/v1/orgs/{$districtUrn}")
    ->then(function (Response $response) {
        $result = json_decode($response->getBody()->getContents(), true);
        return $result['org']['children'] ?? [];
    });

// Step 2a: Get Teachers in District Matching Name Query.
$teachersPromise = $schoolUrnsPromise
    ->then(function (array $schoolUrns) use ($peopleClient, $teacherQuery) {
        return $peopleClient->requestAsync('POST', '/v1/people/search', [
            'form_params' => [
                'orgUrns' => $schoolUrns,
                'role' => 'teacher',
                'query' => $teacherQuery,
            ],
        ]);
    })
    ->then(function (Response $response) {
        $result = json_decode($response->getBody()->getContents(), true);
        return array_map(function (array $person) {
            unset($person['affiliations']);
            return $person;
        }, $result['people']);
    });

// Step 2b: Get Teacher URNs Teaching Matching Course.
$enrolledTeacherUrnsPromise = $schoolUrnsPromise
    ->then(function (array $schoolUrns) use ($sectionsClient) {
        $sectionsPromises = [];
        foreach ($schoolUrns as $schoolUrn) {
            $sectionsPromises[] = $sectionsClient->requestAsync('GET', "/v1/sections/{$schoolUrn}");
        }

        return Promise\all($sectionsPromises);
    })
    ->then(function (array $responses) use ($courseUrn) {
        // Get a list of sections matching the desired course.
        $sections = [];
        /** @var Response $response */
        foreach ($responses as $response) {
            $result = json_decode($response->getBody()->getContents(), true);
            foreach ($result['sections'] as $section) {
                if (in_array($courseUrn, $section['courses'], true)) {
                    $sections[] = $section;
                }
            }
        }

        return $sections;
    })
    ->then(function (array $sections) {
        // Get a list of teacher URNs from the matching sections.
        $teacherUrns = [];
        foreach ($sections as $section) {
            foreach ($section['instructorEnrollments'] as $enrollment) {
                $teacherUrns[] = $enrollment['personUrn'];
            }
        }

        return array_unique($teacherUrns);
    });

// Step 3: Filter Teachers Matching Name Query By Teachers Teaching Matching Course.
$teachersListPromise = Promise\all([$teachersPromise, $enrolledTeacherUrnsPromise])
    ->then(function (array $results) {
        [$matchingTeachers, $enrolledTeacherUrns] = $results;
        return array_filter($matchingTeachers, function (array $teacher) use (&$enrolledTeacherUrns) {
            return in_array($teacher['urn'], $enrolledTeacherUrns, true);
        });
    })
    ->otherwise(function (Throwable $error) {
        echo "Error: {$error->getMessage()}\n";
        return [];
    });

$teachersList = $teachersListPromise->wait();
echo json_encode($teachersList, JSON_PRETTY_PRINT) . "\n";

$timer->stop();
