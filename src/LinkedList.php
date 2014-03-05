<?php
namespace Icecave\Collections;

use Countable;
use Icecave\Collections\Iterator\Traits;
use Icecave\Parity\Exception\NotComparableException;
use InvalidArgumentException;
use IteratorAggregate;
use Serializable;
use stdClass;

/**
 * A mutable sequence with efficient addition and removal of elements.
 */
class LinkedList implements MutableRandomAccessInterface, Countable, IteratorAggregate, Serializable
{
    /**
     * @param mixed<mixed>|null $elements An iterable type containing the elements to include in this list, or null to create an empty list.
     */
    public function __construct($elements = null)
    {
        $this->clear();

        if (null !== $elements) {
            $this->insertMany(0, $elements);
        }
    }

    public function __clone()
    {
        $node = $this->head;
        $prev = null;

        while ($node) {

            // Clone the node ...
            $newNode = clone $node;

            // If there was a previous node, create the link ...
            if ($prev) {
                $prev->next = $newNode;
                $newNode->prev = $prev;

            // Otherwise this must be the head ...
            } else {
                $this->head = $newNode;
            }

            $prev = $newNode;
            $node = $node->next;
        }

        $this->tail = $prev;
    }

    /**
     * Create a LinkedList.
     *
     * @param mixed $element,... Elements to include in the collection.
     *
     * @return LinkedList
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
        return $this->size;
    }

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty()
    {
        return null === $this->head;
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
            return '<LinkedList 0>';
        }

        $elements = $this
            ->slice(0, 3)
            ->map('Icecave\Repr\Repr::repr');

        if ($this->size > 3) {
            $format = '<LinkedList %d [%s, ...]>';
        } else {
            $format = '<LinkedList %d [%s]>';
        }

        return sprintf(
            $format,
            $this->size,
            implode(', ', $elements->elements())
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
        $this->head = null;
        $this->tail = null;
        $this->size = 0;
    }

    //////////////////////////////////////////////
    // Implementation of IteratorTraitsProvider //
    //////////////////////////////////////////////

    /**
     * Return traits describing the collection's iteration capabilities.
     *
     * @return Traits
     */
    public function iteratorTraits()
    {
        return new Traits(true, true);
    }

    /////////////////////////////////////////
    // Implementation of IterableInterface //
    /////////////////////////////////////////

    /**
     * Fetch a native array containing the elements in the collection.
     *
     * @return array An array containing the elements in the collection.
     */
    public function elements()
    {
        $elements = array();

        for ($node = $this->head; null !== $node; $node = $node->next) {
            $elements[] = $node->element;
        }

        return $elements;
    }

