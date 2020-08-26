<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle\Models;

use AZPHP\AsyncGuzzle\Model;

class Affiliation extends Model
{
    public function __construct(string $orgUrn, string $personUrn, string $role)
    {
        parent::__construct(compact('orgUrn', 'personUrn', 'role'));
    }
}
