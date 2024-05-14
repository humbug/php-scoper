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
        title: 'Scalar literal used in an expression',
    ),

    'String value used in a comparison expression' => <<<'PHP'
        <?php

        namespace Composer\Package\Loader;

        if ($class !== 'Composer\Package\CompletePackage' && $class !== 'Composer\Package\RootPackage') {
            trigger_error('The $class arg is deprecated, please reach out to Composer maintainers ASAP if you still need this.', E_USER_DEPRECATED);
        }

        ----
        <?php

        namespace Humbug\Composer\Package\Loader;

        if ($class !== 'Humbug\Composer\Package\CompletePackage' && $class !== 'Humbug\Composer\Package\RootPackage') {
            trigger_error('The $class arg is deprecated, please reach out to Composer maintainers ASAP if you still need this.', \E_USER_DEPRECATED);
        }

        PHP,

    'Non valid class name string value used in a comparison expression' => <<<'PHP'
        <?php

        namespace Composer\Package\Loader;

        if ($class !== '1Composer\Package\CompletePackage' && $class !== '0Composer\Package\RootPackage') {
            trigger_error('The $class arg is deprecated, please reach out to Composer maintainers ASAP if you still need this.', E_USER_DEPRECATED);
        }

        ----
        <?php

        namespace Humbug\Composer\Package\Loader;

        if ($class !== '1Composer\Package\CompletePackage' && $class !== '0Composer\Package\RootPackage') {
            trigger_error('The $class arg is deprecated, please reach out to Composer maintainers ASAP if you still need this.', \E_USER_DEPRECATED);
        }

        PHP,
];
