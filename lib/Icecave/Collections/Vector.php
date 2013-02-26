<?php
namespace Icecave\Collections;

use ArrayAccess;
use Countable;
use Icecave\Collections\TypeCheck\TypeCheck;
use Iterator;
use Serializable;
use SplFixedArray;

class Vector implements MutableRandomAccessInterface, Countable, Iterator, ArrayAccess, Serializable
{
    /**
     * @param mixed<mixed>|null $collection       An iterable type containing the elements to include in this vector, or null to create an empty vector.
     * @param callable|null     $elementValidator The callback used to check the validity of an element; or null to allow any element.
     */
    public function __construct($collection = null, $elementValidator = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->elementValidator = $elementValidator;

        // Short circuit for fast construction from array ...
        if (null === $this->elementValidator && is_array($collection)) {
            $this->elements = SplFixedArray::fromArray($collection, false);
            $this->size = count($collection);
        } else {
            $this->clear();
            if (null !== $collection) {
                $this->insertMany(0, $collection);
            }
        }
    }

    public function __clone()
    {
        $this->typeCheck->validateClone(func_get_args());

        $this->elements = clone $this->elements;
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

        return $this->size;
    }

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty()
    {
        $this->typeCheck->isEmpty(func_get_args());

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
        $this->typeCheck->clear(func_get_args());

        $this->elements = new SplFixedArray;
        $this->size = 0;
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

        $result = new static(null, $this->elementValidator);
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
        $this->typeCheck->map(func_get_args());

        $result = new static(null, $this->elementValidator);
        $result->reserve($this->size);

        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                $result->pushBack(call_user_func($transform, $element));
            }
        }

        return $result;
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

        $size = $this->size;
        $this->size = 0;

        foreach ($this->elements as $index => $element) {
            if ($index >= $size) {
                $this->elements[$index] = null;
            } elseif (call_user_func($predicate, $element)) {
                $this->elements[$this->size++] = $element;
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
    public function apply($transform)
    {
        $this->typeCheck->apply(func_get_args());

        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                $this->elements[$index] = $this->validateElement(
                    call_user_func($transform, $element)
                );
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
        $this->typeCheck->front(func_get_args());

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

        $elements = $this->elements();

        if (null === $comparator) {
            sort($elements);
        } else {
            usort($elements, $comparator);
        }

        return new static($elements, $this->elementValidator);
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

        $result = new static(null, $this->elementValidator);
        $result->size = $this->size;
        $result->reserve($result->size);

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
        $this->typeCheck->join(func_get_args());

        $result = clone $this;

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
    public function reverse()
    {
        $this->typeCheck->reverse(func_get_args());

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
        $this->typeCheck->append(func_get_args());

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
        $this->typeCheck->pushFront(func_get_args());

        $element = $this->validateElement($element);
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
        $this->typeCheck->popFront(func_get_args());

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

        $element = $this->validateElement($element);
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
        $this->typeCheck->popBack(func_get_args());

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

        if ($this->size > $size) {
            $this->elements->setSize($size);
            $this->size = $size;

            return;
        }

        $this->validateElement($element);
        $this->reserve($size);

        if (null === $element) {
            $this->size = $size;
        } else {
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
     * @throws Exception\IndexException if no such index exists.
     */
    public function get($index)
    {
        $this->typeCheck->get(func_get_args());

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
        $this->typeCheck->slice(func_get_args());

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
     * @throws Exception\IndexException if $index is out of range.
     */
    public function range($begin, $end)
    {
        $this->typeCheck->range(func_get_args());

        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size);

        $result = new static(null, $this->elementValidator);

        if ($begin < $end) {
            $result->size = $end - $begin;
            $result->reserve($result->size);

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

        for ($index = $startIndex; $index < $this->size; ++$index) {
            if (call_user_func($predicate, $this->elements[$index])) {
                return $index;
            }
        }

        return null;
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
            $startIndex = $this->size - 1;
        }

        for ($index = $startIndex; $index >= 0; --$index) {
            if (call_user_func($predicate, $this->elements[$index])) {
                return $index;
            }
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
        $this->typeCheck->set(func_get_args());

        $this->validateIndex($index);
        $this->elements[$index] = $this->validateElement($element);
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

        $this->insertMany($index, array($element));
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

        $this->validateIndex($index, $this->size);

        $count = count($elements);

        if (0 === $count) {
            return;
        }

        $this->shiftRight($index, $count);
        $originalSize = $this->size;
        $this->size += $count;

        try {
            foreach ($elements as $element) {
                $this->elements[$index++] = $this->validateElement($element);
            }
        } catch (\Exception $e) {
            // Rollback, $index will be at the correct position after iteration above.
            $this->shiftLeft($index, $count);
            $this->size = $originalSize;
            throw $e;
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
     * @throws Exception\IndexException if $index is out of range.
     */
    public function removeRange($begin, $end)
    {
        $this->typeCheck->removeRange(func_get_args());

        $this->validateIndex($begin);
        $this->validateIndex($end, $this->size);
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

        $count = $this->clamp($count, 0, $this->size - $index);

        // Avoid storing any state for rollback if there is no chance of validation failure ...
        if (null === $this->elementValidator) {
            $diff = count($elements) - $count;

            if ($diff > 0) {
                $this->shiftRight($index + $count, $diff);
            } elseif ($diff < 0) {
                $this->shiftLeft($index + $count, abs($diff));
            }

            foreach ($elements as $element) {
                $this->elements[$index++] = $element;
            }

            $this->size += $diff;

        // Validation may fail, so perform write / remove in discrete steps
        } else {
            $size = $this->size();
            $this->insertMany($index, $elements);
            $this->removeMany($index + $this->size() - $size, $count);
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
        $this->typeCheck->swap(func_get_args());

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
        $this->typeCheck->trySwap(func_get_args());

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
        $this->typeCheck->count(func_get_args());

        return $this->size();
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function current()
    {
        $this->typeCheck->current(func_get_args());

        return current($this->elements);
    }

    public function key()
    {
        $this->typeCheck->key(func_get_args());

        return key($this->elements);
    }

    public function next()
    {
        $this->typeCheck->next(func_get_args());

        next($this->elements);
    }

    public function rewind()
    {
        $this->typeCheck->rewind(func_get_args());

        reset($this->elements);
    }

    public function valid()
    {
        $this->typeCheck->valid(func_get_args());

        $index = $this->key();

        return null !== $index
            && $index < $this->size();
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

        return serialize(
            array(
                $this->elements(),
                $this->elementValidator
            )
        );
    }

    /**
     * @param string $packet The serialized data.
     */
    public function unserialize($packet)
    {
        TypeCheck::get(__CLASS__)->unserialize(func_get_args());

        list($elements, $elementValidator) = unserialize($packet);
        $this->__construct($elements, $elementValidator);
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

        return $this->elements->count();
    }

    /**
     * Reserve enough memory to hold at least $size elements.
     *
     * @param integer $size
     */
    public function reserve($size)
    {
        $this->typeCheck->reserve(func_get_args());

        if ($size > $this->capacity()) {
            $this->elements->setSize($size);
        }
    }

    /**
     * Shrink the reserved memory to match the current vector size.
     */
    public function shrink()
    {
        $this->typeCheck->shrink(func_get_args());

        $this->elements->setSize($this->size);
    }

    /**
     * @return callable|null The callback used to check the validity of an element; or null is any element is allowed.
     */
    public function elementValidator()
    {
        $this->typeCheck->elementValidator(func_get_args());

        return $this->elementValidator;
    }

    /**
     * @param integer      &$index
     * @param integer|null $max
     */
    protected function validateIndex(&$index, $max = null)
    {
        $this->typeCheck->validateIndex(func_get_args());

        if (null === $max) {
            $max = $this->size - 1;
        }

        if ($index < 0) {
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
    protected function shiftLeft($index, $count)
    {
        $this->typeCheck->shiftLeft(func_get_args());

        $target = $index - $count;
        $source = $index;

        while ($source < $this->size) {
            $this->elements[$target++] = $this->elements[$source++];
        }

        while ($target < $this->size) {
            $this->elements[$target++] = null;
        }
    }

    /**
     * @param integer $index
     * @param integer $count
     */
    protected function shiftRight($index, $count)
    {
        $this->typeCheck->shiftRight(func_get_args());

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
    protected function clamp($value, $min, $max)
    {
        $this->typeCheck->clamp(func_get_args());

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
     */
    protected function expand($count)
    {
        $this->typeCheck->expand(func_get_args());

        if ($this->capacity() >= $this->size + $count) {
            return;
        }

        if (0 === $this->size) {
            $capacity = $this->size + $count;
        } else {
            $capacity = $this->capacity();
            $target = $this->size + $count;
            while ($capacity < $target) {
                $capacity <<= 1;
            }
        }
        $this->reserve($capacity);
    }

    /**
     * @param mixed $element
     *
     * @throws Exception\InvalidElementException
     */
    protected function validateElement($element)
    {
        if (null !== $this->elementValidator && !call_user_func($this->elementValidator, $element)) {
            throw new Exception\InvalidElementException($element);
        }

        return $element;
    }

    private $typeCheck;
    private $elements;
    private $size;
    private $elementValidator;
}
