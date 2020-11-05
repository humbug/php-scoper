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

namespace Humbug\PhpScoper\Console;

use Humbug\PhpScoper\Container;
use Symfony\Component\Console\Application as SymfonyApplication;
use function Humbug\PhpScoper\get_php_scoper_version;
use function sprintf;
use function strpos;
use function trim;

final class Application extends SymfonyApplication
{
    private const LOGO = <<<'ASCII'

    ____  __  ______     _____
   / __ \/ / / / __ \   / ___/_________  ____  ___  _____
  / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
 / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /
/_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/
                                     /_/


ASCII;

    private $container;
    private $releaseDate;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Container $container,
        string $name = 'Box',
        ?string $version = null,
        string $releaseDate = '@release-date@'
    ) {
        $this->container = $container;
        $this->releaseDate = false === strpos($releaseDate, '@') ? $releaseDate : '';

        parent::__construct($name, $version ?? get_php_scoper_version());
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @inheritdoc
     */
    public function getLongVersion(): string
    {
        return trim(
            sprintf(
                '<info>%s</info> version <comment>%s</comment> %s',
                $this->getName(),
                $this->getVersion(),
                $this->releaseDate
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getHelp(): string
    {
        return self::LOGO.parent::getHelp();
    }
}
