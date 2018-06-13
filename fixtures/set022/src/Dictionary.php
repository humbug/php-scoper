<?php

declare(strict_types=1);

namespace Set022;

interface Dictionary
{
    /**
     * @return string[]
     */
    public function provideWords(): array;
}
