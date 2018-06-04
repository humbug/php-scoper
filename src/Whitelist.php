<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

use function count;
use Countable;
use function in_array;
use InvalidArgumentException;
use function sprintf;
use function strlen;
use function substr;
use function trim;

final class Whitelist implements Countable
{
    private $classes;
    private $namespaces;

    public static function create(string ...$elements): self
    {
        $classes = [];
        $namespaces = [];

        foreach ($elements as $element) {
            if (isset($element[0]) && '\\' === $element[0]) {
                $element = substr($element, 1);
            }

            if ('' === trim($element)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid whitelist element "%s": cannot accept an empty string',
                        $element
                    )
                );
            }

            $lastElement = $element[strlen($element) - 1];

            if ('*' === $lastElement) {
                $namespaces[] = substr($element, 0, -1);
            } else {
                $classes[] = $element;
            }
        }

        return new self($classes, $namespaces);
    }

    /**
     * @param string[] $classes
     * @param string[] $namespaces
     */
    private function __construct(array $classes, array $namespaces)
    {
        $this->classes = $classes;
        $this->namespaces = $namespaces;
    }

    public function isClassWhitelisted(string $name): bool
    {
        return in_array($name, $this->classes, true);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->classes) + count($this->namespaces);
    }
}