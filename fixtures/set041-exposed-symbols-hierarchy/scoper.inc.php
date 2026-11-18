<?php declare(strict_types=1);

return [
    'exclude-files' => [
        __DIR__.'/index.php',
    ],
    'expose-classes' => [
        'Set041\Polyfill\PhpTokenLike',
    ],
    'exclude-classes' => [
        'StringeableLike',
    ],
];
