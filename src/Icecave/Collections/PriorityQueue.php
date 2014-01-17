<?php
namespace Icecave\Collections;

use Icecave\Repr\Repr;
use Serializable;
use SplPriorityQueue;

/**
 * A prioritized first-in/first-out (FIFO) queue of elements.
 *
 * Higher priority values are moved closer to the front of the queue.
 *
 * Prioritization is provided by the prioritzation function specified in the constructor, but may
 * be optionally overridden by the second parameter to {@see PriorityQueue::push()}.
 */
class PriorityQueue extends Queue implements Serializable
{
    /**
     * The default prioritizer simply uses each element as its own priority.
     *
     * @param mixed<mixed>|null $elements    An iterable type containing the elements to include in this list, or null to create an empty list.
     * @param callable|null     $prioritizer A function used to generate the priority for a given element, or null to use the default.
     */
    public function __construct($elements = null, $prioritizer = null)
    {
        if (null === $prioritizer) {
            $prioritizer = function ($element) {
                return $element;
            };
        }
        $this->prioritizer = $prioritizer;

        parent::__construct($elements);
    }

    /**
     * Create a PriorityQueue.
     *
     * @param mixed $element,... Elements to include in the collection.
     *
     * @return PriorityQueue
     */
    public static function create()
    {
        return new static(func_get_args());
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
        if (null === $priority) {
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
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        return $this->elements->extract();
    }

    ////////////////////////////////////
    // Implementation of Serializable //
    ////////////////////////////////////

    /**
     * Serialize the collection.
     *
     * @return string The serialized data.
     */
    public function serialize()
    {
        return serialize(
            array(
                iterator_to_array($this->elements),
                $this->prioritizer
            )
        );
    }

    /**
     * Unserialize collection data.
     *
     * @param string $packet The serialized data.
     */
    public function unserialize($packet)
    {
        list($elements, $prioritizer) = unserialize($packet);
        $this->__construct($elements, $prioritizer);
    }

    /////////////////////////////////////////////////////
    // Implementation of RestrictedComparableInterface //
    /////////////////////////////////////////////////////

    /**
     * Check if $this is able to be compared to another value.
     *
     * A return value of false indicates that calling $this->compare($value)
     * will throw an exception.
     *
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this can be compared to $value.
     */
    public function canCompare($value)
    {
        return is_object($value)
            && __CLASS__ === get_class($value)
            && $this->prioritizer == $value->prioritizer;
    }

    private $prioritizer;
}
