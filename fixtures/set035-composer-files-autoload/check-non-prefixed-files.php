<?php declare(strict_types=1);

if (!isset($GLOBALS['__composer_autoload_files'])) {
    // This is to mimic a scoped app that may access to the Composer related globals like
    // PHPStan does.
    echo 'Expected to be able to access the composer autoload files!'.PHP_EOL;
    exit(1);
}

$composerAutoloadFiles = $GLOBALS['__composer_autoload_files'];

$expectedPresentComposerAutoloadFiles = [
    'a4a119a56e50fbb293281d9a48007e0e' => true, // vendor/symfony/polyfill-php80/bootstrap.php
    '60884d26763a20c18bdf80c8935efaac' => true, // included-file.php
];
$expectedMissingComposerAutoloadFiles = [
    '430aabe1de335715bfb79e58e8c22198' => true, // excluded-file.php
];

$actualExpectedPresent = array_diff_key(
    $expectedPresentComposerAutoloadFiles,
    $composerAutoloadFiles,
);
$actualExpectedMissing = array_diff_key(
    $expectedMissingComposerAutoloadFiles,
    $composerAutoloadFiles,
);

if (count($actualExpectedPresent) !== 0) {
    echo 'Expected the following hashes to be present:'.PHP_EOL;
    echo var_export($actualExpectedPresent, true).PHP_EOL;
    exit(1);
}

if (count($actualExpectedMissing) !== count($expectedMissingComposerAutoloadFiles)) {
    echo 'Expected the following hashes to be missing:'.PHP_EOL;
    echo var_export($actualExpectedMissing, true).PHP_EOL;
    exit(1);
}

echo 'The hashes are matching.'.PHP_EOL;
