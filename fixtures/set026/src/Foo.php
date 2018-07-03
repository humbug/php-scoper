<?php

declare(strict_types=1);

namespace Acme;

final class Foo
{
    public function __invoke()
    {
        echo 'OK';
    }
}