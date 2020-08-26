<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle\Models;

use AZPHP\AsyncGuzzle\Model;

class Org extends Model
{
    public function __construct(string $urn, string $name, array $children)
    {
        parent::__construct(compact('urn', 'name', 'children'));
    }
}
