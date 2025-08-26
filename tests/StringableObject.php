<?php

declare(strict_types=1);

namespace Phrity\Logger\Console\Test;

use Stringable;

class StringableObject implements Stringable
{
    private string $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function __toString(): string
    {
        return $this->string;
    }
}
