<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use AZPHP\AsyncGuzzle\{Framework, Models, Urn};
use Psr\Http\Message\ServerRequestInterface as Request;

$people = [
    new Models\Person('urn:lms:person:1', 'Jean', 'Grey', [
        new Models\Affiliation('urn:lms:org:2', 'urn:lms:person:1', 'teacher'),
    ]),
    new Models\Person('urn:lms:person:2', 'Scott', 'Summers', [
        new Models\Affiliation('urn:lms:org:2', 'urn:lms:person:2', 'teacher'),
    ]),
    new Models\Person('urn:lms:person:3', 'Alex', 'Summers', [
        new Models\Affiliation('urn:lms:org:3', 'urn:lms:person:3', 'teacher'),
    ]),
    new Models\Person('urn:lms:person:4', 'Ororo', 'Munroe', [
        new Models\Affiliation('urn:lms:org:3', 'urn:lms:person:4', 'teacher'),
    ]),
];

Framework\Server::new()
    ->route('POST', '/v1/people/search', function (Request $request) use (&$people) {
        usleep(3000000);
        $params = $request->getParsedBody();

        $role = $params['role'] ?? null;
        if (empty($role) || !in_array($role, ['student', 'teacher'])) {
            return new Framework\ErrorResponse("Missing/invalid org: {$role}", 400);
        }

        $query = $params['query'] ?? null;
        if (empty($query)) {
            return new Framework\ErrorResponse("Missing query", 400);
        }
        $query = strtolower($query);

        /** @var Urn[] $orgUrns */
        $orgUrns = array_map(function (string $orgUrn) {
            return new Urn('org', $orgUrn);
        }, $params['orgUrns'] ?? []);
        $urnsAllValid = array_reduce($orgUrns, function (bool $stillValid, Urn $urn) {
            return $stillValid && $urn->isValid();
        }, true);
        if (empty($orgUrns) || !$urnsAllValid) {
            return new Framework\ErrorResponse("Missing/invalid org URNs", 400);
        }

        $filtered = array_filter($people, function (Models\Person $person) use ($role, $query, $orgUrns) {
            $matchesAffiliation = false;
            foreach ($orgUrns as $orgUrn) {
                /** @var Models\Affiliation $affilation */
                foreach ($person->affiliations as $affilation) {
                    if ($orgUrn->value() === $affilation->orgUrn && $affilation->role === $role) {
                        $matchesAffiliation = true;
                        break(2);
                    }
                }
            }

            return $matchesAffiliation && (
                strpos(strtolower($person->firstName), $query) !== false
                || strpos(strtolower($person->lastName), $query) !== false
            );
        });

        return new Framework\JsonResponse(['people' => $filtered]);
    })
    ->run();
