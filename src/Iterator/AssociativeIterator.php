<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\AssociativeInterface;
use Iterator;

/**
 * A generic iterator for any collection that implement AssociativeInterface.
 *
 * Note that the concrete implementations provided in this package may provide their own
 * iterator for performance reasons.
 */
class AssociativeIterator implements Iterator
{
    /**
     * @param AssociativeInterface $collection The collection to be iterated.
     */
    public function __construct(AssociativeInterface $collection)
    {
        $this->index = 0;
        $this->collection = $collection;
    }

    /**
     * Fetch the collection to be iterated.
     *
     * @return AssociativeInterface The collection to be iterated.
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
        return $this->collection->get($this->key());
    }

    /**
     * Fetch the current key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        $keys = $this->collection->keys();

        return $keys[$this->index];
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
        return $this->index < $this->collection->size()
            && $this->collection->hasKey($this->key());
    }

    private $index;
    private $collection;
}
