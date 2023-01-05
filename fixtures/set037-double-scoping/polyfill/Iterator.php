<?php

/**
 * Let's polyfill the iterator interface.
 *
 * We use it as example to alias an interface into the root namespace.
 * It will never get loaded for real as it exists on all PHP versions.
 *
 * @link https://php.net/manual/en/class.iterator.php.
 * @link https://github.com/humbug/php-scoper/issues/403
 */
interface Iterator extends Traversable {
    public function current();
    public function next(): void;
    public function key();
    public function valid(): bool;
    public function rewind(): void;
}
