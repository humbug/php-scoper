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

namespace Humbug\PhpScoper\Patcher;

use function array_key_exists;
use function str_contains;
use function str_replace;
use function strlen;

final class SymfonyParentTraitPatcher implements Patcher
{
    private const PATHS = [
        'src/Symfony/Component/DependencyInjection/Loader/Configurator/Traits/ParentTrait.php',
        'symfony/dependency-injection/Loader/Configurator/Traits/ParentTrait.php',
    ];

    /**
     * @var array<string, list<string>>
     */
    private array $replacement = [];

    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        if (!self::isSupportedFile($filePath)) {
            return $contents;
        }

        return str_replace(
            [
                '$definition = \substr_replace($definition, \'53\', 2, 2);',
                '$definition = substr_replace($definition, \'53\', 2, 2);',
                '$definition = \substr_replace($definition, \'Child\', 44, 0);',
                '$definition = substr_replace($definition, \'Child\', 44, 0);',
            ],
            $this->getReplacement($prefix),
            $contents,
        );
    }

    private static function isSupportedFile(string $filePath): bool
    {
        foreach (self::PATHS as $path) {
            if (str_contains($filePath, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    private function getReplacement(string $prefix): array
    {
        if (!array_key_exists($prefix, $this->replacement)) {
            $this->replacement[$prefix] = self::generateReplacement($prefix);
        }

        return $this->replacement[$prefix];
    }

    /**
     * @return list<string>
     */
    private static function generateReplacement(string $prefix): array
    {
        $prefixLength = strlen($prefix);
        $newDefinitionFQCNLength = 53 + $prefixLength + 1;
        $newShortClassNameDefinitionStartPosition = 44 + $prefixLength + 1;

        return [
            str_replace(
                '\'53\'',
                '\''.$newDefinitionFQCNLength.'\'',
                '$definition = \substr_replace($definition, \'53\', 2, 2);',
            ),
            str_replace(
                '\'53\'',
                '\''.$newDefinitionFQCNLength.'\'',
                '$definition = substr_replace($definition, \'53\', 2, 2);',
            ),
            str_replace(
                '44',
                (string) $newShortClassNameDefinitionStartPosition,
                '$definition = \substr_replace($definition, \'Child\', 44, 0);',
            ),
            str_replace(
                '44',
                (string) $newShortClassNameDefinitionStartPosition,
                '$definition = substr_replace($definition, \'Child\', 44, 0);',
            ),
        ];
    }
}
