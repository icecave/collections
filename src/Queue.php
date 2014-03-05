<?php
namespace Icecave\Collections;

use Countable;
use Icecave\Parity\Exception\NotComparableException;
use Icecave\Repr\Repr;
use Serializable;
use SplQueue;

/**
 * A first-in/first-out (FIFO) queue of elements.
 */
class Queue implements QueuedAccessInterface, Countable, Serializable
{
    /**
     * @param mixed<mixed>|null $elements An iterable type containing the elements to include in this list, or null to create an empty list.
     */
    public function __construct($elements = null)
    {
        $this->clear();

        if (null !== $elements) {
            foreach ($elements as $element) {
                $this->push($element);
            }
        }
    }

    public function __clone()
    {
        $this->elements = clone $this->elements;
    }

    /**
     * Create a Queue.
     *
     * @param mixed $element,... Elements to include in the collection.
     *
     * @return Queue
     */
    public static function create()
    {
        return new static(func_get_args());
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

        $element = $this->pop();

        return true;
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function count()
    {
        return $this->size();
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
        return serialize(iterator_to_array($this->elements));
    }

    /**
     * Unserialize collection data.
     *
     * @param string $packet The serialized data.
     */
    public function unserialize($packet)
    {
        $elements = unserialize($packet);
        $this->__construct($elements);
    }

    ///////////////////////////////////////////
    // Implementation of ComparableInterface //
    ///////////////////////////////////////////

    /**
     * Compare this object with another value, yielding a result according to the following table:
     *
     * +--------------------+---------------+
     * | Condition          | Result        |
     * +--------------------+---------------+
     * | $this == $value    | $result === 0 |
     * | $this < $value     | $result < 0   |
     * | $this > $value     | $result > 0   |
     * +--------------------+---------------+
     *
     * @param mixed $value The value to compare.
     *
     * @return integer                                         The result of the comparison.
     * @throws Icecave\Parity\Exception\NotComparableException Indicates that the implementation does not know how to compare $this to $value.
     */
    public function compare($value)
    {
        if (!$this->canCompare($value)) {
            throw new NotComparableException($this, $value);
        }

        return Collection::compare($this->elements, $value->elements);
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
            && __CLASS__ === get_class($value);
    }

    ///////////////////////////////////////////////////
    // Implementation of ExtendedComparableInterface //
    ///////////////////////////////////////////////////

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this == $value.
     */
    public function isEqualTo($value)
    {
        return $this->compare($value) === 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this != $value.
     */
    public function isNotEqualTo($value)
    {
        return $this->compare($value) !== 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this < $value.
     */
    public function isLessThan($value)
    {
        return $this->compare($value) < 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this > $value.
     */
    public function isGreaterThan($value)
    {
        return $this->compare($value) > 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this <= $value.
     */
    public function isLessThanOrEqualTo($value)
    {
        return $this->compare($value) <= 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this >= $value.
     */
    public function isGreaterThanOrEqualTo($value)
    {
        return $this->compare($value) >= 0;
    }

    protected $elements;
}
