<?php
namespace Icecave\Collections\Iterator;

use IteratorIterator;
use Traversable;

/**
 * Wraps a traversable in an iterator that yields an integer key.
 *
 * It is guaranteed that the internal iterator's key() method is never called.
 */
class SequentialKeyIterator extends IteratorIterator
{
    /**
     * @param Traversable $iterator The collection to be iterated.
     */
    public function __construct(Traversable $iterator)
    {
        parent::__construct($iterator);

        $this->index = 0;
    }

    /**
     * Fetch the current key.
     *
     * @return integer The current key.
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Advance to the next element.
     */
    public function next()
    {
        ++$this->index;
        parent::next();
    }

    /**
     * Rewind to the first element.
     */
    public function rewind()
    {
        $this->index = 0;
        parent::rewind();
    }

    private $index;
}
