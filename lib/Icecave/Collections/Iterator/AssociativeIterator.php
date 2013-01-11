<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\AssociativeInterface;
use Icecave\Collections\TypeCheck\Typhoon;
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
     * @param AssociativeInterface $collection The collection to iterate.
     */
    public function __construct(AssociativeInterface $collection)
    {
        $this->typeCheck = Typhoon::get(__CLASS__, func_get_args());

        $this->index = 0;
        $this->collection = $collection;
    }

    /**
     * @return AssociativeInterface The collection to iterate.
     */
    public function collection()
    {
        $this->typeCheck->collection(func_get_args());

        return $this->collection;
    }

    /**
     * @return mixed The current value.
     */
    public function current()
    {
        $this->typeCheck->current(func_get_args());

        return $this->collection->get($this->key());
    }

    /**
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
