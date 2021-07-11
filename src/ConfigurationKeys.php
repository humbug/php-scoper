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
    public const WHITELIST_GLOBAL_CONSTANTS_KEYWORD = 'whitelist-global-constants';
    public const WHITELIST_GLOBAL_CLASSES_KEYWORD = 'whitelist-global-classes';
    public const WHITELIST_GLOBAL_FUNCTIONS_KEYWORD = 'whitelist-global-functions';
    public const CLASSES_INTERNAL_SYMBOLS_KEYWORD = 'excluded-classes';
    public const FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD = 'excluded-functions';
    public const CONSTANTS_INTERNAL_SYMBOLS_KEYWORD = 'excluded-constants';

    public const KEYWORDS = [
        self::PREFIX_KEYWORD,
        self::WHITELISTED_FILES_KEYWORD,
        self::FINDER_KEYWORD,
        self::PATCHERS_KEYWORD,
        self::WHITELIST_KEYWORD,
        self::WHITELIST_GLOBAL_CONSTANTS_KEYWORD,
        self::WHITELIST_GLOBAL_CLASSES_KEYWORD,
        self::WHITELIST_GLOBAL_FUNCTIONS_KEYWORD,
        self::CLASSES_INTERNAL_SYMBOLS_KEYWORD,
        self::FUNCTIONS_INTERNAL_SYMBOLS_KEYWORD,
        self::CONSTANTS_INTERNAL_SYMBOLS_KEYWORD,
    ];

    private function __construct() {}
}
