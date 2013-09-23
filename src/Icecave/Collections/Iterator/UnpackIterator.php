<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\TypeCheck\TypeCheck;
use Iterator;
use OuterIterator;

/**
 * An iterator adaptor that produces PHP keys and values from an iterator that
 * produces 2-tuple values in the form (key, value).
 */
class UnpackIterator implements Iterator, OuterIterator
{
    /**
     * @param Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->iterator = $iterator;
    }

    /**
     * @return Iterator
     */
    public function getInnerIterator()
    {
        $this->typeCheck->getInnerIterator(func_get_args());

        return $this->iterator;
    }

    /**
     * Fetch the current value.
     *
     * @return mixed The current value.
     */
    public function current()
    {
        $this->typeCheck->current(func_get_args());

        list($key, $value) = $this->iterator->current();

        return $value;
    }

    /**
     * Fetch the current key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        $this->typeCheck->key(func_get_args());

        list($key, $value) = $this->iterator->current();

        return $key;
    }

    /**
     * Advance to the next element.
     */
    public function next()
    {
        $this->typeCheck->next(func_get_args());

        $this->iterator->next();
    }

    /**
     * Rewind to the first element.
     */
    public function rewind()
    {
        $this->typeCheck->rewind(func_get_args());

        $this->iterator->rewind();
    }

    /**
     * Check if the current element is valid.
     *
     * @return boolean True if the iterator points to a valid element; otherwise, false.
     */
    public function valid()
    {
        $this->typeCheck->valid(func_get_args());

        return $this->iterator->valid();
    }

    private $typeCheck;
    private $iterator;
}
