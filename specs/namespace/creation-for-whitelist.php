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
		'title' => 'Namespace declaration creation for whitelisted classes',
		// Default values. If not specified will be the one used
		'prefix' => 'Humbug',
		'whitelist' => [
		],
	],

	[
		'spec' => <<<'SPEC'
Single class should receive namespace
SPEC
		,
		'payload' => <<<'PHP'
<?php

class AppKernel
{
}

----
<?php

namespace Humbug;

class AppKernel
{
}

PHP
	],

	[
		'spec' => <<<'SPEC'
Multiple classes should all receive namespace in the same file
SPEC
		,
		'payload' => <<<'PHP'
<?php

class AppKernel
{
}

class AppKernalOther
{
}

class AppKernalOther2
{
}

----
<?php

namespace Humbug;

class AppKernel
{
}
class AppKernalOther
{
}
class AppKernalOther2
{
}

PHP
	],

];
