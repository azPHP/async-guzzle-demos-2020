<?php

declare(strict_types=1);

namespace AZPHP\AsyncGuzzle;

class Timer
{
    private $name;
    private $startTime;

    public static function start(): self
    {
        $timer = new self();
        $timer->name = ucwords(strtr(basename($_SERVER['SCRIPT_FILENAME'], '.php'), ['_' => ' ']));
        $timer->startTime = microtime(true);

        return $timer;
    }

    public function stop(): void
    {
        $stopTime = microtime(true);
        fprintf(STDOUT, "%s: %.3f ms\n", $this->name, ($stopTime - $this->startTime) * 1000);
    }
}
