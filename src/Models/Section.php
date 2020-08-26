<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle\Models;

use AZPHP\AsyncGuzzle\Model;

class Section extends Model
{
    public function __construct(string $urn, string $name, array $courses, array $instructorEnrollments)
    {
        parent::__construct(compact('urn', 'name', 'courses', 'instructorEnrollments'));
    }
}
