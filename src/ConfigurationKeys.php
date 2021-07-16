<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

// TODO: make it an enum once in PHP 8.1
final class ConfigurationKeys
{
    public const PREFIX_KEYWORD = 'prefix';
    public const WHITELISTED_FILES_KEYWORD = 'files-whitelist';
    public const FINDER_KEYWORD = 'finders';
    public const PATCHERS_KEYWORD = 'patchers';
    public const WHITELIST_KEYWORD = 'whitelist';
    public const EXPOSE_GLOBAL_CONSTANTS_KEYWORD = 'expose-global-constants';
    public const EXPOSE_GLOBAL_CLASSES_KEYWORD = 'expose-global-classes';
    public const EXPOSE_GLOBAL_FUNCTIONS_KEYWORD = 'expose-global-functions';
    public const CLASSES_INTERNAL_SYMBOLS_KEYWORD = 'excluded-classes';
    public const FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD = 'excluded-functions';
    public const CONSTANTS_INTERNAL_SYMBOLS_KEYWORD = 'excluded-constants';

    public const KEYWORDS = [
        self::PREFIX_KEYWORD,
        self::WHITELISTED_FILES_KEYWORD,
        self::FINDER_KEYWORD,
        self::PATCHERS_KEYWORD,
        self::WHITELIST_KEYWORD,
        self::EXPOSE_GLOBAL_CONSTANTS_KEYWORD,
        self::EXPOSE_GLOBAL_CLASSES_KEYWORD,
        self::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD,
        self::CLASSES_INTERNAL_SYMBOLS_KEYWORD,
        self::FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD,
        self::CONSTANTS_INTERNAL_SYMBOLS_KEYWORD,
    ];

    private function __construct() {}
}
