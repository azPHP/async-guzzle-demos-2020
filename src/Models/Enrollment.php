<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle\Models;

use AZPHP\AsyncGuzzle\Model;

class Enrollment extends Model
{
    public function __construct(string $sectionUrn, string $personUrn, string $role)
    {
        parent::__construct(compact('sectionUrn', 'personUrn', 'role'));
    }
}
