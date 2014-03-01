<?php
namespace Icecave\Collections;

use ArrayAccess;
use Countable;
use Icecave\Collections\Iterator\Traits;
use Icecave\Parity\Exception\NotComparableException;
use InvalidArgumentException;
use IteratorAggregate;
use Serializable;
use SplFixedArray;

/**
 * A mutable sequence with efficient access by position and iteration.
 */
class Vector implements MutableRandomAccessInterface, Countable, IteratorAggregate, ArrayAccess, Serializable
{
    /**
     * @param mixed<mixed>|null $elements An iterable type containing the elements to include in this vector, or null to create an empty vector.
     */
    public function __construct($elements = null)
    {
        if (is_array($elements)) {
            $this->elements = SplFixedArray::fromArray($elements, false);
            $this->size = count($elements);
        } else {
            $this->clear();
            if (null !== $elements) {
                $this->insertMany(0, $elements);
            }
        }
    }

    public function __clone()
    {
        $this->elements = clone $this->elements;
    }

    /**
     * Create a Vector.
     *
     * @param mixed $element,... Elements to include in the collection.
     *
     * @return Vector
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
        return 0 === $this->size;
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
            return '<Vector 0>';
        }

        $elements = $this
            ->slice(0, 3)
            ->map('Icecave\Repr\Repr::repr');

        if ($this->size > 3) {
            $format = '<Vector %d [%s, ...]>';
        } else {
            $format = '<Vector %d [%s]>';
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
        $this->elements = new SplFixedArray;
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
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                $elements[] = $element;
            }
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
        return null !== $this->indexOf($element);
    }

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to include, or null to include all non-null elements.
     *
     * @return Vector The filtered collection.
     */
    public function filter($predicate = null)
    {
        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $result = new static;
        $result->reserve($this->size);

        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } elseif (call_user_func($predicate, $element)) {
                $result->pushBack($element);
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
        $result->resize($this->size);

        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                $result->elements[$index] = call_user_func($transform, $element);
            }
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

        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } elseif (call_user_func($predicate, $element)) {
                $left->pushBack($element);
            } else {
                $right->pushBack($element);
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
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                call_user_func($callback, $element);
            }
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
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } elseif (!call_user_func($predicate, $element)) {
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
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } elseif (call_user_func($predicate, $element)) {
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

        $size = $this->size;
        $this->size = 0;

        foreach ($this->elements as $index => $element) {
            if ($index >= $size) {
                break;
            } elseif (call_user_func($predicate, $element)) {
                $this->elements[$this->size++] = $element;
            }

            if ($index >= $this->size) {
                $this->elements[$index] = null;
            }
        }
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
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                $this->elements[$index] = call_user_func($transform, $element);
            }
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

        return $this->elements[0];
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
        $element = $this->front();

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

        return $this->elements[$this->size - 1];
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
        $element = $this->back();

        return true;
    }

    /**
     * Create a new sequence with the elements from this sequence in sorted order.
     *
     * @param callable|null $comparator A strcmp style comparator function.
     *
     * @return Vector
     */
    public function sort($comparator = null)
    {
        $elements = $this->elements();

        if (null === $comparator) {
            sort($elements);
        } else {
            usort($elements, $comparator);
        }

        return new static($elements);
    }

    /**
     * Create a new sequence with the elements from this sequence in reverse order.
     *
     * It is not guaranteed that the concrete type of the reversed collection will match this collection.
     *
     * @return Vector The reversed sequence.
     */
    public function reverse()
    {
        $result = new static;
        $result->resize($this->size);

        $target = $this->size - 1;
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                $result->elements[$target--] = $element;
            }
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
        $result = new static($this);
        foreach (func_get_args() as $sequence) {
            $result->insertMany($result->size(), $sequence);
        }

        return $result;
    }

    ////////////////////////////////////////////////
    // Implementation of MutableSequenceInterface //
    ////////////////////////////////////////////////

    /**
     * Sort this sequence in-place.
     *
     * @param callable|null $comparator A strcmp style comparator function.
     */
    public function sortInPlace($comparator = null)
    {
        $elements = $this->elements();

        if (null === $comparator) {
            sort($elements);
        } else {
            usort($elements, $comparator);
        }

        $this->elements = SplFixedArray::fromArray($elements);
    }

    /**
     * Reverse this sequence in-place.
     */
    public function reverseInPlace()
    {
        $first = 0;
        $last  = $this->size;

        while (($first !== $last) && ($first !== --$last)) {
            $this->swap($first++, $last);
        }
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
        $this->shiftRight(0, 1);
        $this->elements[0] = $element;
        ++$this->size;
    }

    /**
     * Remove and return the element at the front of the sequence.
     *
     * @return mixed                              The element at the front of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popFront()
    {
        $element = $this->front();
        $this->shiftLeft(1, 1);
        --$this->size;

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
        $this->expand(1);
        $this->elements[$this->size++] = $element;
    }

    /**
     * Remove and return the element at the back of the sequence.
     *
     * @return mixed                              The element at the back of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popBack()
    {
        $element = $this->back();
        $this->elements[--$this->size] = null;

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
            $this->elements->setSize($size);
            $this->size = $size;
        } elseif (null === $element) {
            $this->reserve($size);
            $this->size = $size;
        } else {
            $this->reserve($size);
            while ($this->size < $size) {
                $this->elements[$this->size++] = $element;
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

        return $this->elements[$index];
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

        if (null === $count) {
            $end = $this->size;
        } else {
            $end = $this->clamp(
                $index + $count,
                $index,
                $this->size
            );
        }

        return $this->range($index, $end);
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

        $result = new static;

        if ($begin < $end) {
            $result->resize($end - $begin);

            $index = 0;
            while ($index < $result->size()) {
                $result->elements[$index++] = $this->elements[$begin++];
            }
        }

        return $result;
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

        for (; $begin !== $end; ++$begin) {
            if (call_user_func($predicate, $this->elements[$begin])) {
                return $begin;
            }
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

        while ($begin !== $end) {
            if (call_user_func($predicate, $this->elements[--$end])) {
                return $end;
            }
        }
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
        $this->elements[$index] = $element;
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

        // The number of elements is not known.
        // Using the normal expansion rules we create a gap in which to insert the elements.
        // Once all elements have been inserted the gap is closed.
        if (!Collection::iteratorTraits($elements)->isCountable) {
            $shiftIndex = $index;

            foreach ($elements as $element) {
                if ($index === $shiftIndex) {
                    $actualExpansion = $this->expand(1);
                    $this->shiftRight($index, $actualExpansion);
                    $shiftIndex += $actualExpansion;
                }

                $this->elements[$index++] = $element;
                ++$this->size;
            }

            $this->shiftLeft($shiftIndex, $shiftIndex - $index);

        // The number of elements is known, expand the vector once and insert the elements.
        } elseif ($count = count($elements)) {
            $this->shiftRight($index, $count);
            $this->size += $count;

            foreach ($elements as $element) {
                $this->elements[$index++] = $element;
            }
        }
    }

    /**
     * Insert a sub-range of another collection at a particular index.
     *
     * Inserts all elements from the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer                      $index    The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param RandomAccessInterface+Vector $elements The elements to insert.
     * @param integer                      $begin    The index of the first element from $elements to insert, if begin is a negative number the removal begins that far from the end of the sequence.
     * @param integer                      $end|null The index of the last element to $elements to insert, if end is a negative number the removal ends that far from the end of the sequence.
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

        $size = $end - $begin;
        $this->shiftRight($index, $size);
        $this->size += $size;

        while ($begin !== $end) {
            $this->elements[$index++] = $elements->elements[$begin++];
        }
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

        $count = $this->clamp($count, 0, $this->size - $index);
        $this->shiftLeft($index + $count, $count);
        $this->size -= $count;
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

        $count = $this->clamp($count, 0, $this->size - $index);

        // Element count is available ...
        if (Collection::iteratorTraits($elements)->isCountable) {
            $diff = count($elements) - $count;

            if ($diff > 0) {
                $this->shiftRight($index + $count, $diff);
            } elseif ($diff < 0) {
                $this->shiftLeft($index + $count, abs($diff));
            }

            $this->size += $diff;

            foreach ($elements as $element) {
                $this->elements[$index++] = $element;
            }

        // No count is available ...
        } else {
            $originalSize = $this->size;
            $this->insertMany($index, $elements);
            $elementCount = $this->size - $originalSize;
            $this->removeMany($index + $elementCount, $count);
        }
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
        $this->validateIndex($end, $this->size);
        $this->replace($begin, $elements, $end - $begin);
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

        $temp = $this->elements[$index1];
        $this->elements[$index1] = $this->elements[$index2];
        $this->elements[$index2] = $temp;
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
            $index1 += $this->size;
        }

        if ($index2 < 0) {
            $index2 += $this->size;
        }

        if ($index1 < 0 || $index1 >= $this->size) {
            return false;
        }

        if ($index2 < 0 || $index2 >= $this->size) {
            return false;
        }

        $temp = $this->elements[$index1];
        $this->elements[$index1] = $this->elements[$index2];
        $this->elements[$index2] = $temp;

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
        return new Iterator\RandomAccessIterator($this);
    }

    ///////////////////////////////////
    // Implementation of ArrayAccess //
    ///////////////////////////////////

    /**
     * @param mixed $offset
     *
     * @return boolean True if offset is a valid, in-range index for this vector; otherwise, false.
     */
    public function offsetExists($offset)
    {
        return is_integer($offset)
            && $offset >= 0
            && $offset < $this->size();
    }

    /**
     * @param integer $offset
     *
     * @return mixed The element at the index specified by $offset.
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param integer|null $offset
     * @param mixed        $value
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->pushBack($value);
        } else {
            $this->set($offset, $value);
        }
    }

    /**
     * @param integer $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $this->remove($offset);
        }
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
     * Fetch the current reserved capacity of the vector.
     *
     * @return integer The current reserved capacity of the vector.
     */
    public function capacity()
    {
        return $this->elements->count();
    }

    /**
     * Reserve enough memory to hold at least $size elements.
     *
     * @param integer $size
     */
    public function reserve($size)
    {
        if ($size > $this->capacity()) {
            $this->elements->setSize($size);
        }
    }

    /**
     * Shrink the reserved memory to match the current vector size.
     */
    public function shrink()
    {
        $this->elements->setSize($this->size);
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
     * @param integer $index
     * @param integer $count
     */
    private function shiftLeft($index, $count)
    {
        $capacity = $this->capacity();
        $target = $index - $count;
        $source = $index;

        while ($source < $capacity) {
            $this->elements[$target++] = $this->elements[$source++];
        }

        while ($target < $capacity) {
            $this->elements[$target++] = null;
        }
    }

    /**
     * @param integer $index
     * @param integer $count
     */
    private function shiftRight($index, $count)
    {
        $this->expand($count);

        $source = $this->size - 1;
        $target = $source + $count;

        while ($source >= $index) {
            $this->elements[$target--] = $this->elements[$source--];
        }
    }

    /**
     * @param integer|null $value
     * @param integer      $min
     * @param integer      $max
     */
    private function clamp($value, $min, $max)
    {
        if (null === $value) {
            return $max;
        } elseif ($value > $max) {
            return $max;
        } elseif ($value < $min) {
            return $min;
        } else {
            return $value;
        }
    }

    /**
     * @param integer $count
     *
     * @return integer The unused capacity of the vector.
     */
    private function expand($count)
    {
        $currentCapacity = $this->capacity();
        $targetCapacity  = $this->size + $count;

        if (0 === $currentCapacity) {
            $newCapacity = $targetCapacity;
        } else {
            $newCapacity = $currentCapacity;
            while ($newCapacity < $targetCapacity) {
                $newCapacity <<= 1;
            }
        }

        $this->reserve($newCapacity);

        return $this->capacity() - $this->size;
    }

    private $elements;
    private $size;
}
