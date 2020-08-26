<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle\Models;

use AZPHP\AsyncGuzzle\Model;

class Person extends Model
{
    public function __construct(string $urn, string $firstName, string $lastName, array $affiliations)
    {
        parent::__construct(compact('urn', 'firstName', 'lastName', 'affiliations'));
    }
}
