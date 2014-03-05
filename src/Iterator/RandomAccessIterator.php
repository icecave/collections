<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\RandomAccessInterface;
use Iterator;
use SeekableIterator;

/**
 * A generic iterator for any collection that implement RandomAccessInterface.
 *
 * Note that the concrete implementations provided in this package may provide their own
 * iterator for performance reasons.
 */
class RandomAccessIterator implements Iterator, SeekableIterator
{
    /**
     * @param RandomAccessInterface $collection The collection to be iterated.
     */
    public function __construct(RandomAccessInterface $collection)
    {
        $this->index = 0;
        $this->collection = $collection;
    }

    /**
     * Fetch the collection to be iterated.
     *
     * @return RandomAccessInterface The collection to be iterated.
     */
    public function collection()
    {
        return $this->collection;
    }

    /**
     * Fetch the current value.
     *
     * @return mixed The current value.
     */
    public function current()
    {
        return $this->collection->get($this->index);
    }

    /**
     * Fetch the current key.
     *
     * @return mixed The current key.
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
    }

    /**
     * Rewind to the first element.
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Check if the current element is valid.
     *
     * @return boolean True if the iterator points to a valid element; otherwise, false.
     */
    public function valid()
    {
        return $this->index < $this->collection->size();
    }

    /**
     * @param integer $index
     */
    public function seek($index)
    {
        if ($index < 0) {
            $index += $this->collection()->size();
        }

        $this->index = $index;
    }

    private $index;
    private $collection;
}
