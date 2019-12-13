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
        'title' => 'String literal used as a function argument',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => false,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'FQCN string argument' => <<<'PHP'
<?php

foo('Symfony\\Component\\Yaml\\Ya_1');
foo('\\Symfony\\Component\\Yaml\\Ya_1');
foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');

foo('DateTime');
foo('\\DateTime');
foo('Swift');
foo('\\Swift');
foo(['DateTime', 'autoload']);
foo(['\\DateTime', 'autoload']);
foo(['Swift', 'autoload']);
foo(['\\Swift', 'autoload']);

spl_autoload_register(['Swift', 'autoload']);
spl_autoload_register(['\Swift', 'autoload']);
spl_autoload_register(['Humbug\\Swift', 'autoload']);
spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
spl_autoload_register(['DateTime', 'autoload']);
spl_autoload_register(['\\DateTime', 'autoload']);

is_a($swift, 'Swift');
is_a($swift, '\\Swift');
is_a($swift, 'Humbug\\Swift');
is_a($swift, '\\Humbug\\Swift');
is_a($swift, 'DateTime');
is_a($swift, '\\DateTime');

is_subclass_of($swift, 'Swift');
is_subclass_of($swift, '\\Swift');
is_subclass_of($swift, 'Humbug\\Swift');
is_subclass_of($swift, '\\Humbug\\Swift');
is_subclass_of($swift, 'DateTime');
is_subclass_of($swift, '\\DateTime');
is_subclass_of('Mailer', 'Swift');
is_subclass_of('\\Mailer', '\\Swift');
is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
is_subclass_of('\\Humbug\\Mailer', '\\Humbug\\Swift');
is_subclass_of('Mailer', 'DateTime');
is_subclass_of('\\Mailer', '\\DateTime');

interface_exists('Swift');
interface_exists('\\Swift');
interface_exists('Humbug\\Swift');
interface_exists('\\Humbug\\Swift');
interface_exists('DateTime');
interface_exists('\\DateTime');

class_exists('Swift');
class_exists('\\Swift');
class_exists('Humbug\\Swift');
class_exists('\\Humbug\\Swift');
class_exists('DateTime');
class_exists('\\DateTime');

trait_exists('Swift');
trait_exists('\\Swift');
trait_exists('Humbug\\Swift');
trait_exists('\\Humbug\\Swift');
trait_exists('DateTime');
trait_exists('\\DateTime');

function_exists('dump');
function_exists('Humbug\\dump');
function_exists('\Humbug\\dump');
function_exists('var_dump');

class_alias('Swift', 'Mailer');
class_alias('\\Swift', '\\Mailer');
class_alias('Humbug\\Swift', 'Mailer');
class_alias('\\Humbug\\Swift', '\\Mailer');
class_alias('DateTime', 'DateTimeInterface');
class_alias('\\DateTime', '\\DateTimeInterface');

($this->colorize)('fg-green', '✔');
($this->colorize)(['Soft', 'autoload']);
($this->colorize)(['\\Soft', 'autoload']);

----
<?php

namespace Humbug;

