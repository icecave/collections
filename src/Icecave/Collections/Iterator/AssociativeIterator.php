<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\AssociativeInterface;
use Icecave\Collections\TypeCheck\TypeCheck;
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
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

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
        $this->typeCheck->collection(func_get_args());

        return $this->collection;
    }

    /**
     * Fetch the current value.
     *
     * @return mixed The current value.
     */
    public function current()
    {
        $this->typeCheck->current(func_get_args());

        return $this->collection->get($this->key());
    }

    /**
     * Fetch the current key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        $this->typeCheck->key(func_get_args());

        $keys = $this->collection->keys();

        return $keys[$this->index];
    }

    /**
     * Advance to the next element.
     */
    public function next()
    {
        $this->typeCheck->next(func_get_args());

        ++$this->index;
    }

    /**
     * Rewind to the first element.
     */
    public function rewind()
    {
        $this->typeCheck->rewind(func_get_args());

        $this->index = 0;
    }

    /**
     * Check if the current element is valid.
     *
     * @return boolean True if the iterator points to a valid element; otherwise, false.
     */
    public function valid()
    {
        $this->typeCheck->valid(func_get_args());

        return $this->index < $this->collection->size()
            && $this->collection->hasKey($this->key());
    }

    private $typeCheck;
    private $index;
    private $collection;
}
