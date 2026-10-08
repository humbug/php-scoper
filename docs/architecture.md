## Architecture

1. [Overview](#overview)
1. [Scopers](#scopers)
1. [Scoping PHP files](#scoping-php-files)
1. [Reflector](#reflector)
    1. [Internal symbols](#internal-symbols)
    1. [Enriched Reflector](#enriched-reflector)
1. [Exposed symbols and the scoper autoload](#exposed-symbols-and-the-scoper-autoload)


### Overview

The entry point is `bin/php-scoper`, which boots the console application
(`Console\Application`, built on [fidry/console][fidry-console]). The available
commands are `add-prefix` (the main command), `init`, `inspect` and
`inspect-symbol`.

Services are wired manually in `Container`, a lazy service container. The
`ConfigurationFactory` turns the user's `scoper.inc.php` file into a
`Configuration`, which holds the prefix, the files to scope, the patchers and
the `SymbolsConfiguration` (the exposed and excluded symbols).


### Scopers

A `Scoper` takes a file path and its contents, and returns the scoped
contents. Scopers are decorators: each handles a specific type of file and
delegates all other files to the next scoper. The chain is assembled in
`Scoper\Factory\StandardScoperFactory`:

```
PatchScoper                 applies the user patchers to the scoped contents
└── PhpScoper               PHP files
    └── JsonFileScoper      composer.json autoload configuration
        └── InstalledPackagesScoper   vendor/composer/installed.json
            └── SymfonyScoper         Symfony YAML and XML service configurations
                └── NullScoper        leaves the contents unchanged
```


### Scoping PHP files

`PhpScoper` parses the file with [PHP-Parser][php-parser], traverses the AST
with a set of node visitors and prints the result.

The node visitors are registered in `PhpParser\TraverserFactory`, and their
order matters:

1. Name resolution and attribute appenders (parent node, resolved identifier
   names), on which the subsequent visitors rely.
1. The namespace and use statement prefixers, which also collect the namespace
   and use statements so that names can be resolved later.
1. The symbol recorders, which record the declared exposed classes and
   functions (see [Exposed symbols and the scoper autoload](#exposed-symbols-and-the-scoper-autoload)).
1. The name prefixer, which prefixes class, function and constant references.
1. The string, heredoc/nowdoc and `eval()` prefixers. PHP code within these is
   scoped by calling the scoper again.
1. The visitors that append `class_alias()` statements for exposed classes and
   replace the `const` statements of exposed constants with `define()` calls.

Most of the work lies not in transforming the AST but in deciding _whether_ a
given name should be prefixed. This is the role of the [Reflector](#reflector).


### Reflector

#### Internal symbols

`Symbol\Reflector` identifies _internal_ symbols, i.e. the classes, functions
and constants provided by PHP itself or by a PHP extension. Internal symbols
are never prefixed, as prefixing `strlen()` or `\Exception` would break the
code.

This list cannot be derived from the PHP runtime running PHP-Scoper, as it
depends on the PHP version and the installed extensions. Instead, it is built
from [JetBrains/phpstorm-stubs][phpstorm-stubs] (`PhpStormStubsMap`), which
covers all PHP versions and the most common extensions.

As the stubs are not always complete or up to date, missing symbols are
declared manually in `Reflector` (`MISSING_CLASSES`, `MISSING_FUNCTIONS` and
`MISSING_CONSTANTS`). This is notably required when a new PHP version
introduces new symbols. Additions should be covered in
`tests/Symbol/Reflector/PhpStormStubsReflectorTest.php`.

Users can extend the list of internal symbols with the
[excluded symbols][excluded-symbols] configuration.

As PHP-Scoper scopes itself, the phpstorm-stubs package also requires special
handling when building the PHAR: the stub files must not be scoped, whereas the
namespace of the stubs map must be. The scripts in `res/`, used in
`scoper.inc.php`, take care of this.

#### Enriched Reflector

`Symbol\EnrichedReflector` combines the `Reflector` with the user's
`SymbolsConfiguration` to answer the questions the node visitors ask, for
example "should this class be prefixed?" or "is this function exposed?". It
takes into account:

- the excluded and exposed namespaces (exclusion takes priority);
- the internal symbols;
- the exposed symbols;
- whether the symbols of the global namespace should be exposed.


### Exposed symbols and the scoper autoload

An [exposed symbol][exposed-symbols] is prefixed like any other symbol, but
must remain accessible under its original name.

During scoping, the symbol recorders register the exposed classes and functions
they encounter in a `Symbol\SymbolsRegistry`. Once all files are scoped,
`Autoload\ScoperAutoloadGenerator` uses this registry to generate
`vendor/scoper-autoload.php`, which declares the
[class and function aliases][autoload-aliases] that make the exposed symbols
available under their original names.


<br />
<hr />

« [Limitations](limitations.md#limitations) • [Table of Contents](../README.md#table-of-contents) »


[autoload-aliases]: further-reading.md#autoload-aliases
[excluded-symbols]: configuration.md#excluded-symbols
[exposed-symbols]: configuration.md#exposed-symbols
[fidry-console]: https://github.com/theofidry/console
[php-parser]: https://github.com/nikic/PHP-Parser
[phpstorm-stubs]: https://github.com/JetBrains/phpstorm-stubs
