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

namespace Humbug\PhpScoper\Symbol\Reflector;

use Humbug\PhpScoper\Symbol\Reflector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Reflector::class)]
class PhpStormStubsReflectorTest extends TestCase
{
    private Reflector $reflector;

    protected function setUp(): void
    {
        $this->reflector = Reflector::createWithPhpStormStubs();
    }

    #[DataProvider('provideClasses')]
    public function test_it_can_identify_internal_classes(string $class, bool $expected): void
    {
        $actual = $this->reflector->isClassInternal($class);

        self::assertSame($expected, $actual);
    }

    #[DataProvider('provideFunctions')]
    public function test_it_can_identify_internal_functions(string $class, bool $expected): void
    {
        $actual = $this->reflector->isFunctionInternal($class);

        self::assertSame($expected, $actual);
    }

    #[DataProvider('provideConstants')]
    public function test_it_can_identify_internal_constants(string $class, bool $expected): void
    {
        $actual = $this->reflector->isConstantInternal($class);

        self::assertSame($expected, $actual);
    }

    public static function provideClasses(): iterable
    {
        yield 'PHP internal class' => [
            'DateTime',
            true,
        ];

        yield 'FQ PHP internal class' => [
            '\DateTime',
            true,
        ];

        yield 'PHP unknown user-defined class' => [
            'Foo',
            false,
        ];

        yield 'PHP 7.0.0 new internal class' => [
            'ReflectionGenerator',
            true,
        ];

        // No new class or interface in 7.1.0

        yield 'PHP 7.2.0 new internal class' => [
            'Countable',
            true,
        ];

        yield 'PHP extension internal class' => [
            'Redis',
            true,
        ];

        // No new classes in PHP 8.2
        // No new classes in PHP 8.3

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-29503',
            'MongoInsertBatch',
            'MongoDeleteBatch',
        );

