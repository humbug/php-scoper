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

return [
    'meta' => [
        'title' => 'Internal symbols',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'Known internal symbols that used to or still require a patch in the Reflector' => <<<'PHP'
<?php

namespace Acme;

use UV;
use Crypto\Cipher;
use Crypto\CipherException;
use Crypto\Hash;
use Crypto\HashException;
use Crypto\MAC;
use Crypto\MACException;
use Crypto\HMAC;
use Crypto\CMAC;
use Crypto\KDF;
use Crypto\KDFException;
use Crypto\PBKDF2;
use Crypto\PBKDF2Exception;
use Crypto\Base64;
use Crypto\Base64Exception;
use Crypto\Rand;
use Crypto\RandException;
use parallel\Channel;
use parallel\Channel\Error as ParallelChannelError;
use parallel\Channel\Error\Closed as ParallelChannelClosed;
use parallel\Channel\Error\Existence as ParallelChannelExistence;
use parallel\Channel\Error\IllegalValue as ParallelChannelIllegalValue;
use parallel\Error as ParallelError;
use parallel\Events;
use parallel\Events\Error as ParallelEventsError;
use parallel\Events\Error\Existence as ParallelEventsExistence;
use parallel\Events\Error\Timeout;
use parallel\Events\Event;
use parallel\Events\Event\Type;
use parallel\Events\Input;
use parallel\Events\Input\Error as ParallelInputError;
use parallel\Events\Input\Error\Existence as ParallelInputExistence;
use parallel\Events\Input\Error\IllegalValue as ParallelInputIllegalValue;
use parallel\Future;
use parallel\Future\Error as ParallelFutureError;
use parallel\Future\Error\Cancelled;
use parallel\Future\Error\Foreign;
use parallel\Future\Error\Killed;
use parallel\Runtime;
use parallel\Runtime\Bootstrap as ParallelRuntimeBootstrap;
use parallel\Runtime\Error as ParallelRuntimeError;
use parallel\Runtime\Error\Bootstrap as ParallelRuntimeErrorBootstrap;
use parallel\Runtime\Error\Closed as ParallelRuntimeClosed;
use parallel\Runtime\Error\IllegalFunction;
use parallel\Runtime\Error\IllegalInstruction;
use parallel\Runtime\Error\IllegalParameter;
use parallel\Runtime\Error\IllegalReturn;
#https://github.com/bobthecow/psysh/issues/581#issuecomment-560137900
use ReflectionClassConstant;

use function parallel\bootstrap;
use function parallel\run;
use function pcov\collect;
use function pcov\start;
use function pcov\stop;
use function pcov\clear;
use function pcov\waiting;
use function pcov\memory;

use const pcov\all;
use const pcov\inclusive;
use const pcov\exclusive;

sapi_windows_vt100_support();
uv_unref();
uv_last_error();
uv_err_name();
uv_strerror();
uv_update_time();
uv_ref();
uv_run();
uv_run_once();
uv_loop_delete();
uv_now();
uv_tcp_bind();
uv_tcp_bind6();
uv_write();
uv_write2();
uv_tcp_nodelay();
uv_accept();
uv_shutdown();
uv_close();
uv_read_start();
uv_read2_start();
uv_read_stop();
uv_ip4_addr();
uv_ip6_addr();
uv_listen();
uv_tcp_connect();
uv_tcp_connect6();
uv_timer_init();
uv_timer_start();
uv_timer_stop();
uv_timer_again();
uv_timer_set_repeat();
uv_timer_get_repeat();
uv_idle_init();
uv_idle_start();
uv_idle_stop();
uv_getaddrinfo();
uv_tcp_init();
uv_default_loop();
uv_loop_new();
uv_udp_init();
uv_udp_bind();
uv_udp_bind6();
uv_udp_recv_start();
uv_udp_recv_stop();
uv_udp_set_membership();
uv_udp_set_multicast_loop();
uv_udp_set_multicast_ttl();
uv_udp_set_broadcast();
uv_udp_send();
uv_udp_send6();
uv_is_active();
uv_is_readable();
uv_is_writable();
uv_walk();
uv_guess_handle();
uv_handle_type();
uv_pipe_init();
uv_pipe_open();
uv_pipe_bind();
uv_pipe_connect();
uv_pipe_pending_instances();
uv_ares_init_options();
ares_gethostbyname();
uv_loadavg();
uv_uptime();
uv_get_free_memory();
uv_get_total_memory();
uv_hrtime();
uv_exepath();
uv_cpu_info();
uv_interface_addresses();
uv_stdio_new();
uv_spawn();
uv_process_kill();
uv_kill();
uv_chdir();
uv_rwlock_init();
uv_rwlock_rdlock();
uv_rwlock_tryrdlock();
uv_rwlock_rdunlock();
uv_rwlock_wrlock();
uv_rwlock_trywrlock();
uv_rwlock_wrunlock();
uv_mutex_init();
uv_mutex_lock();
uv_mutex_trylock();
uv_sem_init();
uv_sem_post();
uv_sem_wait();
uv_sem_trywait();
uv_prepare_init();
uv_prepare_start();
uv_prepare_stop();
uv_check_init();
uv_check_start();
uv_check_stop();
uv_async_init();
uv_async_send();
uv_queue_work();
uv_fs_open();
uv_fs_read();
uv_fs_close();
uv_fs_write();
uv_fs_fsync();
uv_fs_fdatasync();
uv_fs_ftruncate();
uv_fs_mkdir();
uv_fs_rmdir();
uv_fs_unlink();
uv_fs_rename();
uv_fs_utime();
uv_fs_futime();
uv_fs_chmod();
uv_fs_fchmod();
uv_fs_chown();
uv_fs_fchown();
uv_fs_link();
uv_fs_symlink();
uv_fs_readlink();
uv_fs_stat();
uv_fs_lstat();
uv_fs_fstat();
uv_fs_readdir();
uv_fs_sendfile();
uv_fs_event_init();
uv_tty_init();
uv_tty_get_winsize();
uv_tty_set_mode();
uv_tty_reset_mode();
uv_tcp_getsockname();
uv_tcp_getpeername();
uv_udp_getsockname();
uv_resident_set_memory();
uv_ip4_name();
uv_ip6_name();
uv_poll_init();
uv_poll_start();
uv_poll_stop();
uv_fs_poll_init();
uv_fs_poll_start();
uv_fs_poll_stop();
uv_stop();
uv_signal_stop();
bootstrap();
run();
collect();
start();
stop();
clear();
waiting();
memory();
mb_str_split();
password_algos();

echo STDIN;
echo STDOUT;
echo STDERR;
echo all;
echo inclusive;
echo exclusive;

----
<?php

namespace Humbug\Acme;

use UV;
use Crypto\Cipher;
use Crypto\CipherException;
use Crypto\Hash;
use Crypto\HashException;
use Crypto\MAC;
use Crypto\MACException;
use Crypto\HMAC;
use Crypto\CMAC;
use Crypto\KDF;
use Crypto\KDFException;
use Crypto\PBKDF2;
use Crypto\PBKDF2Exception;
use Crypto\Base64;
use Crypto\Base64Exception;
use Crypto\Rand;
use Crypto\RandException;
use parallel\Channel;
use parallel\Channel\Error as ParallelChannelError;
use parallel\Channel\Error\Closed as ParallelChannelClosed;
use parallel\Channel\Error\Existence as ParallelChannelExistence;
use parallel\Channel\Error\IllegalValue as ParallelChannelIllegalValue;
use parallel\Error as ParallelError;
use parallel\Events;
use parallel\Events\Error as ParallelEventsError;
use parallel\Events\Error\Existence as ParallelEventsExistence;
use parallel\Events\Error\Timeout;
use parallel\Events\Event;
use parallel\Events\Event\Type;
use parallel\Events\Input;
use parallel\Events\Input\Error as ParallelInputError;
use parallel\Events\Input\Error\Existence as ParallelInputExistence;
use parallel\Events\Input\Error\IllegalValue as ParallelInputIllegalValue;
use parallel\Future;
use parallel\Future\Error as ParallelFutureError;
use parallel\Future\Error\Cancelled;
use parallel\Future\Error\Foreign;
use parallel\Future\Error\Killed;
use parallel\Runtime;
use parallel\Runtime\Bootstrap as ParallelRuntimeBootstrap;
use parallel\Runtime\Error as ParallelRuntimeError;
use parallel\Runtime\Error\Bootstrap as ParallelRuntimeErrorBootstrap;
use parallel\Runtime\Error\Closed as ParallelRuntimeClosed;
use parallel\Runtime\Error\IllegalFunction;
use parallel\Runtime\Error\IllegalInstruction;
use parallel\Runtime\Error\IllegalParameter;
use parallel\Runtime\Error\IllegalReturn;
#https://github.com/bobthecow/psysh/issues/581#issuecomment-560137900
use ReflectionClassConstant;
use function parallel\bootstrap;
use function parallel\run;
use function pcov\collect;
use function pcov\start;
use function pcov\stop;
use function pcov\clear;
use function pcov\waiting;
use function pcov\memory;
use const pcov\all;
use const pcov\inclusive;
use const pcov\exclusive;
\sapi_windows_vt100_support();
\uv_unref();
\uv_last_error();
\uv_err_name();
\uv_strerror();
\uv_update_time();
\uv_ref();
\uv_run();
\uv_run_once();
\uv_loop_delete();
\uv_now();
\uv_tcp_bind();
\uv_tcp_bind6();
\uv_write();
\uv_write2();
\uv_tcp_nodelay();
\uv_accept();
\uv_shutdown();
\uv_close();
\uv_read_start();
\uv_read2_start();
\uv_read_stop();
\uv_ip4_addr();
\uv_ip6_addr();
\uv_listen();
\uv_tcp_connect();
\uv_tcp_connect6();
\uv_timer_init();
\uv_timer_start();
\uv_timer_stop();
\uv_timer_again();
\uv_timer_set_repeat();
\uv_timer_get_repeat();
\uv_idle_init();
\uv_idle_start();
\uv_idle_stop();
\uv_getaddrinfo();
\uv_tcp_init();
\uv_default_loop();
\uv_loop_new();
\uv_udp_init();
\uv_udp_bind();
\uv_udp_bind6();
\uv_udp_recv_start();
\uv_udp_recv_stop();
\uv_udp_set_membership();
\uv_udp_set_multicast_loop();
\uv_udp_set_multicast_ttl();
\uv_udp_set_broadcast();
\uv_udp_send();
\uv_udp_send6();
\uv_is_active();
\uv_is_readable();
\uv_is_writable();
\uv_walk();
\uv_guess_handle();
\uv_handle_type();
\uv_pipe_init();
\uv_pipe_open();
\uv_pipe_bind();
\uv_pipe_connect();
\uv_pipe_pending_instances();
\uv_ares_init_options();
\ares_gethostbyname();
\uv_loadavg();
\uv_uptime();
\uv_get_free_memory();
\uv_get_total_memory();
\uv_hrtime();
\uv_exepath();
\uv_cpu_info();
\uv_interface_addresses();
\uv_stdio_new();
\uv_spawn();
\uv_process_kill();
\uv_kill();
\uv_chdir();
\uv_rwlock_init();
\uv_rwlock_rdlock();
\uv_rwlock_tryrdlock();
\uv_rwlock_rdunlock();
\uv_rwlock_wrlock();
\uv_rwlock_trywrlock();
\uv_rwlock_wrunlock();
\uv_mutex_init();
\uv_mutex_lock();
\uv_mutex_trylock();
\uv_sem_init();
\uv_sem_post();
\uv_sem_wait();
\uv_sem_trywait();
\uv_prepare_init();
\uv_prepare_start();
\uv_prepare_stop();
\uv_check_init();
\uv_check_start();
\uv_check_stop();
\uv_async_init();
\uv_async_send();
\uv_queue_work();
\uv_fs_open();
\uv_fs_read();
\uv_fs_close();
\uv_fs_write();
\uv_fs_fsync();
\uv_fs_fdatasync();
\uv_fs_ftruncate();
\uv_fs_mkdir();
\uv_fs_rmdir();
\uv_fs_unlink();
\uv_fs_rename();
\uv_fs_utime();
\uv_fs_futime();
\uv_fs_chmod();
\uv_fs_fchmod();
\uv_fs_chown();
\uv_fs_fchown();
\uv_fs_link();
\uv_fs_symlink();
\uv_fs_readlink();
\uv_fs_stat();
\uv_fs_lstat();
\uv_fs_fstat();
\uv_fs_readdir();
\uv_fs_sendfile();
\uv_fs_event_init();
\uv_tty_init();
\uv_tty_get_winsize();
\uv_tty_set_mode();
\uv_tty_reset_mode();
\uv_tcp_getsockname();
\uv_tcp_getpeername();
\uv_udp_getsockname();
\uv_resident_set_memory();
\uv_ip4_name();
\uv_ip6_name();
\uv_poll_init();
\uv_poll_start();
\uv_poll_stop();
\uv_fs_poll_init();
\uv_fs_poll_start();
\uv_fs_poll_stop();
\uv_stop();
\uv_signal_stop();
bootstrap();
run();
collect();
start();
stop();
clear();
waiting();
memory();
\mb_str_split();
\password_algos();
echo \STDIN;
echo \STDOUT;
echo \STDERR;
echo all;
echo inclusive;
echo exclusive;

PHP
    ,
];
