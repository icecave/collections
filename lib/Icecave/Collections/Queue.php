<?php
namespace Icecave\Collections;

use Icecave\Collections\Support\Stringify;
use SplQueue;

class Queue implements IQueuedAccess
{
    /**
     * @param traversable|null $collection An iterable type containing the elements to include in this list, or null to create an empty list.
     */
    public function __construct($collection = null)
    {
        $this->clear();

        if (null !== $collection) {
            foreach ($collection as $element) {
                $this->push($element);
            }
        }
    }

    ///////////////////////////////////
    // Implementation of ICollection //
    ///////////////////////////////////

    /**
     * Fetch the number of elements in the collection.
     *
     * @see ICollection::isEmpty()
     *
     * @return integer The number of elements in the collection.
     */
    public function size()
    {
        return $this->elements->count();
    }

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty()
    {
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
        if ($this->isEmpty()) {
            return '<Queue 0>';
        }

        return sprintf(
            '<Queue %d [next: %s]>',
            $this->size(),
            Stringify::stringify($this->next())
        );
    }

    //////////////////////////////////////////
    // Implementation of IMutableCollection //
    //////////////////////////////////////////

    /**
     * Remove all elements from the collection.
     */
    public function clear()
    {
        $this->elements = new SplQueue;
    }

    /////////////////////////////////////
    // Implementation of IQueuedAccess //
    /////////////////////////////////////

    /**
     * Fetch the element at the front of the queue.
     *
     * @return mixed The element at the front of the queue.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function next()
    {
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        return $this->elements->bottom();
    }

    /**
     * Fetch the element at the front of the queue.
     *
     * @param mixed &$element Assigned the element at the front of the queue.
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryNext(&$element)
    {
        if ($this->isEmpty()) {
            return false;
        }

        $element = $this->elements->bottom();

        return true;
    }

    /**
     * Add a new element to the end of the queue.
     *
     * @param mixed $element The element to add.
     */
    public function push($element)
    {
        $this->elements->push($element);
    }

    /**
     * Remove and return the element at the front of the queue.
     *
     * @return mixed The element at the front of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function pop()
    {
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
        if ($this->isEmpty()) {
            return false;
        }

        $element = $this->elements->dequeue();

        return true;
    }

    private $elements;
}
