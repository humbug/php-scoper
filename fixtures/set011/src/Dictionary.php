<?php

declare(strict_types=1);

namespace Set011;

abstract class Dictionary
{
    /**
     * @return string[]
     */
    abstract public function provideWords(): array;
}