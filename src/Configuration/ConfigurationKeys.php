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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Configuration\Throwable\UnknownConfigurationKey;
use Humbug\PhpScoper\NotInstantiable;

final class ConfigurationKeys
{
    use NotInstantiable;

    public const PREFIX_KEYWORD = 'prefix';
    public const PHP_VERSION_KEYWORD = 'php-version';
    public const OUTPUT_DIR_KEYWORD = 'output-dir';
    public const EXCLUDED_FILES_KEYWORD = 'exclude-files';
    public const FINDER_KEYWORD = 'finders';
    public const PATCHERS_KEYWORD = 'patchers';

    public const EXPOSE_GLOBAL_CONSTANTS_KEYWORD = 'expose-global-constants';
    public const EXPOSE_GLOBAL_CLASSES_KEYWORD = 'expose-global-classes';
    public const EXPOSE_GLOBAL_FUNCTIONS_KEYWORD = 'expose-global-functions';

    public const EXPOSE_NAMESPACES_KEYWORD = 'expose-namespaces';
    public const EXPOSE_CLASSES_SYMBOLS_KEYWORD = 'expose-classes';
    public const EXPOSE_FUNCTIONS_SYMBOLS_KEYWORD = 'expose-functions';
    public const EXPOSE_CONSTANTS_SYMBOLS_KEYWORD = 'expose-constants';

    public const EXCLUDE_NAMESPACES_KEYWORD = 'exclude-namespaces';
    public const CLASSES_INTERNAL_SYMBOLS_KEYWORD = 'exclude-classes';
    public const FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD = 'exclude-functions';
    public const CONSTANTS_INTERNAL_SYMBOLS_KEYWORD = 'exclude-constants';

    public const KEYWORDS = [
        self::PREFIX_KEYWORD,
        self::PHP_VERSION_KEYWORD,
        self::OUTPUT_DIR_KEYWORD,
        self::EXCLUDED_FILES_KEYWORD,
        self::FINDER_KEYWORD,
        self::PATCHERS_KEYWORD,
        self::EXPOSE_GLOBAL_CONSTANTS_KEYWORD,
        self::EXPOSE_GLOBAL_CLASSES_KEYWORD,
        self::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD,
        self::EXPOSE_NAMESPACES_KEYWORD,
        self::EXPOSE_CLASSES_SYMBOLS_KEYWORD,
        self::EXPOSE_FUNCTIONS_SYMBOLS_KEYWORD,
        self::EXPOSE_CONSTANTS_SYMBOLS_KEYWORD,
        self::EXCLUDE_NAMESPACES_KEYWORD,
        self::CLASSES_INTERNAL_SYMBOLS_KEYWORD,
        self::FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD,
        self::CONSTANTS_INTERNAL_SYMBOLS_KEYWORD,
    ];

    /**
     * @throws UnknownConfigurationKey
     */
    public static function assertIsValidKey(string $key): void
    {
        if (!self::isValidateKey($key)) {
            throw UnknownConfigurationKey::forKey($key);
        }
    }

    public static function isValidateKey(string $key): bool
    {
        return in_array($key, self::KEYWORDS, true);
    }
}
