## Contributing

The project provides a `Makefile` in which the most common commands have been
registered such as fixing the coding style or running the test.

```bash
# Print the list of available commands
make
# or
make help
```

This project has a certain number of unit tests. However because it is tightly
coupled to [PHP-Parser][php-parser] and since [node visitors][node-visitors]
behaviour and effects depends a lot on how they are combined, you can find an
extensive integration test suite for the scoping of PHP files in
[PhpScoperTest][PhpScoperTest] and in peculiar `test_can_scope_valid_files()`.
This tests will collect "spec files" from `_specs` or if none are found in there
`specs`. Those files have the following structure:

```php
<?php declare(strict_types=1);

return [
    'meta' => [
        'title' => 'Title of the specification: this is used to quickly identify what is tested/covered by this file',
        
        // Default configuration value for this file
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-functions' => true,
    ],

    // List of specifications
    [
        'spec' => <<<'SPEC'
This is a multiline spec description.
It can also be a simple string when more readable.
SPEC
        ,
        
        // Each configuration setting defined in "meta" can be overridden
        // here for this specification
        'whitelist' => ['Bar'],
        
        // Content of the specification: this should be the content of a plain
        // PHP file as you can notice by the presence of the opening `<?php`
        // tag. You can also see the `----` delimiter: this is what separate
        // the first part which is the content of the original PHP code and
        // the second part which is the scoped content
        'payload' => <<<'PHP'
<?php declare(strict_types=1);

namespace Acme;

class Foo {}

----
<?php declare (strict_types=1);

namespace Humbug\Acme;

class Foo
{
}

PHP
    ],
    
    // When a specification has no configuration setting that requires to be
    // overridden, the format can be simplified to: 
    'Simple spec description' => <<<'PHP'
    <?php declare(strict_types=1);
    
    namespace Acme;
    
    class Foo {}
    
    ----
    <?php declare (strict_types=1);
    
    namespace Humbug\Acme;
    
    class Foo
    {
    }
    
PHP
    ,
];

```

There is however a lot of specification files which can be tedious to debug. In
order to help, two things have been done:

- On failure, a comprehensive error message is given: the specification title,
  the configuration used, the input file content, the expected result and the
  diff.
- To ease the debugging of one peculiar or a small set of files, the
  specification files can be moved from `specs` to `_specs`. `specs` is only
  used when `_specs` is empty.


Last but not least, scoping is a tricky process and there is a lot of
autoloading related concerns. For this reason end-to-end tests are required.
Those are configured in the `Makefile`. The fixtures are usually declared under
`fixtures/setX` and the result put under `build/setX`. See the `e2e` or `e2e_*`
commands in the `Makefile` for more information.


<br />
<hr />

« [Back to Table of Contents](../README.md#table-of-contents) »


[node-visitors]: https://github.com/humbug/php-scoper/tree/master/src/PhpParser/NodeVisitor
[php-parser]: https://github.com/nikic/PHP-Parser
[PhpScoperTest]: tests/Scoper/PhpScoperTest.php
