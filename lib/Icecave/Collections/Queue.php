<?php
namespace Icecave\Collections;

use Countable;
use Icecave\Collections\TypeCheck\TypeCheck;
use Icecave\Repr\Repr;
use SplQueue;

class Queue implements QueuedAccessInterface, Countable
{
    /**
     * @param mixed<mixed>|null $collection An iterable type containing the elements to include in this list, or null to create an empty list.
     */
    public function __construct($collection = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->clear();

        if (null !== $collection) {
            foreach ($collection as $element) {
                $this->push($element);
            }
        }
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    /**
     * Fetch the number of elements in the collection.
     *
     * @see CollectionInterface::isEmpty()
     *
     * @return integer The number of elements in the collection.
     */
    public function size()
    {
        $this->typeCheck->size(func_get_args());

        return $this->elements->count();
    }

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty()
    {
        $this->typeCheck->isEmpty(func_get_args());

        return $this->elements->isEmpty();
    }

    /**
     * Fetch a string representation of the collection.
     *
     * The string may not describe all elements of the collection, but should at least
     * provide information on the type and state of the collection.
     *
     * @return string A string representation of the collection.
     */
    public function __toString()
    {
        $this->typeCheck->__toString(func_get_args());

        if ($this->isEmpty()) {
            return '<Queue 0>';
        }

        return sprintf(
            '<Queue %d [next: %s]>',
            $this->size(),
            Repr::repr($this->next())
        );
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    /**
     * Remove all elements from the collection.
     */
    public function clear()
    {
        $this->typeCheck->clear(func_get_args());

        $this->elements = new SplQueue;
    }

    /////////////////////////////////////////////
    // Implementation of QueuedAccessInterface //
    /////////////////////////////////////////////

    /**
     * Fetch the element at the front of the queue.
     *
     * @return mixed                              The element at the front of the queue.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function next()
    {
        $this->typeCheck->next(func_get_args());

        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        return $this->elements->bottom();
    }

    /**
     * Fetch the element at the front of the queue.
     *
     * @param mixed &$element Assigned the element at the front of the queue.
     *
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryNext(&$element)
    {
        $this->typeCheck->tryNext(func_get_args());

        if ($this->isEmpty()) {
            return false;
        }

        $element = $this->next();

        return true;
    }

    /**
     * Add a new element to the end of the queue.
     *
     * @param mixed $element The element to add.
     */
    public function push($element)
    {
        $this->typeCheck->push(func_get_args());

        $this->elements->push($element);
    }

    /**
     * Remove and return the element at the front of the queue.
     *
     * @return mixed                              The element at the front of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function pop()
    {
        $this->typeCheck->pop(func_get_args());

        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        return $this->elements->dequeue();
    }

    /**
     * Remove the element at the front of the queue.
     *
     * @param mixed &$element Assigned the removed element.
     *
     * @return boolean True if the front element is removed and assigned to $element; otherwise, false.
     */
    public function tryPop(&$element = null)
    {
        $this->typeCheck->tryPop(func_get_args());

        if ($this->isEmpty()) {
            return false;
        }

        $element = $this->pop();

        return true;
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function count()
    {
        $this->typeCheck->count(func_get_args());

        return $this->size();
    }

    private $typeCheck;
    protected $elements;
}
