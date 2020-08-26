<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use AZPHP\AsyncGuzzle\{Framework, Models, Urn};
use Psr\Http\Message\ServerRequestInterface as Request;

$orgs = [
    'urn:lms:org:1' => new Models\Org('urn:lms:org:1', 'District A', ['urn:lms:org:2', 'urn:lms:org:3']),
    'urn:lms:org:2' => new Models\Org('urn:lms:org:2', 'School A1', []),
    'urn:lms:org:3' => new Models\Org('urn:lms:org:3', 'School A2', []),
];

Framework\Server::new()
    ->route('GET', '/v1/orgs/(?<orgUrn>[a-z0-9:]+)', function (Request $request) use (&$orgs) {
        usleep(500000);
        $orgUrn = new Urn('org', $request->getAttribute('params')['orgUrn']);
        if ($orgUrn->isValid() && isset($orgs[$orgUrn->value()])) {
            return new Framework\JsonResponse(['org' => $orgs[$orgUrn->value()]]);
        } else {
            return new Framework\ErrorResponse("Invalid org: {$orgUrn}", 404);
        }
    })
    ->run();
