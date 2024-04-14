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

use Humbug\PhpScoper\SpecFramework\Config\Meta;

return [
    'meta' => new Meta(
        title: 'Internal symbols',
    ),

    'PHP native symbols' => <<<'PHP'
        <?php

        namespace Acme;

        // https://github.com/bobthecow/psysh/issues/581#issuecomment-560137900
        use ReflectionClassConstant;

        use function mb_str_split;
        use function password_algos;

        // https://github.com/humbug/php-scoper/issues/618
        use const STDIN;
        use const STDOUT;
        use const STDERR;
        // https://github.com/humbug/php-scoper/issues/618
        use const true;
        use const TRUE;
        use const null;
        use const NULL;

        mb_str_split();
        \mb_str_split();
        password_algos();
        \password_algos();

        echo STDIN;
        echo \STDIN;
        echo STDOUT;
        echo \STDOUT;
        echo STDERR;
        echo \STDERR;
        echo true;
        echo \true;
        echo TRUE;
        echo \TRUE;
        echo null;
        echo \null;
        echo NULL;
        echo \NULL;

        ----
        <?php

        namespace Humbug\Acme;

        // https://github.com/bobthecow/psysh/issues/581#issuecomment-560137900
        use ReflectionClassConstant;
        use function mb_str_split;
        use function password_algos;
        // https://github.com/humbug/php-scoper/issues/618
        use const STDIN;
        use const STDOUT;
        use const STDERR;
        // https://github.com/humbug/php-scoper/issues/618
        use const true;
        use const TRUE;
        use const null;
        use const NULL;
        mb_str_split();
        \mb_str_split();
        password_algos();
        \password_algos();
        echo STDIN;
        echo \STDIN;
        echo STDOUT;
        echo \STDOUT;
        echo STDERR;
        echo \STDERR;
        echo true;
        echo \true;
        echo \TRUE;
        echo \TRUE;
        echo null;
        echo \null;
        echo NULL;
        echo \NULL;

        PHP,

    'PCOV patch' => <<<'PHP'
        <?php

        namespace Acme;

        use function pcov\collect;

        use const pcov\all;

        collect();
        \pcov\collect();

        echo all;
        echo \pcov\all;

        ----
        <?php

        namespace Humbug\Acme;

        use function pcov\collect;
        use const pcov\all;
        collect();
        \pcov\collect();
        echo all;
        echo \pcov\all;

        PHP,

    'parallel ext patch' => <<<'PHP'
        <?php

        namespace Acme;

        use parallel\Channel;

        use function parallel\bootstrap;

        new Channel();
        new \parallel\Channel();

        bootstrap();
        \parallel\bootstrap();

        ----
        <?php

        namespace Humbug\Acme;

        use parallel\Channel;
        use function parallel\bootstrap;
        new Channel();
        new \parallel\Channel();
        bootstrap();
        \parallel\bootstrap();

        PHP,

    'php-crypto patch' => <<<'PHP'
        <?php

        namespace Acme;

        use Crypto\Cipher;

        new Cipher();
        new \Crypto\Cipher();

        ----
        <?php

        namespace Humbug\Acme;

        use Crypto\Cipher;
        new Cipher();
        new \Crypto\Cipher();

        PHP,

    'AMPHP UV ext patch' => <<<'PHP'
        <?php

        namespace Acme;

        use UV;

        use function ares_gethostbyname;
        use function uv_accept;

        new UV();
        new \UV();

        ares_gethostbyname();
        \ares_gethostbyname();
        uv_accept();
        \uv_accept();

        ----
        <?php

        namespace Humbug\Acme;

        use UV;
        use function ares_gethostbyname;
        use function uv_accept;
        new UV();
        new \UV();
        ares_gethostbyname();
        \ares_gethostbyname();
        uv_accept();
        \uv_accept();

        PHP,

    'Swole patch' => <<<'PHP'
        <?php

        namespace Acme;

        use Swoole\Atomic;

        use function go;
        use function swoole_async_dns_lookup_coro;

        use const SWOOLE_ASYNC;

        new Atomic();
        new \Swoole\Atomic();
        go();
        \go();
        swoole_async_dns_lookup_coro();
        \swoole_async_dns_lookup_coro();
        echo SWOOLE_ASYNC;
        echo \SWOOLE_ASYNC;

        ----
        <?php

        namespace Humbug\Acme;

        use Swoole\Atomic;
        use function go;
        use function swoole_async_dns_lookup_coro;
        use const SWOOLE_ASYNC;
        new Atomic();
        new \Swoole\Atomic();
        go();
        \go();
        swoole_async_dns_lookup_coro();
        \swoole_async_dns_lookup_coro();
        echo SWOOLE_ASYNC;
        echo \SWOOLE_ASYNC;

        PHP,

    'Tideways patch' => <<<'PHP'
        <?php

        namespace Acme;

        use function tideways_xhprof_enable;

        use const TIDEWAYS_XHPROF_FLAGS_MEMORY;

        tideways_xhprof_enable();
        \tideways_xhprof_enable();
        echo TIDEWAYS_XHPROF_FLAGS_MEMORY;
        echo \TIDEWAYS_XHPROF_FLAGS_MEMORY;

        ----
        <?php

        namespace Humbug\Acme;

        use function tideways_xhprof_enable;
        use const TIDEWAYS_XHPROF_FLAGS_MEMORY;
        tideways_xhprof_enable();
        \tideways_xhprof_enable();
        echo TIDEWAYS_XHPROF_FLAGS_MEMORY;
        echo \TIDEWAYS_XHPROF_FLAGS_MEMORY;

        PHP,

    // https://github.com/humbug/php-scoper/issues/540
    'Xdebug patch' => <<<'PHP'
        <?php

        namespace Acme;

        use function xdebug_info;

        xdebug_info();
        \xdebug_info();

        ----
        <?php

        namespace Humbug\Acme;

        use function xdebug_info;
        xdebug_info();
        \xdebug_info();

        PHP,

    // https://youtrack.jetbrains.com/issue/WI-29503
    'MongoDB patch' => <<<'PHP'
        <?php

        namespace Acme;

        use MongoWriteBatch;
        use MongoUpdateBatch;
        use MongoInsertBatch;
        use MongoDeleteBatch;

        use function bson_encode;
        use function bson_decode;

        use const MONGODB_VERSION;
        use const MONGODB_STABILITY;

        new MongoWriteBatch();
        new \MongoWriteBatch();
        new MongoUpdateBatch();
        new \MongoUpdateBatch();
        new MongoInsertBatch();
        new \MongoInsertBatch();
        new MongoDeleteBatch();
        new \MongoDeleteBatch();

        bson_encode();
        \bson_encode();
        bson_decode();
        \bson_decode();

        echo MONGODB_VERSION;
        echo \MONGODB_VERSION;
        echo MONGODB_STABILITY;
        echo \MONGODB_STABILITY;

        ----
        <?php

        namespace Humbug\Acme;

        use MongoWriteBatch;
        use MongoUpdateBatch;
        use MongoInsertBatch;
        use MongoDeleteBatch;
        use function bson_encode;
        use function bson_decode;
        use const MONGODB_VERSION;
        use const MONGODB_STABILITY;
        new MongoWriteBatch();
        new \MongoWriteBatch();
        new MongoUpdateBatch();
        new \MongoUpdateBatch();
        new MongoInsertBatch();
        new \MongoInsertBatch();
        new MongoDeleteBatch();
        new \MongoDeleteBatch();
        bson_encode();
        \bson_encode();
        bson_decode();
        \bson_decode();
        echo MONGODB_VERSION;
        echo \MONGODB_VERSION;
        echo MONGODB_STABILITY;
        echo \MONGODB_STABILITY;

        PHP,

    // https://github.com/humbug/php-scoper/issues/618
    'NULL anti-regression test' => <<<'PHP'
        <?php

        namespace SebastianBergmann\Template;

        class Template {
            public function setVar(array $values, bool $merge = true): void {}
            public function setVarAlias(array $values, bool $merge = TRUE): void {}
        }
        ----
        <?php

        namespace Humbug\SebastianBergmann\Template;

        class Template
        {
            public function setVar(array $values, bool $merge = \true) : void
            {
            }
            public function setVarAlias(array $values, bool $merge = \TRUE) : void
            {
            }
        }

        PHP,
];
