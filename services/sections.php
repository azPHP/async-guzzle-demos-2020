<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use AZPHP\AsyncGuzzle\{Framework, Models, Urn};
use Psr\Http\Message\ServerRequestInterface as Request;

$sections = [
    'urn:lms:org:2' => [
        new Models\Section('urn:lms:section:1', '3rd Grade Math (1)', ['urn:lms:course:1'], [
            new Models\Enrollment('urn:lms:section:1', 'urn:lms:person:1', 'teacher'),
            new Models\Enrollment('urn:lms:section:1', 'urn:lms:person:2', 'teacher'),
        ]),
        new Models\Section('urn:lms:section:2', '3rd Grade ELA (1)', ['urn:lms:course:2'], [
            new Models\Enrollment('urn:lms:section:2', 'urn:lms:person:1', 'teacher'),
            new Models\Enrollment('urn:lms:section:1', 'urn:lms:person:2', 'teacher'),
        ]),
        new Models\Section('urn:lms:section:3', '3rd Grade Science', ['urn:lms:course:3'], [
            new Models\Enrollment('urn:lms:section:3', 'urn:lms:person:1', 'teacher'),
        ]),
        new Models\Section('urn:lms:section:4', '3rd Grade PE', ['urn:lms:course:4'], [
            new Models\Enrollment('urn:lms:section:4', 'urn:lms:person:2', 'teacher'),
        ]),
    ],
    'urn:lms:org:3' => [
        new Models\Section('urn:lms:section:5', '3rd Grade Math (2)', ['urn:lms:course:1'], [
            new Models\Enrollment('urn:lms:section:5', 'urn:lms:person:3', 'teacher'),
        ]),
        new Models\Section('urn:lms:section:6', '3rd Grade ELA (2)', ['urn:lms:course:2'], [
            new Models\Enrollment('urn:lms:section:6', 'urn:lms:person:3', 'teacher'),
        ]),
        new Models\Section('urn:lms:section:7', '3rd Grade Music', ['urn:lms:course:5'], [
            new Models\Enrollment('urn:lms:section:7', 'urn:lms:person:3', 'teacher'),
        ]),
        new Models\Section('urn:lms:section:8', '3rd Grade Math (3)', ['urn:lms:course:1'], [
            new Models\Enrollment('urn:lms:section:8', 'urn:lms:person:4', 'teacher'),
        ]),
        new Models\Section('urn:lms:section:9', '3rd Grade ELA (3)', ['urn:lms:course:2'], [
            new Models\Enrollment('urn:lms:section:9', 'urn:lms:person:4', 'teacher'),
        ]),
        new Models\Section('urn:lms:section:10', '3rd Grade Social Studies', ['urn:lms:course:6'], [
            new Models\Enrollment('urn:lms:section:10', 'urn:lms:person:4', 'teacher'),
        ]),
    ],
];

Framework\Server::new()
    ->route('GET', '/v1/sections/(?<orgUrn>[a-z0-9:]+)', function (Request $request) use (&$sections) {
        usleep(1500000);
        $orgUrn = new Urn('org', $request->getAttribute('params')['orgUrn']);
        if ($orgUrn->isValid() && isset($sections[$orgUrn->value()])) {
            return new Framework\JsonResponse(['sections' => $sections[$orgUrn->value()]]);
        } else {
            return new Framework\ErrorResponse("Invalid org: {$orgUrn}", 404);
        }
    })
    ->run();
