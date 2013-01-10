<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\RandomAccessInterface;
use Iterator;

class RandomAccessIterator implements Iterator
{
    public function __construct(RandomAccessInterface $collection)
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
        return $this->collection->get($this->index);
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
        return $this->index < $this->collection->size();
    }

    private $index;
    private $collection;
}
