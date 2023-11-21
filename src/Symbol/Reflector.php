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
        // https://youtrack.jetbrains.com/issue/WI-29503
        'MongoInsertBatch',
        'MongoDeleteBatch',
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

        // https://youtrack.jetbrains.com/issue/WI-74921
        'ssh2_send_eof',

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

        // https://youtrack.jetbrains.com/issue/WI-74924
        'rpmaddtag',

        // https://youtrack.jetbrains.com/issue/WI-74925
        'oci_set_prefetch_lob',

        // https://youtrack.jetbrains.com/issue/WI-74926
        'normalizer_is_normalized',
        'normalizer_normalize',

        // https://youtrack.jetbrains.com/issue/WI-74927
        'mysql_drop_db',
        'mysql_create_db',

        // https://youtrack.jetbrains.com/issue/WI-74928
        'event_base_reinit',
        'event_priority_set',

        // https://youtrack.jetbrains.com/issue/WI-74929
        'db2_pclose',

        // https://youtrack.jetbrains.com/issue/WI-74930
        'cubrid_current_oid',

        // https://youtrack.jetbrains.com/issue/WI-74965
        'uv_unref',
        'uv_last_error',
        'uv_err_name',
        'uv_strerror',
        'uv_ref',
        'uv_run',
        'uv_run_once',
        'uv_loop_delete',
        'uv_now',
        'uv_tcp_bind',
        'uv_tcp_bind6',
        'uv_write',
        'uv_write2',
        'uv_tcp_nodelay',
        'uv_accept',
        'uv_shutdown',
        'uv_close',
        'uv_read_start',
        'uv_read2_start',
        'uv_read_stop',
        'uv_ip4_addr',
        'uv_ip6_addr',
        'uv_listen',
        'uv_tcp_connect',
        'uv_tcp_connect6',
        'uv_timer_init',
        'uv_timer_stop',
        'uv_timer_again',
        'uv_timer_set_repeat',
        'uv_timer_get_repeat',
        'uv_idle_init',
        'uv_idle_start',
        'uv_idle_stop',
        'uv_get_addrinfo',
        'uv_tcp_init',
        'uv_default_loop',
        'uv_loop_new',
        'uv_udp_init',
        'uv_udp_bind',
        'uv_udp_bind6',
        'uv_udp_recv_start',
        'uv_udp_recv_stop',
        'uv_udp_set_membership',
        'uv_udp_set_multicast_loop',
        'uv_udp_set_multicast_ttl',
        'uv_udp_set_broadcast',
        'uv_udp_send',
        'uv_udp_send6',
        'uv_is_active',
        'uv_is_readable',
        'uv_is_writeable',
        'uv_walk',
        'uv_guess_handle',
        'uv_handle_type',
        'uv_pipe_init',
        'uv_pipe_open',
        'uv_pipe_bind',
        'uv_pipe_connect',
        'uv_pipe_pending_instances',
        'uv_ares_init_options',
        'ares_ghostbyname',
        'uv_loadavg',
        'uv_uptime',
        'uv_get_free_memory',
        'uv_get_total_memory',
        'uv_hrtime',
        'uv_exepath',
        'uv_cpu_info',
        'uv_interface_addresses',
        'uv_stdio_new',
        'uv_spawn',
        'uv_process_kill',
        'uv_kill',
        'uv_chdir',
        'uv_rwlock_init',
        'uv_rwlock_rdlock',
        'uv_rwlock_tryrdlock',
        'uv_rwlock_rdunlock',
        'uv_rwlock_wrlock',
        'uv_rwlock_trywrlock',
        'uv_rwlock_wrunlock',
        'uv_mutex_init',
        'uv_mutex_lock',
        'uv_mutex_trylock',
        'uv_sem_init',
        'uv_sem_post',
        'uv_sem_wait',
        'uv_sem_trywait',
        'uv_prepare_init',
        'uv_prepare_start',
        'uv_prepare_stop',
        'uv_check_init',
        'uv_check_start',
        'uv_check_stop',
        'uv_async_init',
        'uv_async_asend',
        'uv_queue_work',
        'uv_fs_open',
        'uv_fs_read',
        'uv_fs_close',
        'uv_fs_write',
        'uv_fs_fsync',
        'uv_fs_fdatasync',
        'uv_fs_ftruncate',
        'uv_fs_mkdir',
        'uv_fs_rmdir',
        'uv_fs_unlink',
        'uv_fs_rename',
        'uv_fs_utime',
        'uv_fs_futime',
        'uv_fs_chmod',
        'uv_fs_fchmod',
        'uv_fs_chown',
        'uv_fs_fchown',
        'uv_fs_link',
        'uv_fs_symlink',
        'uv_fs_readlink',
        'uv_fs_stat',
        'uv_fs_lstat',
        'uv_fs_fstat',
        'uv_fs_readdir',
        'uv_fs_sendfile',
        'uv_fs_event_init',
        'uv_tty_init',
        'uv_tty_get_winsize',
        'uv_tty_set_mode',
        'uv_tty_reset_mode',
        'uv_tcp_getsockname',
        'uv_tcp_getpeername',
        'uv_udp_getsockname',
        'uv_resident_set_memory',
        'uv_ip4_name',
        'uv_ip6_name',
        'uv_poll_init',
        'uv_poll_start',
        'uv_poll_stop',
        'uv_fs_poll_init',
        'uv_fs_poll_start',
        'uv_fs_poll_stop',
        'uv_stop',
        'uv_signal_init',
        'uv_signal_start',
        'uv_signal_stop',
    ];

    /**
     * Basically mirrors https://github.com/nikic/PHP-Parser/blob/9aebf377fcdf205b2156cb78c0bd6e7b2003f106/lib/PhpParser/Lexer.php#L430.
     */
    private const MISSING_CONSTANTS = [
        'STDIN',
        'STDOUT',
        'STDERR',

        // https://github.com/humbug/php-scoper/issues/618
        'true',
        'TRUE',
        'false',
        'FALSE',
        'null',
        'NULL',

        // Added in PHP 8.0
        'T_NAME_QUALIFIED',
        'T_NAME_FULLY_QUALIFIED',
        'T_NAME_RELATIVE',
        'T_MATCH',
        'T_NULLSAFE_OBJECT_OPERATOR',
        'T_ATTRIBUTE',

        // Added in PHP 8.1
        'T_ENUM',
        'T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG',
        'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG',
        'T_READONLY',

        // https://youtrack.jetbrains.com/issue/WI-53323
        'TIDEWAYS_XHPROF_FLAGS_MEMORY',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_MU',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_PMU',
        'TIDEWAYS_XHPROF_FLAGS_CPU',
        'TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_ALLOC',
        'TIDEWAYS_XHPROF_FLAGS_MEMORY_ALLOC_AS_MU',

        // https://youtrack.jetbrains.com/issue/WI-29503
        'MONGODB_VERSION',
        'MONGODB_STABILITY',

        // https://youtrack.jetbrains.com/issue/WI-74918/Missing-PostScript-extension-symbols
        'ps_LINECAP_BUTT',
        'ps_LINECAP_ROUND',
        'ps_LINECAP_SQUARED',
        'ps_LINEJOIN_MITER',
        'ps_LINEJOIN_ROUND',
        'ps_LINEJOIN_BEVEL',
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
