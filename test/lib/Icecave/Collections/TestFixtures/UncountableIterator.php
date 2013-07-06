<?php
namespace Icecave\Collections\TestFixtures;

use Iterator;
use ArrayIterator;

class UncountableIterator implements Iterator
{
    public function __construct(array $values)
    {
        $this->values = new ArrayIterator($values);
    }

    public function current()
    {
        return $this->values->current();
    }

    public function key()
    {
        return $this->values->key();
    }

    public function next()
    {
        return $this->values->next();
    }

    public function rewind()
    {
        return $this->values->rewind();
    }

    public function valid()
    {
        return $this->values->valid();
    }

    private $values;
}
