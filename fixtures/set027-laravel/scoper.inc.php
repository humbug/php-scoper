<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

$consoleViewFiles = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathname(),
    iterator_to_array(
        Finder::create()
            ->in('vendor/laravel/framework/src/Illuminate/Console/resources/views')
            ->files(),
        false,
    ),
);

return [
    'exclude-files' => [
        ...$consoleViewFiles,
    ],
    'patchers' => [
        static function (string $filePath, string $prefix, string $contents): string {
            if (!str_ends_with($filePath, 'vendor/laravel/framework/src/Illuminate/Console/View/Components/Factory.php')) {
                return $contents;
            }

            return str_replace(
                '$component = \'\\Illuminate\\Console\\View\\Components\\\\\' . ucfirst($method);',
                '$component = \'\\\\'.$prefix.'\\\\Illuminate\\\\Console\\\\View\\\\Components\\\\\' . ucfirst($method);',
                $contents,
            );
        },
    ],
];
