<?php
namespace Icecave\Collections;

use Icecave\Collections\TypeCheck\TypeCheck;
use Icecave\Repr\Repr;
use Serializable;
use SplPriorityQueue;

/**
 * A prioritized queue.
 *
 * Higher priority values are moved closer to the front of the queue.
 *
 * Prioritization is provided by the prioritzation function specified in the constructor, but may
 * be optionally overridden by the second parameter to {@see PriorityQueue::push()}.
 */
class PriorityQueue extends Queue implements Serializable
{
    /**
     * @param callable          $prioritizer A function used to generate the priority for a given element.
     * @param mixed<mixed>|null $collection  An iterable type containing the elements to include in this list, or null to create an empty list.
     */
    public function __construct($prioritizer, $collection = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->prioritizer = $prioritizer;
        parent::__construct($collection);
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

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
        $this->typeCheck->validateToString(func_get_args());

        if ($this->isEmpty()) {
            return '<PriorityQueue 0>';
        }

        return sprintf(
            '<PriorityQueue %d [next: %s]>',
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

        $this->elements = new SplPriorityQueue;
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

        return $this->elements->top();
    }

    /**
     * Add a new element to the end of the queue.
     *
     * @param mixed        $element  The element to add.
     * @param integer|null $priority The priority of the element being added, or NULL to use the queue's prioritizer.
     */
    public function push($element, $priority = null)
    {
        $this->typeCheck->push(func_get_args());

        if (null == $priority) {
            $priority = call_user_func($this->prioritizer, $element);
        }

        $this->elements->insert($element, $priority);
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

        return $this->elements->extract();
    }

    ////////////////////////////////////
    // Implementation of Serializable //
    ////////////////////////////////////

    /**
     * @return string The serialized data.
     */
    public function serialize()
    {
        $this->typeCheck->serialize(func_get_args());

        return serialize(
            array(
                $this->prioritizer,
                iterator_to_array($this->elements)
            )
        );
    }

    /**
     * @param string $packet The serialized data.
     */
    public function unserialize($packet)
    {
        TypeCheck::get(__CLASS__)->unserialize(func_get_args());

        list($prioritizer, $elements) = unserialize($packet);
        $this->__construct($prioritizer, $elements);
    }

    private $typeCheck;
}
