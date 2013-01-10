<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\AssociativeInterface;
use Iterator;

class AssociativeIterator implements Iterator
{
    public function __construct(AssociativeInterface $collection)
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
        return $this->collection->get($this->key());
    }

    public function key()
    {
        $keys = $this->collection->keys();
        return $keys[$this->index];
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
        $keys = $this->collection->keys();
        return $this->index < count($keys)
            && $this->collection->hasKey($this->key());
    }

    private $index;
    private $collection;
}
