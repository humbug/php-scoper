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

use Fidry\Console\Application\Application as FidryApplication;
use Humbug\PhpScoper\Console\Command\AddPrefixCommand;
use Humbug\PhpScoper\Console\Command\InitCommand;
use Humbug\PhpScoper\Console\Command\InspectCommand;
use Humbug\PhpScoper\Console\Command\InspectSymbolCommand;
use Humbug\PhpScoper\Container;
use Symfony\Component\Console\Helper\FormatterHelper;
use function Humbug\PhpScoper\get_php_scoper_version;
use function sprintf;
use function str_contains;
use function trim;

/**
 * @private
 * @codeCoverageIgnore
 */
final class Application implements FidryApplication
{
    private const LOGO = <<<'ASCII'

            ____  __  ______     _____
           / __ \/ / / / __ \   / ___/_________  ____  ___  _____
          / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
         / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /
        /_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/
                                             /_/


        ASCII;

    private const RELEASE_DATE_PLACEHOLDER = '@release-date@';

    public static function create(): self
    {
        return new self(
            new Container(),
            get_php_scoper_version(),
            !str_contains(self::RELEASE_DATE_PLACEHOLDER, '@')
              ? self::RELEASE_DATE_PLACEHOLDER
              : '',
            true,
            true,
        );
    }

    public function __construct(
        private readonly Container $container,
        private readonly string $version,
        private readonly string $releaseDate,
        private readonly bool $isAutoExitEnabled,
        private readonly bool $areExceptionsCaught,
    ) {
    }

    public function getName(): string
    {
        return 'PhpScoper';
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getLongVersion(): string
    {
        return trim(
            sprintf(
                '<info>%s</info> version <comment>%s</comment> %s',
                $this->getName(),
                $this->getVersion(),
                $this->releaseDate,
            ),
        );
    }

    public function getHelp(): string
    {
        return self::LOGO.$this->getLongVersion();
    }

    public function getCommands(): array
    {
        return [
            new AddPrefixCommand(
                $this->container->getFileSystem(),
                $this->container->getScoperFactory(),
                $this,
                $this->container->getConfigurationFactory(),
            ),
            new InspectCommand(
                $this->container->getFileSystem(),
                $this->container->getScoperFactory(),
                $this->container->getConfigurationFactory(),
            ),
            new InspectSymbolCommand(
                $this->container->getFileSystem(),
                $this->container->getConfigurationFactory(),
                $this->container->getEnrichedReflectorFactory(),
            ),
            new InitCommand(
                $this->container->getFileSystem(),
                new FormatterHelper(),
            ),
        ];
    }

    public function getDefaultCommand(): string
    {
        return 'list';
    }

    public function isAutoExitEnabled(): bool
    {
        return $this->isAutoExitEnabled;
    }

    public function areExceptionsCaught(): bool
    {
        return $this->areExceptionsCaught;
    }
}