        yield from self::createDataSetForInternalSymbols(
            'HP 8.4 new class-like (added or modified)',
            'RoundingMode',
            'DOMNode',
            'DOMElement',
            'ResourceBundle',
            'Pdo\DbLib',
            'Pdo\Firebird',
            'Pdo\Mysql',
            'Pdo\Odbc',
            'Pdo\Pgsql',
            'Pdo\Sqlite',
        );
    }

    public static function provideFunctions(): iterable
    {
        yield 'PHP internal function' => [
            'class_exists',
            true,
        ];

        yield 'FQ PHP internal function' => [
            '\class_exists',
            true,
        ];

        yield 'PHP internal function with the wrong case' => [
            'CLASS_EXISTS',
            true,
        ];

        yield 'PHP unknown user-defined function' => [
            'unknown',
            false,
        ];

        yield 'PHP 7.0.0 new internal function' => [
            'error_clear_last',
            true,
        ];

        yield 'PHP 7.1.0 new internal function' => [
            'is_iterable',
            true,
        ];

        yield 'PHP 7.2.0 new internal function' => [
            'spl_object_id',
            true,
        ];

        yield 'PHP extension internal function' => [
            'ftp_alloc',
            true,
        ];

        // https://github.com/sebastianbergmann/phpunit/issues/4211
        yield 'PHPDBG internal function' => [
            'phpdbg_break_file',
            true,
        ];

        yield from self::createDataSetForInternalSymbols(
            'PHP 8.2 functions',
            'curl_upkeep',
            'ini_parse_quantity',
            'libxml_get_external_entity_loader',
            'memory_reset_peak_usage',
            'mysqli_execute_query',
            'openssl_cipher_key_length',
            'sodium_crypto_stream_xchacha20_xor_ic',
        );

        yield from self::createDataSetForInternalSymbols(
            'PHP 8.3 functions',
            'ldap_connect_wallet',
            'ldap_exop_sync',
            'mb_str_pad',
            'posix_sysconf',
            'posix_pathconf',
            'posix_fpathconf',
            'posix_eaccess',
            'socket_atmark',
            'str_increment',
            'str_decrement',
            'stream_context_set_options',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-53323',
            'tideways_xhprof_enable',
            'tideways_xhprof_disable',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-29503',
            'bson_encode',
            'bson_decode',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74920',
            'swoole_async_dns_lookup',
            'swoole_async_readfile',
            'swoole_async_write',
            'swoole_async_writefile',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74921',
            'ssh2_send_eof',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74922',
            'ssdeep_fuzzy_compare',
            'ssdeep_fuzzy_hash',
            'ssdeep_fuzzy_hash_filename',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74918',
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
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74919',
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
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74923',
            'setproctitle',
            'setthreadtitle',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74924',
            'rpmaddtag',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74925',
            'oci_set_prefetch_lob',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74926',
            'normalizer_is_normalized',
            'normalizer_normalize',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74927',
            'mysql_drop_db',
            'mysql_create_db',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74928',
            'event_base_reinit',
            'event_priority_set',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74929',
            'db2_pclose',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74930',
            'cubrid_current_oid',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74965',
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
        );

        yield from self::createDataSetForInternalSymbols(
            'PHP 8.4 functions (added or modified)',
            'bcfloor',
            'bcceil',
            'bcround',
            'bcdiv',
            'bcpow',
            'bcmath',
            'bcdivmod',
            'exit',
            'die',
            'dba_key_split',
            'inet_ntoa',
            'intltz_get_iana_id',
            'resourcebundle_get',
            'grapheme_str_split',
            'libxml_set_streams_context',
            'mb_trim',
            'mb_ltrim',
            'mb_rtrim',
            'mb_ucfirst',
            'mb_lcfirst',
            'mb_detect_encoding',
            'mysqli_ping',
            'mysqli_kill',
            'mysqli_refresh',
            'mysqli_store_result',
            'opcache_jit_blacklist',
            'openssl_csr_sign',
            'openssl_csr_new',
            'pcntl_setns',
            'pcntl_getcpuaffinity',
            'pcntl_setcpuaffinity',
            'pcntl_get_signal_handler',
            'pcntl_getcpu',
            'pcntl_getqos_class',
            'pcntl_setqos_class',
            'pcntl_waitid',
            'pcntl_sigwaitinfo',
            'pg_result_memory_size',
            'pg_change_password',
            'pg_put_copy_data',
            'pg_put_copy_end',
            'pg_socket_poll',
            'pg_jit',
            'pg_set_chunked_rows_size',
            'pg_convert',
            'pg_insert',
            'pg_update',
            'pg_delete',
            'posix_isatty',
            'lcg_value',
            'readline_info',
            'rl_line_buffer_length',
            'rl_len',
            'simplexml_import_dom',
            'inet_ntoa',
            'socket_create_listen',
            'socket_create',
            'socket_create_pair',
            'http_get_last_response_headers',
            'http_clear_last_response_headers',
            'php_base64_encode_ex',
            'array_find',
            'array_find_key',
            'array_all',
            'array_any',
            'highlight_string',
            'print_r',
            'request_parse_body',
            'fputcsv',
            'fgetcsv',
            'str_getcsv',
            'xml_set_object',
        );
    }

    public static function provideConstants(): iterable
    {
        yield 'PHP internal constant' => [
            'PHP_VERSION',
            true,
        ];

        yield '\PHP internal constant' => [
            'PHP_VERSION',
            true,
        ];

        yield 'PHP unknown user-defined constant' => [
            'UNKNOWN',
            false,
        ];

        yield 'PHP 7.0.0 new internal constant' => [
            'PHP_INT_MIN',
            true,
        ];

        yield 'PHP 7.1.0 new internal constant' => [
            'CURLMOPT_PUSHFUNCTION',
            true,
        ];

        yield 'PHP 7.2.0 new internal constant' => [
            'PHP_OS_FAMILY',
            true,
        ];

        yield 'PHP 7.3.0 new internal constant' => [
            'JSON_THROW_ON_ERROR',
            true,
        ];

        yield 'PHP extension internal constant' => [
            'FTP_ASCII',
            true,
        ];

        yield from self::createDataSetForInternalSymbols(
            'STD constants',
            'STDIN',
            'STDOUT',
            'STDERR',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://github.com/humbug/php-scoper/issues/618',
            'true',
            'TRUE',
            'false',
            'FALSE',
            'null',
            'NULL',
        );

        yield from self::createDataSetForInternalSymbols(
            'PHP 8.0 constants',
            'T_NAME_QUALIFIED',
            'T_NAME_FULLY_QUALIFIED',
            'T_NAME_RELATIVE',
            'T_MATCH',
            'T_NULLSAFE_OBJECT_OPERATOR',
            'T_ATTRIBUTE',
        );

        yield from self::createDataSetForInternalSymbols(
            'PHP 8.1 constants',
            'T_ENUM',
            'T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG',
            'T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG',
            'T_READONLY',
        );

        // No PHP 8.2 constants
        // No PHP 8.3 constants

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-53323',
            'TIDEWAYS_XHPROF_FLAGS_MEMORY',
            'TIDEWAYS_XHPROF_FLAGS_MEMORY_MU',
            'TIDEWAYS_XHPROF_FLAGS_MEMORY_PMU',
            'TIDEWAYS_XHPROF_FLAGS_CPU',
            'TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS',
            'TIDEWAYS_XHPROF_FLAGS_MEMORY_ALLOC',
            'TIDEWAYS_XHPROF_FLAGS_MEMORY_ALLOC_AS_MU',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-29503',
            'MONGODB_VERSION',
            'MONGODB_STABILITY',
        );

        yield from self::createDataSetForInternalSymbols(
            'https://youtrack.jetbrains.com/issue/WI-74918/Missing-PostScript-extension-symbols',
            'ps_LINECAP_BUTT',
            'ps_LINECAP_ROUND',
            'ps_LINECAP_SQUARED',
            'ps_LINEJOIN_MITER',
            'ps_LINEJOIN_ROUND',
            'ps_LINEJOIN_BEVEL',
        );

        yield from self::createDataSetForInternalSymbols(
            'PHP 8.4 constants (added or modified)',
            'LONG_MAX',
            'CURL_HTTP_VERSION_3',
            'CURL_HTTP_VERSION_3ONLY',
            'CURLOPT_HTTP_VERSION',
            'CURLOPT_TCP_KEEPCNT',
            'CURLOPT_PREREQFUNCTION',
            'CURLOPT_SERVER_RESPONSE_TIMEOUT',
            'CURLOPT_FTP_RESPONSE_TIMEOUT',
            'CURLOPT_DNS_USE_GLOBAL_CACHE',
            'CURLOPT_DEBUGFUNCTION',
            'CURLMOPT_PUSHFUNCTION',
            'SUNFUNCS_RET_TIMESTAMP',
            'SUNFUNCS_RET_STRING',
            'SUNFUNCS_RET_DOUBLE',
            'DOM_PHP_ERR',
            'PROPERTY_IDS_UNARY_OPERATOR',
            'PROPERTY_ID_COMPAT_MATH_START',
            'PROPERTY_ID_COMPAT_MATH_CONTINUE',
            'LDAP_OPT_X_TLS_PROTOCOL_MAX',
            'LDAP_OPT_X_TLS_PROTOCOL_TLS1_3',
            'LIBXML_RECOVER',
            'LIBXML_NO_XXE',
            'MYSQLI_STORE_RESULT_COPY_DATA',
            'X509_PURPOSE_OCSP_HELPER',
            'X509_PURPOSE_TIMESTAMP_SIGN',
            'SIGCKPT',
            'SIGCKPTEXIT',
            'PGSQL_ATTR_RESULT_MEMORY_SIZE',
            'POSIX_SC_CHILD_MAX',
            'POSIX_SC_CLK_TCK',
            'SOAP_FUNCTIONS_ALL',
            'SO_EXECLUSIVEADDRUSE',
            'SOCK_CONN_DGRAM',
            'SOCK_DCCP',
            'TCP_SYNCNT',
            'SO_EXCLBIND',
            'SO_NOSIGPIPE',
            'SO_LINGER_SEC',
            'SOCK_NONBLOCK',
            'SOCK_CLOEXEC',
        );
    }

    /**
     * @return iterable<string, array{string}>
     */
    private static function createDataSetForInternalSymbols(string $source, string ...$data): iterable
    {
        foreach ($data as $index => $value) {
            yield $source.' index #'.$index => [$value, true];
        }
    }
}
