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
        'whitelist-global-constants' => true,
        'whitelist-global-functions' => true,
    ],

    'FQCN string argument: transform into a FQCN and prefix it' => <<<'PHP'
<?php

foo('Symfony\\Component\\Yaml\\Yaml');
foo('\\Symfony\\Component\\Yaml\\Yaml');
foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
foo('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

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

\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
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
    ,

    'FQCN string argument on whitelisted class: transform into a FQCN' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml', 'Swift'],
        'payload' => <<<'PHP'
<?php

foo('Symfony\\Component\\Yaml\\Yaml');
foo('\\Symfony\\Component\\Yaml\\Yaml');
foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
foo('\\Humbug\\Symfony\\Component\\Yaml\\Yaml');

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

----
<?php

namespace Humbug;

\Humbug\foo('Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('Humbug\\Symfony\\Component\\Yaml\\Yaml');
\Humbug\foo('DateTime');
\Humbug\foo('Swift');
\Humbug\foo(['DateTime', 'autoload']);
\Humbug\foo(['Swift', 'autoload']);
\spl_autoload_register(['Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['Humbug\\Swift', 'autoload']);
\spl_autoload_register(['DateTime', 'autoload']);
\is_a($swift, 'Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'Humbug\\Swift');
\is_a($swift, 'DateTime');
\is_subclass_of($swift, 'Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'Humbug\\Swift');
\is_subclass_of($swift, 'DateTime');
\is_subclass_of('Humbug\\Mailer', 'Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
\is_subclass_of('Humbug\\Mailer', 'DateTime');
\interface_exists('Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('Humbug\\Swift');
\interface_exists('DateTime');
\class_exists('Swift');
\class_exists('Humbug\\Swift');
\class_exists('Humbug\\Swift');
\class_exists('DateTime');
\trait_exists('Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('Humbug\\Swift');
\trait_exists('DateTime');
\function_exists('Humbug\\dump');
\function_exists('Humbug\\dump');
\function_exists('Humbug\\dump');
\function_exists('var_dump');
\class_alias('Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('Humbug\\Swift', 'Humbug\\Mailer');
\class_alias('DateTime', 'DateTimeInterface');

PHP
    ],

    'FQCN string argument formed by concatenated strings: do nothing' => <<<'PHP'
<?php

foo('Symfony\\Component' . '\\Yaml\\Yaml');
foo('\\Symfony\\Component' . '\\Yaml\\Yaml');

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

\Humbug\foo('Symfony\\Component' . '\\Yaml\\Yaml');
\Humbug\foo('\\Symfony\\Component' . '\\Yaml\\Yaml');
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

    'FQC constant call: transform into FQC call and prefix them' => <<<'PHP'
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

    'FQC constant call on whitelisted class: transform into FQC call' => [
        'whitelist' => ['Symfony\Component\Yaml\Yaml'],
        'payload' => <<<'PHP'
<?php

namespace Symfony\Component\Yaml {
    class Yaml {}
}

namespace {
    foo(Symfony\Component\Yaml\Yaml::class);
    foo(\Symfony\Component\Yaml\Yaml::class);
    foo(Humbug\Symfony\Component\Yaml\Yaml::class);
    foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
}
----
<?php

namespace Humbug\Symfony\Component\Yaml;

class Yaml
{
}
\class_alias('Humbug\\Symfony\\Component\\Yaml\\Yaml', 'Symfony\\Component\\Yaml\\Yaml', \false);
namespace Humbug;

\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
\Humbug\foo(\Humbug\Symfony\Component\Yaml\Yaml::class);

PHP
    ],
];
