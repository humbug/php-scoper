<?php

/** @noinspection ClassConstantCanBeUsedInspection */

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

namespace Humbug\PhpScoper\Symbol;

use JetBrains\PHPStormStub\PhpStormStubsMap;
use function array_keys;
use function array_merge;

/**
 * @private
 */
final readonly class Reflector
{
    private const MISSING_CLASSES = [
        'Deprecated',

        // https://youtrack.jetbrains.com/issue/WI-29503
        'MongoInsertBatch',
        'MongoDeleteBatch',

        // PHP 8.4
        // TODO: to report
        'Pdo\DbLib',
        'Pdo\Firebird',
        'Pdo\Odbc',
        'Pdo\Pgsql',
    ];

    private const MISSING_FUNCTIONS = [
        // https://youtrack.jetbrains.com/issue/WI-53323
        'tideways_xhprof_enable',
        'tideways_xhprof_disable',

        // https://youtrack.jetbrains.com/issue/WI-29503
        'bson_encode',
        'bson_decode',

        // https://youtrack.jetbrains.com/issue/WI-74920
        'swoole_async_dns_lookup',
        'swoole_async_readfile',
        'swoole_async_write',
        'swoole_async_writefile',

        // https://youtrack.jetbrains.com/issue/WI-74922
        'ssdeep_fuzzy_compare',
        'ssdeep_fuzzy_hash',
        'ssdeep_fuzzy_hash_filename',

        // https://youtrack.jetbrains.com/issue/WI-74918
        'ps_add_bookmark',
        'ps_add_launchlink',
        'ps_add_locallink',
        'ps_add_note',
        'ps_add_pdflink',
        'ps_add_weblink',
        'ps_arc',
        'ps_arcn',
        'ps_begin_page',
        'ps_begin_pattern',
        'ps_begin_template',
        'ps_circle',
        'ps_clip',
        'ps_close_image',
        'ps_close',
        'ps_closepath_stroke',
        'ps_closepath',
        'ps_continue_text',
        'ps_curveto',
        'ps_delete',
        'ps_end_page',
        'ps_end_pattern',
        'ps_end_template',
        'ps_fill_stroke',
        'ps_fill',
        'ps_findfont',
        'ps_get_buffer',
        'ps_get_parameter',
        'ps_get_value',
        'ps_hyphenate',
        'ps_include_file',
        'ps_lineto',
        'ps_makespotcolor',
        'ps_moveto',
        'ps_new',
        'ps_open_file',
        'ps_open_image_file',
        'ps_open_image',
        'ps_open_memory_image',
        'ps_place_image',
        'ps_rect',
        'ps_restore',
        'ps_rotate',
        'ps_save',
        'ps_scale',
        'ps_set_border_color',
        'ps_set_border_dash',
        'ps_set_border_style',
        'ps_set_info',
        'ps_set_parameter',
        'ps_set_text_pos',
        'ps_set_value',
        'ps_setcolor',
        'ps_setdash',
        'ps_setflat',
        'ps_setfont',
        'ps_setgray',
        'ps_setlinecap',
        'ps_setlinejoin',
        'ps_setlinewidth',
        'ps_setmiterlimit',
        'ps_setoverprintmode',
        'ps_setpolydash',
        'ps_shading_pattern',
        'ps_shading',
        'ps_shfill',
        'ps_show_boxed',
        'ps_show_xy2',
        'ps_show_xy',
        'ps_show2',
        'ps_show',
        'ps_string_geometry',
        'ps_stringwidth',
        'ps_stroke',
        'ps_symbol_name',
        'ps_symbol_width',
        'ps_symbol',
        'ps_translate',

        // https://youtrack.jetbrains.com/issue/WI-74919
        'yaz_addinfo',
        'yaz_ccl_conf',
        'yaz_ccl_parse',
        'yaz_close',
        'yaz_connect',
        'yaz_database',
        'yaz_element',
        'yaz_errno',
        'yaz_error',
        'yaz_es_result',
        'yaz_es',
        'yaz_get_option',
        'yaz_hits',
        'yaz_itemorder',
        'yaz_present',
        'yaz_range',
        'yaz_record',
        'yaz_scan_result',
        'yaz_scan',
        'yaz_schema',
        'yaz_search',
        'yaz_set_option',
        'yaz_sort',
        'yaz_syntax',
        'yaz_wait',

        // https://youtrack.jetbrains.com/issue/WI-74923
        'setproctitle',
        'setthreadtitle',

        // https://youtrack.jetbrains.com/issue/WI-74927
        'mysql_drop_db',
        'mysql_create_db',

        // https://youtrack.jetbrains.com/issue/WI-74928
        'event_base_reinit',
        'event_priority_set',

        // https://youtrack.jetbrains.com/issue/WI-74965
        'uv_get_addrinfo',
        'uv_is_writeable',
        'ares_ghostbyname',
        'uv_async_asend',
        'uv_signal_init',
        'uv_signal_start',

        // PHP 8.3
        'ldap_connect_wallet',
        'posix_pathconf',
        'posix_fpathconf',

        // Removed in https://github.com/JetBrains/phpstorm-stubs/pull/1627
        'ares_gethostbyname',
        'uv_ares_init_options',
        'uv_handle_type',
        'uv_read2_start',

        // PHP 8.4
        // TODO: To report
        'pcntl_getqos_class',
        'pcntl_setns',
        'sodium_crypto_aead_aegis128l_',
        'sodium_crypto_aead_aegis256l_',
    ];

    /**
     * Basically mirrors https://github.com/nikic/PHP-Parser/blob/9aebf377fcdf205b2156cb78c0bd6e7b2003f106/lib/PhpParser/Lexer.php#L430.
     */
    private const MISSING_CONSTANTS = [
        // https://github.com/humbug/php-scoper/issues/618
        'TRUE',
        'FALSE',
        'NULL',

        // https://youtrack.jetbrains.com/issue/WI-53323
        'TIDEWAYS_XHPROF_FLAGS_MEMORY',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_MU',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_PMU',
        'TIDEWAYS_XHPROF_FLAGS_CPU',
        'TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_ALLOC',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_ALLOC_AS_MU',

        // https://youtrack.jetbrains.com/issue/WI-74918/Missing-PostScript-extension-symbols
        'ps_LINECAP_BUTT',
        'ps_LINECAP_ROUND',
        'ps_LINECAP_SQUARED',
        'ps_LINEJOIN_MITER',
        'ps_LINEJOIN_ROUND',
        'ps_LINEJOIN_BEVEL',

        // PHP 8.4
        // TODO: to report
        'CURL_TCP_KEEPCNT',
        'IP_PORTRANGE_DEFAULT',
        'IP_PORTRANGE_HIGH',
        'IP_PORTRANGE_LOW',
        'PATTERN',
        'P_UID',
        'P_GID',
        'P_SID',
        'P_JAILID',
        'PROPERTY_ID_COMPAT_MATH_CONTINUE',
        'PROPERTY_ID_COMPAT_MATH_START',
        'PROPERTY_IDS_UNARY_OPERATOR',
        'SIGCKPT',
        'SIGCKPTEXIT',
        'SOCK_CONN_DGRAM',
        'SO_EXCLBIND',
        'SO_EXCLUSIVEADDRUSE',
        'SO_NOSIGPIPE',
        'SO_LINGER_SEC',
        'IP_PORTRANGE',
    ];

    public static function createWithPhpStormStubs(): self
    {
        return new self(
            self::createSymbolList(
                array_keys(PhpStormStubsMap::CLASSES),
                self::MISSING_CLASSES,
            ),
            self::createSymbolList(
                array_keys(PhpStormStubsMap::FUNCTIONS),
                self::MISSING_FUNCTIONS,
            ),
            self::createConstantSymbolList(
                array_keys(PhpStormStubsMap::CONSTANTS),
                self::MISSING_CONSTANTS,
            ),
        );
    }

    public static function createEmpty(): self
    {
        return new self(
            SymbolRegistry::create(),
            SymbolRegistry::create(),
            SymbolRegistry::createForConstants(),
        );
    }

    private function __construct(
        private SymbolRegistry $classes,
        private SymbolRegistry $functions,
        private SymbolRegistry $constants,
    ) {
    }

    public function withAdditionalSymbols(
        SymbolRegistry $classNames,
        SymbolRegistry $functionNames,
        SymbolRegistry $constantNames
    ): self {
        return new self(
            $this->classes->merge($classNames),
            $this->functions->merge($functionNames),
            $this->constants->merge($constantNames),
        );
    }

    public function isClassInternal(string $name): bool
    {
        return $this->classes->matches($name);
    }

    public function isFunctionInternal(string $name): bool
    {
        return $this->functions->matches($name);
    }

    public function isConstantInternal(string $name): bool
    {
        return $this->constants->matches($name);
    }

    /**
     * @param string[] $sources
     */
    private static function createSymbolList(array ...$sources): SymbolRegistry
    {
        return SymbolRegistry::create(
            array_merge(...$sources),
        );
    }

    /**
     * @param string[] $sources
     */
    private static function createConstantSymbolList(array ...$sources): SymbolRegistry
    {
        return SymbolRegistry::createForConstants(
            array_merge(...$sources),
        );
    }
}
