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

use Humbug\PhpScoper\Scoper\Spec\Meta;

return [
    'meta' => new Meta(
        title: 'String literal used as a function argument of class-related functions',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'FQCN string argument' => <<<'PHP'
    <?php
    
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
    
    class_alias('Swift', 'Mailer');
    class_alias('\\Swift', '\\Mailer');
    class_alias('Humbug\\Swift', 'Mailer');
    class_alias('\\Humbug\\Swift', '\\Mailer');
    class_alias('DateTime', 'DateTimeInterface');
    class_alias('\\DateTime', '\\DateTimeInterface');
    
    ----
    <?php
    
    namespace Humbug;
    
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
    \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
    \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
    \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
    \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
    \class_alias('DateTime', 'DateTimeInterface');
    \class_alias('\\DateTime', '\\DateTimeInterface');
    
    PHP,

    'FQCN string argument on exposed class' => [
        exposeClasses: ['Symfony\Component\Yaml\Yaml', 'Swift'],
        'payload' => <<<'PHP'
        <?php
        
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
        
        class_alias('Swift', 'Mailer');
        class_alias('Humbug\Swift', 'Mailer');
        class_alias('\Humbug\Swift', 'Mailer');
        class_alias('DateTime', 'DateTimeInterface');
        
        ----
        <?php
        
        namespace Humbug;
        
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
        \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
        \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
        \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
        \class_alias('DateTime', 'DateTimeInterface');
        
        PHP,
    ],

    'FQCN string argument on class from an excluded namespace' => [
        excludeNamespaces: [
            'Symfony\Component\Yaml',
            '/^$/',
        ],
        'payload' => <<<'PHP'
        <?php
        
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
        
        class_alias('Swift', 'Mailer');
        class_alias('Humbug\Swift', 'Mailer');
        class_alias('\Humbug\Swift', 'Mailer');
        class_alias('DateTime', 'DateTimeInterface');
        
        ----
        <?php
        
        namespace {
            \is_a($swift, 'Swift');
            \is_a($swift, 'Humbug\\Swift');
            \is_a($swift, 'Humbug\\Swift');
            \is_a($swift, 'DateTime');
            \is_subclass_of($swift, 'Swift');
            \is_subclass_of($swift, 'Humbug\\Swift');
            \is_subclass_of($swift, 'Humbug\\Swift');
            \is_subclass_of($swift, 'DateTime');
            \is_subclass_of('Mailer', 'Swift');
            \is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
            \is_subclass_of('Humbug\\Mailer', 'Humbug\\Swift');
            \is_subclass_of('Mailer', 'DateTime');
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
            \class_alias('Swift', 'Mailer');
            \class_alias('Humbug\\Swift', 'Mailer');
            \class_alias('Humbug\\Swift', 'Mailer');
            \class_alias('DateTime', 'DateTimeInterface');
        }
        
        PHP,
    ],

    'FQCN string argument with global functions not exposed' => [
        
        'payload' => <<<'PHP'
        <?php
        
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
        
        class_alias('Swift', 'Mailer');
        class_alias('Humbug\Swift', 'Mailer');
        class_alias('\Humbug\Swift', 'Mailer');
        class_alias('DateTime', 'DateTimeInterface');
        
        ----
        <?php
        
        namespace Humbug;
        
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
        \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
        \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
        \class_alias('Humbug\\Swift', 'Humbug\\Mailer');
        \class_alias('DateTime', 'DateTimeInterface');
        
        PHP,
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
    <?php
    
    is_a($swift, 'Swift'.'');
    is_subclass_of($swift, 'Swift'.'');
    is_subclass_of('Mailer'.'', 'Swift'.'');
    interface_exists('Swift'.'');
    class_exists('Swift'.'');
    trait_exists('Swift'.'');
    class_alias('Swift'.'', 'Mailer'.'');
    
    ----
    <?php
    
    namespace Humbug;
    
    \is_a($swift, 'Swift' . '');
    \is_subclass_of($swift, 'Swift' . '');
    \is_subclass_of('Mailer' . '', 'Swift' . '');
    \interface_exists('Swift' . '');
    \class_exists('Swift' . '');
    \trait_exists('Swift' . '');
    \class_alias('Swift' . '', 'Mailer' . '');
    
    PHP,

    'FQC constant call' => <<<'PHP'
    <?php
    
    namespace Symfony\Component\Yaml {
        class Yaml {}
    }
    
    namespace {
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
    
    PHP,
];
