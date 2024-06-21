<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Tests\Iterator;

class Iterator implements \Iterator
{
    protected array $values = [];

    public function __construct(array $values = [])
    {
        foreach ($values as $value) {
            $this->attach(new \SplFileInfo($value));
        }
        $this->rewind();
    }

    public function attach(\SplFileInfo $fileinfo): void
    {
        $this->values[] = $fileinfo;
    }

    public function rewind(): void
    {
        reset($this->values);
    }

    public function valid(): bool
    {
        return false !== $this->current();
    }

    public function next(): void
    {
        next($this->values);
    }

    public function current(): mixed
    {
        return current($this->values);
    }

    public function key(): mixed
    {
        return key($this->values);
    }
}
