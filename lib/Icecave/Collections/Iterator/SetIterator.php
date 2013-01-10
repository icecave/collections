<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\Set;
use Iterator;

class SetIterator implements Iterator
{
    public function __construct(Set $collection)
    {
        $this->index = 0;
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function current()
    {
        $elements = $this->collection->elements();
        return $elements[$this->index];
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        ++$this->index;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function valid()
    {
        return $this->index < $this->collection->size()
            && $this->collection->contains($this->current());
    }

    private $index;
    private $collection;
}
