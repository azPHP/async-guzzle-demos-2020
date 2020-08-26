<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle;

use JsonSerializable;

abstract class Model implements JsonSerializable
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function __isset(string $name)
    {
        return isset($this->data[$name]);
    }
}