\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('DateTime');
\Humbug\foo('\\DateTime');
\Humbug\foo('Swift');
\Humbug\foo('\\Swift');
\Humbug\foo(['DateTime', 'autoload']);
\Humbug\foo(['\\DateTime', 'autoload']);
\Humbug\foo(['Swift', 'autoload']);
\Humbug\foo(['\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['DateTime', 'autoload']);
\spl_autoload_register(['\\DateTime', 'autoload']);
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'DateTime');
\is_a($swift, '\\DateTime');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'DateTime');
\is_subclass_of($swift, '\\DateTime');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'DateTime');
\is_subclass_of('Humbug\\Mailer', '\\DateTime');
\interface_exists('Humbug\\Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('DateTime');
\interface_exists('\\DateTime');
\class_exists('Humbug\\Swift');
\class_exists('Humbug\\Swift');
\class_exists('Humbug\\Swift');
\class_exists('Humbug\\Swift');
\class_exists('DateTime');
\class_exists('\\DateTime');
\trait_exists('Humbug\\Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('DateTime');
\trait_exists('\\DateTime');
\function_exists('Humbug\\dump');
\function_exists('Humbug\\dump');
\function_exists('Humbug\\dump');
\function_exists('var_dump');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('DateTime', 'DateTimeInterface');
\class_alias('\\DateTime', '\\DateTimeInterface');
($this->colorize)('fg-green', '✔');
($this->colorize)(['Soft', 'autoload']);
($this->colorize)(['\\Soft', 'autoload']);

PHP
    ,

    'FQCN string argument on whitelisted class' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml', 'Swift'],
        'payload' => <<<'PHP'
<?php

foo('Symfony\\Component\\Yaml\\Ya_1');
foo('\\Symfony\\Component\\Yaml\\Ya_1');
foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');

foo('DateTime');
foo('Swift');
foo(['DateTime', 'autoload']);
foo(['Swift', 'autoload']);

(function($x = 'Symfony\\Component\\Yaml\\Ya_1') {})();
(function($x = '\\Symfony\\Component\\Yaml\\Ya_1') {})();
(function($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();
(function($x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1') {})();

(function($x = 'DateTime') {})();
(function($x = 'Swift') {})();
(function($x = ['DateTime', 'autoload']) {})();
(function($x = ['Swift', 'autoload']) {})();

spl_autoload_register(['Swift', 'autoload']);
spl_autoload_register(['Humbug\\Swift', 'autoload']);
spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
spl_autoload_register(['DateTime', 'autoload']);

is_a($swift, 'Swift');
is_a($swift, 'Humbug\\Swift');
is_a($swift, '\\Humbug\\Swift');
is_a($swift, 'DateTime');

is_subclass_of($swift, 'Swift');
is_subclass_of($swift, 'Humbug\Swift');
is_subclass_of($swift, '\Humbug\Swift');
is_subclass_of($swift, 'DateTime');
is_subclass_of('Mailer', 'Swift');
is_subclass_of('Humbug\Mailer', 'Humbug\Swift');
is_subclass_of('\Humbug\Mailer', '\Humbug\Swift');
is_subclass_of('Mailer', 'DateTime');

interface_exists('Swift');
interface_exists('Humbug\Swift');
interface_exists('\Humbug\Swift');
interface_exists('DateTime');

class_exists('Swift');
class_exists('Humbug\Swift');
class_exists('\Humbug\Swift');
class_exists('DateTime');

trait_exists('Swift');
trait_exists('Humbug\Swift');
trait_exists('\Humbug\Swift');
trait_exists('DateTime');

function_exists('dump');
function_exists('Humbug\dump');
function_exists('\Humbug\dump');
function_exists('var_dump');

class_alias('Swift', 'Mailer');
class_alias('Humbug\Swift', 'Mailer');
class_alias('\Humbug\Swift', 'Mailer');
class_alias('DateTime', 'DateTimeInterface');

----
<?php

namespace Humbug;

\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('DateTime');
\Humbug\foo('Swift');
\Humbug\foo(['DateTime', 'autoload']);
\Humbug\foo(['Swift', 'autoload']);
(function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
})();
(function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
})();
(function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
})();
(function ($x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1') {
})();
(function ($x = 'DateTime') {
})();
(function ($x = 'Swift') {
})();
(function ($x = ['DateTime', 'autoload']) {
})();
(function ($x = ['Swift', 'autoload']) {
})();
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['DateTime', 'autoload']);
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'DateTime');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'DateTime');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'DateTime');
\interface_exists('Humbug\\Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('DateTime');
\class_exists('Humbug\\Swift');
\class_exists('Humbug\\Swift');
\class_exists('Humbug\\Swift');
\class_exists('DateTime');
\trait_exists('Humbug\\Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('DateTime');
\function_exists('Humbug\\dump');
\function_exists('Humbug\\dump');
\function_exists('Humbug\\dump');
\function_exists('var_dump');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('DateTime', 'DateTimeInterface');

PHP
    ],

    'FQCN string argument with global functions not whitelisted' => [
        'whitelist-global-functions' => false,
        'payload' => <<<'PHP'
<?php

foo('Symfony\\Component\\Yaml\\Ya_1');
foo('\\Symfony\\Component\\Yaml\\Ya_1');
foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');

foo('DateTime');
foo('Swift');
foo(['DateTime', 'autoload']);
foo(['Swift', 'autoload']);

spl_autoload_register(['Swift', 'autoload']);
spl_autoload_register(['Humbug\\Swift', 'autoload']);
spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
spl_autoload_register(['DateTime', 'autoload']);

is_a($swift, 'Swift');
is_a($swift, 'Humbug\\Swift');
is_a($swift, '\\Humbug\\Swift');
is_a($swift, 'DateTime');

is_subclass_of($swift, 'Swift');
is_subclass_of($swift, 'Humbug\Swift');
is_subclass_of($swift, '\Humbug\Swift');
is_subclass_of($swift, 'DateTime');
is_subclass_of('Mailer', 'Swift');
is_subclass_of('Humbug\Mailer', 'Humbug\Swift');
is_subclass_of('\Humbug\Mailer', '\Humbug\Swift');
is_subclass_of('Mailer', 'DateTime');

interface_exists('Swift');
interface_exists('Humbug\Swift');
interface_exists('\Humbug\Swift');
interface_exists('DateTime');

class_exists('Swift');
class_exists('Humbug\Swift');
class_exists('\Humbug\Swift');
class_exists('DateTime');

trait_exists('Swift');
trait_exists('Humbug\Swift');
trait_exists('\Humbug\Swift');
trait_exists('DateTime');

function_exists('dump');
function_exists('Humbug\dump');
function_exists('\Humbug\dump');
function_exists('var_dump');

class_alias('Swift', 'Mailer');
class_alias('Humbug\Swift', 'Mailer');
class_alias('\Humbug\Swift', 'Mailer');
class_alias('DateTime', 'DateTimeInterface');

($this->colorize)('fg-green', '✔');
($this->colorize)(['Soft', 'autoload']);

----
<?php

namespace Humbug;

\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
\Humbug\foo('DateTime');
\Humbug\foo('Swift');
\Humbug\foo(['DateTime', 'autoload']);
\Humbug\foo(['Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['DateTime', 'autoload']);
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'DateTime');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'DateTime');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'DateTime');
\interface_exists('Humbug\\Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('DateTime');
\class_exists('Humbug\\Swift');
\class_exists('Humbug\\Swift');
\class_exists('Humbug\\Swift');
\class_exists('DateTime');
\trait_exists('Humbug\\Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('DateTime');
\function_exists('Humbug\\dump');
\function_exists('Humbug\\dump');
\function_exists('Humbug\\dump');
\function_exists('var_dump');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('DateTime', 'DateTimeInterface');
($this->colorize)('fg-green', '✔');
($this->colorize)(['Soft', 'autoload']);

PHP
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
<?php

foo('Symfony\\Component' . '\\Yaml\\Ya_1');
foo('\\Symfony\\Component' . '\\Yaml\\Ya_1');

foo('Swift'.'');
spl_autoload_register(['Swift'.'', 'autoload']);
is_a($swift, 'Swift'.'');
is_subclass_of($swift, 'Swift'.'');
is_subclass_of('Mailer'.'', 'Swift'.'');
interface_exists('Swift'.'');
class_exists('Swift'.'');
trait_exists('Swift'.'');
function_exists('dump'.'');
class_alias('Swift'.'', 'Mailer'.'');

----
<?php

namespace Humbug;

\Humbug\foo('Symfony\\Component' . '\\Yaml\\Ya_1');
\Humbug\foo('\\Symfony\\Component' . '\\Yaml\\Ya_1');
\Humbug\foo('Swift' . '');
\spl_autoload_register(['Swift' . '', 'autoload']);
\is_a($swift, 'Swift' . '');
\is_subclass_of($swift, 'Swift' . '');
\is_subclass_of('Mailer' . '', 'Swift' . '');
\interface_exists('Swift' . '');
\class_exists('Swift' . '');
\trait_exists('Swift' . '');
\function_exists('dump' . '');
\class_alias('Swift' . '', 'Mailer' . '');

PHP
    ,

    'FQC constant call' => <<<'PHP'
<?php

namespace Symfony\Component\Yaml {
    class Yaml {}
}

namespace {
    foo(Symfony\Component\Yaml\Yaml::class);
    foo(\Symfony\Component\Yaml\Yaml::class);
    foo(Humbug\Symfony\Component\Yaml\Yaml::class);
    foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
    
    foo(\DateTime::class);
    foo(\Swift::class);
    foo([\DateTime::class, 'autoload']);
    foo([\Swift::class, 'autoload']);
    
    spl_autoload_register([\Swift::class, 'autoload']);
    spl_autoload_register([\Humbug\Swift::class, 'autoload']);
    spl_autoload_register([\DateTime::class, 'autoload']);
    
    is_a($swift, \Swift::class);
    is_a($swift, \Humbug\Swift::class);
    is_a($swift, \DateTime::class);
    
    is_subclass_of($swift, \Swift::class);
    is_subclass_of($swift, \Humbug\Swift::class);
    is_subclass_of($swift, \DateTime::class);
    is_subclass_of(\Mailer::class, \Swift::class);
    is_subclass_of(\Humbug\Mailer::class, \Humbug\Swift::class);
    is_subclass_of(\Mailer::class, \DateTime::class);
    
    interface_exists(\Swift::class);
    interface_exists(\Humbug\Swift::class);
    interface_exists(\DateTime::class);
    
    class_exists(\Swift::class);
    class_exists(\Humbug\Swift::class);
    class_exists(\DateTime::class);
    
    trait_exists(\Swift::class);
    trait_exists(\Humbug\Swift::class);
    trait_exists(\DateTime::class);
    
    class_alias(\Swift::class, \Mailer::class);
    class_alias(\Humbug\Swift::class, \Mailer::class);
    class_alias(\DateTime::class, \DateTimeInterface::class);
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Yaml
{
}
namespace Humbug;

\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\DateTime::class);
\Humbug\foo(\Humbug\Swift::class);
\Humbug\foo([\DateTime::class, 'autoload']);
\Humbug\foo([\Humbug\Swift::class, 'autoload']);
\spl_autoload_register([\Humbug\Swift::class, 'autoload']);
\spl_autoload_register([\Humbug\Swift::class, 'autoload']);
\spl_autoload_register([\DateTime::class, 'autoload']);
\is_a($swift, \Humbug\Swift::class);
\is_a($swift, \Humbug\Swift::class);
\is_a($swift, \DateTime::class);
\is_subclass_of($swift, \Humbug\Swift::class);
\is_subclass_of($swift, \Humbug\Swift::class);
\is_subclass_of($swift, \DateTime::class);
\is_subclass_of(\Humbug\Mailer::class, \Humbug\Swift::class);
\is_subclass_of(\Humbug\Mailer::class, \Humbug\Swift::class);
\is_subclass_of(\Humbug\Mailer::class, \DateTime::class);
\interface_exists(\Humbug\Swift::class);
\interface_exists(\Humbug\Swift::class);
\interface_exists(\DateTime::class);
\class_exists(\Humbug\Swift::class);
\class_exists(\Humbug\Swift::class);
\class_exists(\DateTime::class);
\trait_exists(\Humbug\Swift::class);
\trait_exists(\Humbug\Swift::class);
\trait_exists(\DateTime::class);
\class_alias(\Humbug\Swift::class, \Humbug\Mailer::class);
\class_alias(\Humbug\Swift::class, \Humbug\Mailer::class);
\class_alias(\DateTime::class, \DateTimeInterface::class);

PHP
    ,

    'FQC constant call on whitelisted class' => [
        'whitelist' => ['Symfony\Component\Yaml\Ya_1'],
        'registered-classes' => [
            ['Symfony\Component\Yaml\Ya_1', 'Humbug\Symfony\Component\Yaml\Ya_1'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Symfony\Component\Yaml {
    class Ya_1 {}
}

namespace {
    foo(Symfony\Component\Yaml\Ya_1::class);
    foo(\Symfony\Component\Yaml\Ya_1::class);
    foo(Humbug\Symfony\Component\Yaml\Ya_1::class);
    foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Ya_1
{
}
\class_alias('Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Symfony\\Component\\Yaml\\Ya_1', \false);
namespace Humbug;

\Humbug\foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);

PHP
    ],
];
