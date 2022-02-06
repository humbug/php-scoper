<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

use Humbug\PhpScoper\Configuration\ConfigurationKeys;
use PHPUnit\Framework\TestCase;
use function array_diff;
use function Safe\file_get_contents;
use function Safe\preg_match_all;

/**
 * @coversNothing
 */
final class TemplateFileTest extends TestCase
{
    private const TEMPLATE_PATH = __DIR__.'/../src/scoper.inc.php.tpl';

    public function test_the_template_file_contains_an_entry_for_each_configuration_key(): void
    {
        $templateConfigKeys = self::retrieveKeys();

        self::assertEqualsCanonicalizing(
            array_diff(ConfigurationKeys::KEYWORDS, [ConfigurationKeys::WHITELIST_KEYWORD]),
            $templateConfigKeys,
        );
    }

    /**
     * @return list<string>
     */
    private static function retrieveKeys(): array
    {
        $template = file_get_contents(self::TEMPLATE_PATH);

        if (preg_match_all('/\'(.*?)\' => .*/', $template, $matches)) {
            return $matches[1];
        }

        return [];
    }
}
