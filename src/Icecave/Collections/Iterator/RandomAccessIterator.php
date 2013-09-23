<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\RandomAccessInterface;
use Icecave\Collections\TypeCheck\TypeCheck;
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
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

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

        return $this->collection->get($this->index);
    }

    /**
     * Fetch the current key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        $this->typeCheck->key(func_get_args());

        return $this->index;
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

        return $this->index < $this->collection->size();
    }

    /**
     * @param integer $index
     */
    public function seek($index)
    {
        $this->typeCheck->seek(func_get_args());

        if ($index < 0) {
            $index += $this->collection()->size();
        }

        $this->index = $index;
    }

    private $typeCheck;
    private $index;
    private $collection;
}
