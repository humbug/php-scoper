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

namespace Humbug\PhpScoper;

use Humbug\PhpScoper\Configuration\ConfigurationKeys;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use function Safe\file_get_contents;
use function Safe\preg_match_all;

/**
 * @internal
 */
#[CoversNothing]
final class TemplateFileTest extends TestCase
{
    private const TEMPLATE_PATH = __DIR__.'/../src/scoper.inc.php.tpl';

    public function test_the_template_file_contains_an_entry_for_each_configuration_key(): void
    {
        $templateConfigKeys = self::retrieveKeys();

        self::assertEqualsCanonicalizing(
            ConfigurationKeys::KEYWORDS,
            $templateConfigKeys,
        );
    }

    /**
     * @return list<string>
     */
    private static function retrieveKeys(): array
    {
        $template = file_get_contents(self::TEMPLATE_PATH);

        return preg_match_all('/\'(.*?)\' => .*/', $template, $matches)
            ? $matches[1]
            : [];
    }
}
