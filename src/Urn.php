<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle;

use function explode;

class Urn
{
    private const PREFIX = 'urn';
    private const NAMESPACE = 'lms';

    private $urn;
    private $type;
    private $parts;

    public function __construct(string $type, string $urn)
    {
        $this->urn = $urn;
        $this->type = $type;
        $this->parts = explode(':', $urn);
    }

    public function isValid(): bool
    {
        if (empty($this->urn)) {
            return false;
        }

        if (count($this->parts) !== 4) {
            return false;
        }

        if ($this->parts[0] !== self::PREFIX) {
            return false;
        }

        if ($this->parts[1] !== self::NAMESPACE) {
            return false;
        }

        if ($this->parts[2] !== $this->type) {
            return false;
        }

        if (!is_numeric($this->parts[3])) {
            return false;
        }

        return true;
    }

    public function value(): string
    {
        return $this->urn;
    }

    public function __toString()
    {
        return $this->urn;
    }
}
