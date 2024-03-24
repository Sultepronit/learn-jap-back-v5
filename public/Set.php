<?php
declare(strict_types=1);

class Set implements IteratorAggregate
{
    public $data = [];

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    public function add(mixed $newEntry): void
    {
        if(!in_array($newEntry, $this->data)) {
            $this->data[] = $newEntry;
        }
    }

    public function has(mixed $item): bool
    {
        return in_array($item, $this->data);
    }
}