    /**
     * Check if the collection contains an element.
     *
     * @param mixed $element The element to check.
     *
     * @return boolean True if the collection contains $element; otherwise, false.
     */
    public function contains($element)
    {
        for ($node = $this->head; null !== $node; $node = $node->next) {
            if ($element === $node->element) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to include, or null to include all non-null elements.
     *
     * @return LinkedList The filtered collection.
     */
    public function filter($predicate = null)
    {
        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $result = new static;

        for ($node = $this->head; null !== $node; $node = $node->next) {
            if (call_user_func($predicate, $node->element)) {
                $result->pushBack($node->element);
            }
        }

        return $result;
    }

    /**
     * Produce a new collection by applying a transformation to each element.
     *
     * The new elements produced by the transform need not be of the same type.
     * It is not guaranteed that the concrete type of the resulting collection will match this collection.
     *
     * @param callable $transform The transform to apply to each element.
     *
     * @return IterableInterface A new collection produced by applying $transform to each element in this collection.
     */
    public function map($transform)
    {
        $result = new static;

        for ($node = $this->head; null !== $node; $node = $node->next) {
            $result->pushBack(call_user_func($transform, $node->element));
        }

        return $result;
    }

    /**
     * Partitions this collection into two collections according to a predicate.
     *
     * It is not guaranteed that the concrete type of the partitioned collections will match this collection.
     *
     * @param callable $predicate A predicate function used to determine which partitioned collection to place the elements in.
     *
     * @return tuple<IterableInterface,IterableInterface> A 2-tuple containing the partitioned collections. The first collection contains the element for which the predicate returned true.
     */
    public function partition($predicate)
    {
        $left = new static;
        $right = new static;

        for ($node = $this->head; null !== $node; $node = $node->next) {
            if (call_user_func($predicate, $node->element)) {
                $left->pushBack($node->element);
            } else {
                $right->pushBack($node->element);
            }
        }

        return array($left, $right);
    }

    /**
     * Invokes the given callback on every element in the collection.
     *
     * This method behaves the same as {@see IterableInterface::map()} except that the return value of the callback is not retained.
     *
     * @param callable $callback The callback to invoke with each element.
     */
    public function each($callback)
    {
        for ($node = $this->head; null !== $node; $node = $node->next) {
            call_user_func($callback, $node->element);
        }
    }

    /**
     * Returns true if the given predicate returns true for all elements.
     *
     * The loop is short-circuited, exiting after the first element for which the predicate returns false.
     *
     * @param callable $predicate
     *
     * @return boolean True if $predicate($element) returns true for all elements; otherwise, false.
     */
    public function all($predicate)
    {
        for ($node = $this->head; null !== $node; $node = $node->next) {
            if (!call_user_func($predicate, $node->element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if the given predicate returns true for any element.
     *
     * The loop is short-circuited, exiting after the first element for which the predicate returns false.
     *
     * @param callable $predicate
     *
     * @return boolean True if $predicate($element) returns true for any element; otherwise, false.
     */
    public function any($predicate)
    {
        for ($node = $this->head; null !== $node; $node = $node->next) {
            if (call_user_func($predicate, $node->element)) {
                return true;
            }
        }

        return false;
    }

    ////////////////////////////////////////////////
    // Implementation of MutableIterableInterface //
    ////////////////////////////////////////////////

    /**
     * Filter this collection in-place.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to retain, or null to retain all non-null elements.
     */
    public function filterInPlace($predicate = null)
    {
        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $node = $this->head;
        $prev = null;

        while ($node) {

            // Keep the node ...
            if (call_user_func($predicate, $node->element)) {
                $prev = $node;
            // Don't keep the node, and it's the first one ...
            } elseif (null === $prev) {
                $this->head = $node->next;
                $this->head->prev = null;
                --$this->size;
            // Don't keep the node ...
            } else {
                $prev->next = $node->next;
                $node->next->prev = $prev;
                --$this->size;
            }

            $node = $node->next;
        }

        $this->tail = $prev;
    }

    /**
     * Replace each element in the collection with the result of a transformation on that element.
     *
     * The new elements produced by the transform must be the same type.
     *
     * @param callable $transform The transform to apply to each element.
     */
    public function mapInPlace($transform)
    {
        for ($node = $this->head; null !== $node; $node = $node->next) {
            $node->element = call_user_func($transform, $node->element);
        }
    }

    /////////////////////////////////////////
    // Implementation of SequenceInterface //
    /////////////////////////////////////////

    /**
     * Fetch the first element in the sequence.
     *
     * @return mixed                              The first element in the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function front()
    {
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        return $this->head->element;
    }

    /**
     * Fetch the first element in the sequence.
     *
     * @param mixed &$element Assigned the element at the front of collection.
     *
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryFront(&$element)
    {
        if ($this->isEmpty()) {
            return false;
        }

        $element = $this->head->element;

        return true;
    }

    /**
     * Fetch the last element in the sequence.
     *
     * @return mixed                              The first element in the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function back()
    {
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        return $this->tail->element;
    }

    /**
     * Fetch the last element in the sequence.
     *
     * @param mixed &$element Assigned the element at the front of collection.
     *
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryBack(&$element)
    {
        if ($this->isEmpty()) {
            return false;
        }

        $element = $this->tail->element;

        return true;
    }

    /**
     * Create a new sequence with the elements from this sequence in sorted order.
     *
     * @param callable|null $comparator A strcmp style comparator function.
     *
     * @return LinkedList
     */
    public function sort($comparator = null)
    {
        $result = clone $this;
        $result->sortInPlace($comparator);

        return $result;
    }

    /**
     * Create a new sequence with the elements from this sequence in reverse order.
     *
     * It is not guaranteed that the concrete type of the reversed collection will match this collection.
     *
     * @return LinkedList The reversed sequence.
     */
    public function reverse()
    {
        $result = new static;

        for ($node = $this->head; null !== $node; $node = $node->next) {
            $result->pushFront($node->element);
        }

        return $result;
    }

    /**
     * Create a new sequence by appending the elements in the given sequence to this sequence.
     *
     * @param mixed<mixed> $sequence       The sequence to append.
     * @param mixed<mixed> $additional,... Additional sequences to append.
     *
     * @return SequenceInterface A new sequence containing all elements from this sequence and $sequence.
     */
    public function join($sequence)
    {
        $result = new static;
        list($result->head, $result->tail, $result->size) = $this->cloneNodes($this->head);

        foreach (func_get_args() as $sequence) {
            $result->insertMany($result->size, $sequence);
        }

        return $result;
    }

    ////////////////////////////////////////////////
    // Implementation of MutableSequenceInterface //
    ////////////////////////////////////////////////

    /**
     * Sort this sequence in-place.
     *
     * @link http://www.chiark.greenend.org.uk/~sgtatham/algorithms/listsort.html
     *
     * @param callable|null $comparator A strcmp style comparator function.
     */
    public function sortInPlace($comparator = null)
    {
        if ($this->size <= 1) {
            return;
        }

        if (null === $comparator) {
            $comparator = function ($a, $b) {
                if ($a < $b) {
                    return -1;
                } elseif ($a > $b) {
                    return 1;
                } else {
                    return 0;
                }
            };
        }

        $chunkSize = 1;

        $left = null;
        $head = $this->head;
        $tail = null;

        do {
            $left = $head;
            $head = null;
            $tail = null;

            $mergeCount = 0;

            while ($left) {
                ++$mergeCount;

                $right = $left;

                for ($leftSize = 0; $right && $leftSize < $chunkSize; ++$leftSize) {
                    $right = $right->next;
                }

                $rightSize = $chunkSize;

                while ($leftSize || ($right && $rightSize)) {
                    if (0 === $leftSize) {
                        $node = $right;
                        $right = $right->next;
                        --$rightSize;
                    } elseif (!$right || 0 === $rightSize) {
                        $node = $left;
                        $left = $left->next;
                        --$leftSize;
                    } elseif (call_user_func($comparator, $left->element, $right->element) <= 0) {
                        $node = $left;
                        $left = $left->next;
                        --$leftSize;
                    } else {
                        $node = $right;
                        $right = $right->next;
                        --$rightSize;
                    }

                    if ($tail) {
                        $tail->next = $node;
                        $node->prev = $tail;
                    } else {
                        $head = $node;
                    }

                    $tail = $node;
                }

                $left = $right;
            }

            $tail->next = null;
            $chunkSize *= 2;

        } while ($mergeCount > 1);

        $this->head = $head;
        $this->tail = $tail;
    }

    /**
     * Reverse this sequence in-place.
     */
    public function reverseInPlace()
    {
        $prev = null;
        $node = $this->head;

        while ($node) {
            $next = $node->next;
            $node->next = $prev;
            $node->prev = $next;
            $prev = $node;
            $node = $next;
        }

        $head       = $this->head;
        $this->head = $this->tail;
        $this->tail = $head;
    }

    /**
     * Appending elements in the given sequence to this sequence.
     *
     * @param mixed<mixed> $sequence       The sequence to append.
     * @param mixed<mixed> $additional,... Additional sequences to append.
     */
    public function append($sequence)
    {
        foreach (func_get_args() as $sequence) {
            $this->insertMany($this->size, $sequence);
        }
    }

    /**
     * Add a new element to the front of the sequence.
     *
     * @param mixed $element The element to prepend.
     */
    public function pushFront($element)
    {
        $this->head = $this->createNode($element, $this->head);

        if (0 === $this->size++) {
            $this->tail = $this->head;
        } else {
            $this->head->next->prev = $this->head;
        }
    }

    /**
     * Remove and return the element at the front of the sequence.
     *
     * @return mixed                              The element at the front of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popFront()
    {
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        $element    = $this->head->element;
        $this->head = $this->head->next;

        if (0 === --$this->size) {
            $this->tail = null;
        } else {
            $this->head->prev = null;
        }

        return $element;
    }

    /**
     * Remove the element at the front of the sequence.
     *
     * @param mixed &$element Assigned the removed element.
     *
     * @return boolean True if the front element is removed and assigned to $element; otherwise, false.
     */
    public function tryPopFront(&$element = null)
    {
        if ($this->isEmpty()) {
            return false;
        }

        $element = $this->popFront();

        return true;
    }

    /**
     * Add a new element to the back of the sequence.
     *
     * @param mixed $element The element to append.
     */
    public function pushBack($element)
    {
        $node = $this->createNode($element, null, $this->tail);

        if (0 === $this->size++) {
            $this->head = $node;
        } else {
            $this->tail->next = $node;
        }

        $this->tail = $node;
    }

    /**
     * Remove and return the element at the back of the sequence.
     *
     * @return mixed                              The element at the back of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popBack()
    {
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        $element = $this->tail->element;

        if (0 === --$this->size) {
            $this->head = null;
            $this->tail = null;
        } else {
            $this->tail = $this->nodeAt($this->size - 1);
            $this->tail->next->prev = null;
            $this->tail->next = null;
        }

        return $element;
    }

    /**
     * Remove the element at the back of the sequence.
     *
     * @param mixed &$element Assigned the removed element.
     *
     * @return boolean True if the back element is removed and assigned to $element; otherwise, false.
     */
    public function tryPopBack(&$element = null)
    {
        if ($this->isEmpty()) {
            return false;
        }

        $element = $this->popBack();

        return true;
    }

    /**
     * Resize the sequence.
     *
     * @param integer $size    The new size of the collection.
     * @param mixed   $element The value to use for populating new elements when $size > $this->size().
     */
    public function resize($size, $element = null)
    {
        if ($this->size > $size) {
            $this->removeMany($size);
        } else {
            while ($size--) {
                $this->pushBack($element);
            }
        }
    }

    /////////////////////////////////////////////
    // Implementation of RandomAccessInterface //
    /////////////////////////////////////////////

    /**
     * Fetch the element at the given index.
     *
     * @param mixed $index The index of the element to fetch, if index is a negative number the element that far from the end of the sequence is returned.
     *
     * @return mixed                    The element at $index.
     * @throws Exception\IndexException if $index is out of range.
     */
    public function get($index)
    {
        $this->validateIndex($index);

        return $this->nodeAt($index)->element;
    }

    /**
     * Extract a range of elements.
     *
     * It is not guaranteed that the concrete type of the slice collection will match this collection.
     *
     * @param integer      $index The index from which the slice will start. If index is a negative number the slice will begin that far from the end of the sequence.
     * @param integer|null $count The maximum number of elements to include in the slice, or null to include all elements from $index to the end of the sequence.
     *
     * @return SequenceInterface        The sliced sequence.
     * @throws Exception\IndexException if $index is out of range.
     */
    public function slice($index, $count = null)
    {
        $this->validateIndex($index);

        $start = $this->nodeAt($index);

        if (null === $count) {
            $stop = null;
        } else {
            $count = max(0, min($this->size - $index, $count));
            $stop = $this->nodeFrom($start, $count);
        }

        $result = new static;
        list($result->head, $result->tail, $result->size) = $this->cloneNodes($start, $stop);

        return $result;
    }

    /**
     * Extract a range of elements.
     *
     * It is not guaranteed that the concrete type of the slice collection will match this collection.
     *
     * Extracts all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer $begin The index from which the slice will start. If begin is a negative number the slice will begin that far from the end of the sequence.
     * @param integer $end   The index at which the slice will end. If end is a negative number the slice will end that far from the end of the sequence.
     *
     * @return SequenceInterface        The sliced sequence.
     * @throws Exception\IndexException if $begin or $end is out of range.
     */
    public function range($begin, $end)
    {
        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size);

        return $this->slice($begin, $end - $begin);
    }

    /**
     * Find the index of the first instance of a particular element in the sequence.
     *
     * Searches all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param mixed        $element The element to search for.
     * @param integer      $begin   The index to start searching from.
     * @param integer|null $end     The index to to stop searching at, or null to search to the end of the sequence.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $begin or $end is out of range.
     */
    public function indexOf($element, $begin = 0, $end = null)
    {
        $predicate = function ($e) use ($element) {
            return $element === $e;
        };

        return $this->find($predicate, $begin, $end);
    }

    /**
     * Find the index of the last instance of a particular element in the sequence.
     *
     * Searches all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param mixed        $element The element to search for.
     * @param integer      $begin   The index to start searching from.
     * @param integer|null $end     The index to to stop searching at, or null to search to the end of the sequence.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $begin is out of range.
     */
    public function indexOfLast($element, $begin = 0, $end = null)
    {
        $predicate = function ($e) use ($element) {
            return $element === $e;
        };

        return $this->findLast($predicate, $begin, $end);
    }

    /**
     * Find the index of the first instance of an element matching given criteria.
     *
     * Searches all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param callable     $predicate A predicate function used to determine which element constitutes a match.
     * @param integer      $begin     The index to start searching from.
     * @param integer|null $end       The index to to stop searching at, or null to search to the end of the sequence.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $begin is out of range.
     */
    public function find($predicate, $begin = 0, $end = null)
    {
        if ($this->isEmpty()) {
            return null;
        }

        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size);

        $node = $this->nodeAt($begin);

        while (null !== $node && $begin !== $end) {
            if (call_user_func($predicate, $node->element)) {
                return $begin;
            }

            ++$begin;
            $node = $node->next;
        }

        return null;
    }

    /**
     * Find the index of the last instance of an element matching given criteria.
     *
     * Searches all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param callable     $predicate A predicate function used to determine which element constitutes a match.
     * @param integer      $begin     The index to start searching from.
     * @param integer|null $end       The index to to stop searching at, or null to search to the end of the sequence.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $begin is out of range.
     */
    public function findLast($predicate, $begin = 0, $end = null)
    {
        if ($this->isEmpty()) {
            return null;
        }

        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size);

        $node = $this->nodeAt(--$end);

        while (null !== $node && $end >= $begin) {
            if (call_user_func($predicate, $node->element)) {
                return $end;
            }

            --$end;
            $node = $node->prev;
        }

        return null;
    }

    ////////////////////////////////////////////////////
    // Implementation of MutableRandomAccessInterface //
    ////////////////////////////////////////////////////

    /**
     * Replace the element at a particular position in the sequence.
     *
     * @param integer $index   The index of the element to set, if index is a negative number the element that far from the end of the sequence is set.
     * @param mixed   $element The element to set.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function set($index, $element)
    {
        $this->validateIndex($index);

        $this->nodeAt($index)->element = $element;
    }

    /**
     * Insert an element at a particular index.
     *
     * @param integer $index   The index at which the element is inserted, if index is a negative number the element is inserted that far from the end of the sequence.
     * @param mixed   $element The element to insert.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function insert($index, $element)
    {
        $this->insertMany($index, array($element));
    }

    /**
     * Insert all elements from another collection at a particular index.
     *
     * @param integer      $index    The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     */
    public function insertMany($index, $elements)
    {
        $this->validateIndex($index, $this->size);

        list($head, $tail, $size) = $this->createNodes($elements);

        $this->insertNodes($index, $head, $tail, $size);
    }

    /**
     * Insert a sub-range of another collection at a particular index.
     *
     * Inserts all elements from the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer                          $index    The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param RandomAccessInterface+LinkedList $elements The elements to insert.
     * @param integer                          $begin    The index of the first element from $elements to insert, if begin is a negative number the removal begins that far from the end of the sequence.
     * @param integer                          $end|null The index of the last element to $elements to insert, if end is a negative number the removal ends that far from the end of the sequence.
     *
     * @throws Exception\IndexException if $index, $begin or $end is out of range.
     */
    public function insertRange($index, RandomAccessInterface $elements, $begin, $end = null)
    {
        if (!$elements instanceof self) {
            throw new InvalidArgumentException('The given collection is not an instance of ' . __CLASS__ . '.');
        }

        $this->validateIndex($index);
        $elements->validateIndex($begin);
        $elements->validateIndex($end, $elements->size);

        list($head, $tail, $size) = $elements->cloneNodes(
            $elements->nodeAt($begin),
            null,
            $end - $begin
        );

        $this->insertNodes($index, $head, $tail, $size);
    }

    /**
     * Remove the element at a given index.
     *
     * Elements after the given endex are moved forward by one.
     *
     * @param integer $index The index of the element to remove, if index is a negative number the element that far from the end of the sequence is removed.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function remove($index)
    {
        $this->removeRange($index, $index + 1);
    }

    /**
     * Remove a range of elements at a given index.
     *
     * @param integer      $index The index of the first element to remove, if index is a negative number the removal begins that far from the end of the sequence.
     * @param integer|null $count The number of elements to remove, or null to remove all elements up to the end of the sequence.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function removeMany($index, $count = null)
    {
        $this->validateIndex($index);

        // Remove, but not all the way to the end ...
        if (null !== $count && $count < $this->size - $index) {
            $count = max(0, $count);
            $node = $this->nodeAt($index - 1);
            $node->next->prev = null;
            $node->next = $this->nodeFrom($node, $count + 1);
            $node->next->prev = $node;
            $this->size -= $count;

        // Remove everything ...
        } elseif (0 === $index) {
            $this->clear();

        // Remove everything to the end ...
        } else {
            $node = $this->nodeAt($index - 1);
            $node->next->prev = null;
            $node->next = null;
            $this->tail = $node;
            $this->size = $index;
        }
    }

    /**
     * Remove a range of elements at a given index.
     *
     * Removes all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer $begin The index of the first element to remove, if $begin is a negative number the removal begins that far from the end of the sequence.
     * @param integer $end   The index of the last element to remove, if $end is a negative number the removal ends that far from the end of the sequence.
     *
     * @throws Exception\IndexException if $begin or $end is out of range.
     */
    public function removeRange($begin, $end)
    {
        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size);

        $this->removeMany($begin, $end - $begin);
    }

    /**
     * Replace a range of elements with a second set of elements.
     *
     * @param integer      $index    The index of the first element to replace, if index is a negative number the replace begins that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     * @param integer|null $count    The number of elements to replace, or null to replace all elements up to the end of the sequence.
     */
    public function replace($index, $elements, $count = null)
    {
        $this->validateIndex($index);

        $this->removeMany($index, $count);
        $this->insertMany($index, $elements);
    }

    /**
     * Replace a range of elements with a second set of elements.
     *
     * Replaces all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer      $begin    The index of the first element to replace, if begin is a negative number the replace begins that far from the end of the sequence.
     * @param integer      $end      The index of the last element to replace, if end is a negativ enumber the replace ends that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     */
    public function replaceRange($begin, $end, $elements)
    {
        $this->validateIndex($begin);

        $this->removeRange($begin, $end);
        $this->insertMany($begin, $elements);
    }

    /**
     * Swap the elements at two index positions.
     *
     * @param integer $index1 The index of the first element.
     * @param integer $index2 The index of the second element.
     *
     * @throws Exception\IndexException if $index1 or $index2 is out of range.
     */
    public function swap($index1, $index2)
    {
        $this->validateIndex($index1);
        $this->validateIndex($index2);

        $this->doSwap($index1, $index2);
    }

    /**
     * Swap the elements at two index positions.
     *
     * @param integer $index1 The index of the first element.
     * @param integer $index2 The index of the second element.
     *
     * @return boolean True if $index1 and $index2 are in range and the swap is successful.
     */
    public function trySwap($index1, $index2)
    {
        if ($index1 < 0) {
            $index1 += $this->size();
        }

        if ($index2 < 0) {
            $index2 += $this->size();
        }

        if ($index1 < 0 || $index1 >= $this->size()) {
            return false;
        }

        if ($index2 < 0 || $index2 >= $this->size()) {
            return false;
        }

        $this->doSwap($index1, $index2);

        return true;
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function count()
    {
        return $this->size();
    }

    /////////////////////////////////////////
    // Implementation of IteratorAggregate //
    /////////////////////////////////////////

    public function getIterator()
    {
        return new Detail\LinkedListIterator($this->head, $this->tail);
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
        return serialize($this->elements());
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

        return Collection::compare($this, $value);
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

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    /**
     * @param integer $index1
     * @param integer $index2
     */
    private function doSwap($index1, $index2)
    {
        $a = min($index1, $index2);
        $b = max($index1, $index2);

        $node1 = $this->nodeAt($a);
        $node2 = $this->nodeFrom($node1, $b - $a);

        $element        = $node1->element;
        $node1->element = $node2->element;
        $node2->element = $element;
    }

    /**
     * @param integer      &$index
     * @param integer|null $max
     */
    private function validateIndex(&$index, $max = null)
    {
        if (null === $max) {
            $max = $this->size - 1;
        }

        if (null === $index) {
            $index = $max;
        } elseif ($index < 0) {
            $index += $this->size;
        }

        if ($index < 0 || $index > $max) {
            throw new Exception\IndexException($index);
        }
    }

    /**
     * @param mixed         $element
     * @param stdClass|null $next
     * @param stdClass|null $prev
     */
    private function createNode($element = null, stdClass $next = null, stdClass $prev = null)
    {
        $node = new stdClass;
        $node->next = $next;
        $node->prev = $prev;
        $node->element = $element;

        return $node;
    }

    /**
     * @param integer $index
     */
    private function nodeAt($index)
    {
        $half = $this->size / 2;

        if ($index <= $half) {
            return $this->nodeFrom($this->head, $index);
        }

        return $this->nodeFromReverse($this->tail, $this->size - $index - 1);
    }

    /**
     * @param stdClass $node
     * @param integer  $count
     */
    private function nodeFrom(stdClass $node, $count)
    {
        while ($node && $count--) {
            $node = $node->next;
        }

        return $node;
    }

    private function nodeFromReverse(stdClass $node, $count)
    {
        while ($node && $count--) {
            $node = $node->prev;
        }

        return $node;
    }

    /**
     * @param stdClass      $start
     * @param stdClass|null $stop
     * @param integer|null  $limit
     */
    private function cloneNodes(stdClass $start, stdClass $stop = null, $limit = null)
    {
        $head = null;
        $tail = null;
        $size = 0;

        for ($node = $start; $stop !== $node && $size !== $limit; $node = $node->next) {
            $n = $this->createNode($node->element);
            if (null === $head) {
                $head = $n;
            } else {
                $tail->next = $n;
                $n->prev = $tail;
            }
            $tail = $n;
            ++$size;
        }

        return array($head, $tail, $size);
    }

    /**
     * @param mixed<mixed> $elements
     */
    private function createNodes($elements)
    {
        $head = null;
        $tail = null;
        $size = 0;

        foreach ($elements as $element) {
            $node = $this->createNode($element);
            if (null === $head) {
                $head = $node;
            } else {
                $tail->next = $node;
                $node->prev = $tail;
            }
            $tail = $node;
            ++$size;
        }

        return array($head, $tail, $size);
    }

    /**
     * @param integer       $index
     * @param stdClass|null $head
     * @param stdClass|null $tail
     * @param integer       $size
     */
    private function insertNodes($index, stdClass $head = null, stdClass $tail = null, $size)
    {
        if (null === $head) {
            return;
        } elseif (0 === $this->size) {
            $this->head = $head;
            $this->tail = $tail;
        } elseif (0 === $index) {
            $tail->next = $this->head;
            $this->head->prev = $tail;
            $this->head = $head;
        } elseif ($this->size === $index) {
            $this->tail->next = $head;
            $head->prev = $this->tail;
            $this->tail = $tail;
        } else {
            $node = $this->nodeAt($index - 1);
            $tail->next = $node->next;
            $node->next->prev = $tail;
            $node->next = $head;
            $head->prev = $node;
        }

        $this->size += $size;
    }

    private $head;
    private $tail;
    private $size;
}
