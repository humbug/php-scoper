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
        'title' => 'Date related functions/calls',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'date values' => <<<'PHP'
<?php

const ISO8601_BASIC = 'Ymd\THis\Z';

new Foo('d\H\Z');
new DateTime('d\H\Z');
new DateTimeImmutable('d\H\Z');
date_create('d\H\Z');
date('d\H\Z');
gmdate('d\H\Z');

DateTime::createFromFormat('d\H\Z', '15\Feb\2009');
DateTimeImmutable::createFromFormat('d\H\Z', '15\Feb\2009');
date_create_from_format('d\H\Z', '15\Feb\2009');

(new DateTime('now'))->format('d\H\Z');
date_format(new DateTime('now'), 'd\H\Z');

----
<?php

namespace Humbug;

const ISO8601_BASIC = 'Humbug\\Ymd\\THis\\Z';
new \Humbug\Foo('Humbug\\d\\H\\Z');
new \DateTime('d\\H\\Z');
new \DateTimeImmutable('d\\H\\Z');
\date_create('d\\H\\Z');
\date('d\\H\\Z');
\gmdate('d\\H\\Z');
\DateTime::createFromFormat('d\\H\\Z', '15\\Feb\\2009');
\DateTimeImmutable::createFromFormat('d\\H\\Z', '15\\Feb\\2009');
\date_create_from_format('d\\H\\Z', '15\\Feb\\2009');
(new \DateTime('now'))->format('Humbug\\d\\H\\Z');
\date_format(new \DateTime('now'), 'Humbug\\d\\H\\Z');

PHP
    ,

    'date values in a namespace' => <<<'PHP'
<?php

namespace Acme;

use DateTime;
use DateTimeImmutable;

const ISO8601_BASIC = 'Ymd\THis\Z';

new Foo('d\H\Z');
new DateTime('d\H\Z');
new DateTimeImmutable('d\H\Z');
date_create('d\H\Z');
date('d\H\Z');
gmdate('d\H\Z');

DateTime::createFromFormat('d\H\Z', '15\Feb\2009');
DateTimeImmutable::createFromFormat('d\H\Z', '15\Feb\2009');
date_create_from_format('d\H\Z', '15\Feb\2009');

(new DateTime('now'))->format('d\H\Z');
date_format(new DateTime('now'), 'd\H\Z');

----
<?php

namespace Humbug\Acme;

use DateTime;
use DateTimeImmutable;
const ISO8601_BASIC = 'Humbug\\Ymd\\THis\\Z';
new \Humbug\Acme\Foo('Humbug\\d\\H\\Z');
new \DateTime('d\\H\\Z');
new \DateTimeImmutable('d\\H\\Z');
\date_create('d\\H\\Z');
\date('d\\H\\Z');
\gmdate('d\\H\\Z');
\DateTime::createFromFormat('d\\H\\Z', '15\\Feb\\2009');
\DateTimeImmutable::createFromFormat('d\\H\\Z', '15\\Feb\\2009');
\date_create_from_format('d\\H\\Z', '15\\Feb\\2009');
(new \DateTime('now'))->format('Humbug\\d\\H\\Z');
\date_format(new \DateTime('now'), 'Humbug\\d\\H\\Z');

PHP
    ,
];
