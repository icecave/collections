<?php
namespace Icecave\Collections;

use ArrayAccess;
use Countable;
use Icecave\Collections\Impl\ImplFactory;
use Icecave\Collections\Iterator\Traits;
use Icecave\Collections\TypeCheck\TypeCheck;
use Iterator;
use Serializable;

class Vector implements MutableRandomAccessInterface, Countable, Iterator, ArrayAccess, Serializable
{
    /**
     * @param mixed<mixed>|null $collection An iterable type containing the elements to include in this vector, or null to create an empty vector.
     */
    public function __construct($collection = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->iteratorPosition = 0;
        $this->impl = ImplFactory::instance()->create('Vector');
        if (null !== $collection) {
            $this->insertMany(0, $collection);
        }
    }

    public function __clone()
    {
        $this->typeCheck->validateClone(func_get_args());

        $this->impl = clone $this->impl;
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

        return $this->impl->size();
    }

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty()
    {
        $this->typeCheck->isEmpty(func_get_args());

        return 0 === $this->size();
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

        if ($this->size() > 3) {
            $format = '<Vector %d [%s, ...]>';
        } else {
            $format = '<Vector %d [%s]>';
        }

        return sprintf(
            $format,
            $this->size(),
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
        $this->typeCheck->clear(func_get_args());

        $this->impl->clear();
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
        $this->typeCheck->iteratorTraits(func_get_args());

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
        $this->typeCheck->elements(func_get_args());

        return $this->impl->elements();
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
        $this->typeCheck->contains(func_get_args());

        return null !== $this->indexOf($element);
    }

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to include, or null to include all non-null elements.
     *
     * @return Vector The filtered collection.
     */
    public function filtered($predicate = null)
    {
        $this->typeCheck->filtered(func_get_args());

        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $result = new static;
        $this->impl->filter($predicate, $this->impl, $result->impl);

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
        $this->typeCheck->map(func_get_args());

        $result = new static;
        $this->impl->map($transform, $this->impl, $result->impl);

        return $result;
    }

    /**
     * Partitions this collection into two collections according to a predicate.
     *
     * It is not guaranteed that the concrete type of the partitioned collections will match this collection.
     *
     * @param callable $predicate A predicate function used to determine which partitioned collection to place the elements in.
     *
     * @return tuple<IterableInterface, IterableInterface> A 2-tuple containing the partitioned collections. The first collection contains the element for which the predicate returned true.
     */
    public function partition($predicate)
    {
        $this->typeCheck->partition(func_get_args());

        $left = new static;
        $right = new static;
        $this->impl->partition($predicate, $left->impl, $right->impl);

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
        $this->typeCheck->each(func_get_args());

        $this->all(
            function ($element) use ($callback) {
                call_user_func($callback, $element);

                return true;
            }
        );
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
        $this->typeCheck->all(func_get_args());

        return $this->impl->all($predicate);
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
        $this->typeCheck->any(func_get_args());

        return !$this->all(
            function ($element) use ($predicate) {
                return !call_user_func($predicate, $element);
            }
        );
    }

    ////////////////////////////////////////////////
    // Implementation of MutableIterableInterface //
    ////////////////////////////////////////////////

    /**
     * Filter this collection in-place.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to retain, or null to retain all non-null elements.
     */
    public function filter($predicate = null)
    {
        $this->typeCheck->filter(func_get_args());

        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $this->impl->filter($predicate, $this->impl, $this->impl);
    }

    /**
     * Replace each element in the collection with the result of a transformation on that element.
     *
     * The new elements produced by the transform must be the same type.
     *
     * @param callable $transform The transform to apply to each element.
     */
    public function apply($transform)
    {
        $this->typeCheck->apply(func_get_args());

        $this->impl->map($transform, $this->impl, $this->impl);
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
        $this->typeCheck->front(func_get_args());

        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        return $this->impl->get(0);
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
        $this->typeCheck->tryFront(func_get_args());

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
        $this->typeCheck->back(func_get_args());

        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }

        return $this->impl->get($this->size() - 1);
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
        $this->typeCheck->tryBack(func_get_args());

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
    public function sorted($comparator = null)
    {
        $this->typeCheck->sorted(func_get_args());

        if (null === $comparator) {
            $comparator = function ($left, $right) {
                if ($left < $right) {
                    return -1;
                } elseif ($right < $left) {
                    return +1;
                } else {
                    return 0;
                }
            };
        }

        $result = new static;
        $this->impl->sort($comparator, $this->impl, $result->impl);

        return $result;
    }

    /**
     * Create a new sequence with the elements from this sequence in reverse order.
     *
     * It is not guaranteed that the concrete type of the reversed collection will match this collection.
     *
     * @return Vector The reversed sequence.
     */
    public function reversed()
    {
        $this->typeCheck->reversed(func_get_args());

        $result = new static;
        $this->impl->reverse($result->impl);

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
        $this->typeCheck->join(func_get_args());

        $result = new static;
        $result->insertMany(0, $this);
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
    public function sort($comparator = null)
    {
        $this->typeCheck->sort(func_get_args());

        if (null === $comparator) {
            $comparator = function ($left, $right) {
                if ($left < $right) {
                    return -1;
                } elseif ($right < $left) {
                    return +1;
                } else {
                    return 0;
                }
            };
        }

        $this->impl->sort($comparator, $this->impl, $this->impl);
    }

    /**
     * Reverse this sequence in-place.
     */
    public function reverse()
    {
        $this->typeCheck->reverse(func_get_args());

        $this->impl->reverseInPlace();
    }

    /**
     * Appending elements in the given sequence to this sequence.
     *
     * @param mixed<mixed> $sequence       The sequence to append.
     * @param mixed<mixed> $additional,... Additional sequences to append.
     */
    public function append($sequence)
    {
        $this->typeCheck->append(func_get_args());

        foreach (func_get_args() as $sequence) {
            $this->insertMany($this->size(), $sequence);
        }
    }

    /**
     * Add a new element to the front of the sequence.
     *
     * @param mixed $element The element to prepend.
     */
    public function pushFront($element)
    {
        $this->typeCheck->pushFront(func_get_args());

        $this->insert(0, $element);
    }

    /**
     * Remove and return the element at the front of the sequence.
     *
     * @return mixed                              The element at the front of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popFront()
    {
        $this->typeCheck->popFront(func_get_args());

        $element = $this->front();
        $this->remove(0);

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
        $this->typeCheck->tryPopFront(func_get_args());

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
        $this->typeCheck->pushBack(func_get_args());

        $this->insert($this->size(), $element);
    }

    /**
     * Remove and return the element at the back of the sequence.
     *
     * @return mixed                              The element at the back of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popBack()
    {
        $this->typeCheck->popBack(func_get_args());

        $element = $this->back();
        $this->remove($this->size() - 1);

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
        $this->typeCheck->tryPopBack(func_get_args());

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
        $this->typeCheck->resize(func_get_args());

        $this->impl->resize($size, $element);
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
     * @throws Exception\IndexException if no such index exists.
     */
    public function get($index)
    {
        $this->typeCheck->get(func_get_args());

        $this->validateIndex($index);

        return $this->impl->get($index);
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
        $this->typeCheck->slice(func_get_args());

        $this->validateIndex($index);

        if (null === $count) {
            $end = $this->size();
        } else {
            $end = $this->clamp(
                $index + $count,
                $index,
                $this->size()
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
     * @throws Exception\IndexException if $index is out of range.
     */
    public function range($begin, $end)
    {
        $this->typeCheck->range(func_get_args());

        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size());

        $result = new static;
        $this->impl->range($begin, $end, $result->impl);

        return $result;
    }

    /**
     * Find the index of the first instance of a particular element in the sequence.
     *
     * @param mixed   $element    The element to search for.
     * @param integer $startIndex The index to start searching from.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $startIndex is out of range.
     */
    public function indexOf($element, $startIndex = 0)
    {
        $this->typeCheck->indexOf(func_get_args());

        $predicate = function ($e) use ($element) {
            return $element === $e;
        };

        return $this->find($predicate, $startIndex);
    }

    /**
     * Find the index of the last instance of a particular element in the sequence.
     *
     * @param mixed        $element    The element to search for.
     * @param integer|null $startIndex The index to start searching from, or null to use the last index.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $startIndex is out of range.
     */
    public function indexOfLast($element, $startIndex = null)
    {
        $this->typeCheck->indexOfLast(func_get_args());

        $predicate = function ($e) use ($element) {
            return $element === $e;
        };

        return $this->findLast($predicate, $startIndex);
    }

    /**
     * Find the index of the first instance of an element matching given criteria.
     *
     * @param callable $predicate  A predicate function used to determine which element constitutes a match.
     * @param integer  $startIndex The index to start searching from.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $startIndex is out of range.
     */
    public function find($predicate, $startIndex = 0)
    {
        $this->typeCheck->find(func_get_args());

        if ($this->isEmpty()) {
            return null;
        }

        $this->validateIndex($startIndex);

        return $this->impl->find($predicate, $startIndex);
    }

    /**
     * Find the index of the last instance of an element matching given criteria.
     *
     * @param callable     $predicate  A predicate function used to determine which element constitutes a match.
     * @param integer|null $startIndex The index to start searching from, or null to use the last index.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $startIndex is out of range.
     */
    public function findLast($predicate, $startIndex = null)
    {
        $this->typeCheck->findLast(func_get_args());

        if ($this->isEmpty()) {
            return null;
        } elseif (null === $startIndex) {
            $startIndex = $this->size() - 1;
        }

        return $this->impl->findLast($predicate, $startIndex);
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
        $this->typeCheck->set(func_get_args());

        $this->validateIndex($index);
        $this->impl->set($index, $element);
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
        $this->typeCheck->insert(func_get_args());

        $this->validateIndex($index, $this->size());

        $this->impl->insert($index, $element);
    }

    /**
     * Insert a range of elements at a particular index.
     *
     * @param integer      $index    The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     */
    public function insertMany($index, $elements)
    {
        $this->typeCheck->insertMany(func_get_args());

        $this->validateIndex($index, $this->size());

        if (Collection::iteratorTraits($elements)->isCountable) {
            $this->impl->insertMany($index, $elements);
        } else {
            foreach ($elements as $element) {
                $this->impl->insert($index++, $element);
            }
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
        $this->typeCheck->remove(func_get_args());

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
        $this->typeCheck->removeMany(func_get_args());

        $this->validateIndex($index);
        $count = $this->clamp($count, 0, $this->size() - $index);
        $this->impl->remove($index, $count);
    }

    /**
     * Remove a range of elements at a given index.
     *
     * Removes all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer $begin The index of the first element to remove, if $begin is a negative number the removal begins that far from the end of the sequence.
     * @param integer $end   The index of the last element to remove, if $end is a negative number the removal ends that far from the end of the sequence.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function removeRange($begin, $end)
    {
        $this->typeCheck->removeRange(func_get_args());

        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size());
        $this->removeMany($begin, $end - $begin);
    }

    /**
     * Replace a range of elements with a second set of elements.
     *
     * Replaces all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer      $index    The index of the first element to replace, if index is a negative number the replace begins that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     * @param integer|null $count    The number of elements to replace, or null to replace all elements up to the end of the sequence.
     */
    public function replace($index, $elements, $count = null)
    {
        $this->typeCheck->replace(func_get_args());

        $this->validateIndex($index);
        $count = $this->clamp($count, 0, $this->size() - $index);

        if (Collection::iteratorTraits($elements)->isCountable) {
            $this->impl->replace($index, $elements, $count);
        } else {
            $before = $this->size();
            $this->insertMany($index, $elements);
            $this->removeMany($index + $this->size() - $before, $count);
        }
    }

    /**
     * Replace a range of elements with a second set of elements.
     *
     * @param integer      $begin    The index of the first element to replace, if begin is a negative number the replace begins that far from the end of the sequence.
     * @param integer      $end      The index of the last element to replace, if end is a negativ enumber the replace ends that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     */
    public function replaceRange($begin, $end, $elements)
    {
        $this->typeCheck->replaceRange(func_get_args());

        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size());
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
        $this->typeCheck->swap(func_get_args());

        $this->validateIndex($index1);
        $this->validateIndex($index2);

        $temp = $this->get($index1);
        $this->set($index1, $this->get($index2));
        $this->set($index2, $temp);
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
        $this->typeCheck->trySwap(func_get_args());

        $size = $this->size();

        if ($index1 < 0) {
            $index1 += $size;
        }

        if ($index2 < 0) {
            $index2 += $size;
        }

        if ($index1 < 0 || $index1 >= $size) {
            return false;
        }

        if ($index2 < 0 || $index2 >= $size) {
            return false;
        }

        $temp = $this->get($index1);
        $this->set($index1, $this->get($index2));
        $this->set($index2, $temp);

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

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function current()
    {
        $this->typeCheck->current(func_get_args());

        return $this->get($this->iteratorPosition);
    }

    public function key()
    {
        $this->typeCheck->key(func_get_args());

        return $this->iteratorPosition;
    }

    public function next()
    {
        $this->typeCheck->next(func_get_args());

        ++$this->iteratorPosition;
    }

    public function rewind()
    {
        $this->typeCheck->rewind(func_get_args());

        $this->iteratorPosition = 0;
    }

    public function valid()
    {
        $this->typeCheck->valid(func_get_args());

        return $this->iteratorPosition >= 0
            && $this->iteratorPosition < $this->size();
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
        $this->typeCheck->offsetExists(func_get_args());

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
        $this->typeCheck->offsetGet(func_get_args());

        return $this->get($offset);
    }

    /**
     * @param integer|null $offset
     * @param mixed        $value
     */
    public function offsetSet($offset, $value)
    {
        $this->typeCheck->offsetSet(func_get_args());

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
        $this->typeCheck->offsetUnset(func_get_args());

        if ($this->offsetExists($offset)) {
            $this->remove($offset);
        }
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

        return serialize($this->elements());
    }

    /**
     * @param string $packet The serialized data.
     */
    public function unserialize($packet)
    {
        TypeCheck::get(__CLASS__)->unserialize(func_get_args());

        $elements = unserialize($packet);
        $this->__construct($elements);
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    /**
     * @return integer The current reserved capacity of the vector.
     */
    public function capacity()
    {
        $this->typeCheck->capacity(func_get_args());

        return $this->impl->capacity();
    }

    /**
     * Reserve enough memory to hold at least $size elements.
     *
     * @param integer $size
     */
    public function reserve($size)
    {
        $this->typeCheck->reserve(func_get_args());

        $this->impl->reserve($size);
    }

    /**
     * Shrink the reserved memory to match the current vector size.
     */
    public function shrink()
    {
        $this->typeCheck->shrink(func_get_args());

        $this->impl->shrink();
    }

    /**
     * @param integer      &$index
     * @param integer|null $max
     */
    private function validateIndex(&$index, $max = null)
    {
        if (null === $max) {
            $max = $this->size() - 1;
        }

        if ($index < 0) {
            $index += $this->size();
        }

        if ($index < 0 || $index > $max) {
            throw new Exception\IndexException($index);
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

    private $typeCheck;
    private $iteratorPosition;
    private $impl;
}
