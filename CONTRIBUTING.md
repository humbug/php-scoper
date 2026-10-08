## Contributing

1. [Commands](#commands)
1. [Tests](#tests)
    1. [Specs](#specs)
    1. [End-to-end tests](#end-to-end-tests)

For an overview of how PHP-Scoper works, refer to the
[Architecture][architecture] documentation.


### Commands

The most common commands, such as fixing the coding style or running the tests,
are registered in the project `Makefile`.

```bash
# Print the list of available commands
make
# or
make help
```

Install the dependencies with `make vendor` rather than `composer install`, as
it first sets `COMPOSER_ROOT_VERSION` from `.composer-root-version`. This file
is kept up to date by the `composer-root-version-checker` sub-project
(`make composer_root_version_update`). Alternatively, [direnv] loads the
`.envrc` file, which includes `COMPOSER_ROOT_VERSION`.

The development tools (PHPStan, Rector and PHP-CS-Fixer) are installed in
isolation under `vendor-bin/` with
[bamarni/composer-bin-plugin][composer-bin-plugin].

To run a single PHPUnit test:

```bash
bin/phpunit tests/Path/To/SomeTest.php
bin/phpunit --filter=test_name
```


### Tests

#### Specs

The project has unit tests. However, PHP-Scoper is tightly coupled to
[PHP-Parser][php-parser], and the behaviour of the
[node visitors][node-visitors] depends largely on how they are combined. For
this reason, the scoping of PHP files is covered by an extensive integration
test suite: [PhpScoperSpecTest][PhpScoperSpecTest], in particular
`test_can_scope_valid_files()`. This test collects the specification files
from `_specs` or, if that directory is empty, from `specs`. These files have
the following structure:

```php
<?php declare(strict_types=1);

return [
    'meta' => [
        'title' => 'Title of the specification: used to quickly identify what this file covers',
        
        // Default configuration values for this file
        'prefix' => 'Humbug',
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

    // List of specifications
    [
        'spec' => <<<'SPEC'
            This is a multiline spec description.
            It can also be a simple string when more readable.
            SPEC
        ,
        
        // Any configuration setting defined in "meta" can be overridden here
        // for this specification
        'expose-global-constants' => true,
        
        // Content of the specification: the content of a plain PHP file, hence
        // the opening `<?php` tag. The `----` delimiter separates the original
        // PHP code (first part) from the scoped code (second part)
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
    
    // When a specification does not override any configuration setting, the
    // format can be simplified to:
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
        
    PHP,
];

```

As the number of specification files is large, debugging them can be tedious.
Two measures help with this:

- On failure, the error message is comprehensive: it includes the
  specification title, the configuration used, the input file content, the
  expected result and the diff.
- To debug a single file or a small set of files, move the specification files
  from `specs` to `_specs`, as `specs` is only used when `_specs` is empty.
  Remember to move them back afterwards: the CI ensures `_specs` is empty.


#### End-to-end tests

Scoping is a delicate process with many autoloading concerns, which is why
end-to-end tests are required. They are configured in the `Makefile`. The
fixtures are usually declared under `fixtures/setX` and the results written to
`build/setX`. Most of them build a scoped PHAR with [Box][box] and compare its
output with the fixture `expected-output` file. Refer to the `e2e` and `e2e_*`
commands in the `Makefile` for more details.

A new end-to-end test must be registered in:

- `.makefile/e2e.file`, which declares the `e2e_XXX` rule;
- the `e2e` rule of the `Makefile`;
- the GitHub Actions workflow `.github/workflows/e2e-tests.yaml`.

The tests under `tests/AutoReview` check that these remain in sync.


<br />
<hr />

« [Back to Table of Contents](README.md#table-of-contents) »


[architecture]: docs/architecture.md#architecture
[box]: https://github.com/humbug/box
[composer-bin-plugin]: https://github.com/bamarni/composer-bin-plugin
[direnv]: https://direnv.net/
[node-visitors]: https://github.com/humbug/php-scoper/tree/master/src/PhpParser/NodeVisitor
[php-parser]: https://github.com/nikic/PHP-Parser
[PhpScoperSpecTest]: tests/Scoper/PhpScoperSpecTest.php
