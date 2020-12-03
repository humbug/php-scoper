<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'meta' => [
        'title' => 'Parentheses',
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'parentheses' => <<<'PHP'
<?php
declare(strict_types=1);

namespace Acme;

class Parentheses
{
    private $invert = 0;

    public function invert($inverted = null)
    {
        $this->invert = (\func_num_args() === 0 ? !$this->invert : $inverted) ? 1 : 0;

        return $this;
    }
}
----
<?php

declare (strict_types=1);
namespace Humbug\Acme;

class Parentheses
{
    private $invert = 0;
    public function invert($inverted = null)
    {
        $this->invert = (\func_num_args() === 0 ? !$this->invert : $inverted) ? 1 : 0;
        return $this;
    }
}

PHP
];
