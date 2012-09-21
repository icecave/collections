<?php
namespace Icecave\Collections;

use SplDoublyLinkedList;

/**
 * A doubly-linked list.
 */
class LinkedList implements IMutableRandomAccess
{
    /**
     * @param traversable|null $collection An iterable type containing the elements to include in this list, or null to create an empty list.
     */
    public function __construct($collection = null)
    {
        $this->clear();

        if (null !== $collection) {
            foreach ($collection as $element) {
                $this->pushBack($element);
            }
        }
    }

    ///////////////////////////////////
    // Implementation of ICollection //
    ///////////////////////////////////

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
            return '<LinkedList 0>';
        }

        $elements = $this
            ->slice(0, 3)
            ->map('Icecave\Collections\Support\Stringify::stringify');

        if ($this->size() > 3) {
            $elements->pushBack('...');
        }

        return sprintf(
            '<LinkedList %d [%s]>',
            $this->size(),
            implode(', ', $elements->elements())
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
        $this->elements = new SplDoublyLinkedList;
    }

    /////////////////////////////////
    // Implementation of IIterable //
    /////////////////////////////////

    /**
     * Fetch the number of elements in the collection.
     *
     * @see ICollection::empty()
     *
     * @return integer The number of elements in the collection.
     */
    public function size()
    {
        return $this->elements->count();
    }

    /**
     * Fetch a native array containing the elements in the collection.
     *
     * @return array An array containing the elements in the collection.
     */
    public function elements()
    {
        return iterator_to_array($this->elements);
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
     * @return LinkedList The filtered collection.
     */
    public function filtered($predicate = null)
    {
        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $list = new static;
        foreach ($this->elements as $element) {
            if (call_user_func($predicate, $element)) {
                $list->pushBack($element);
            }
        }

        return $list;
    }

    /**
     * Produce a new collection by applying a transformation to each element.
     *
     * The new elements produced by the transform need not be of the same type.
     * It is not guaranteed that the concrete type of the resulting collection will match this collection.
     *
     * @param callable $transform The transform to apply to each element.
     *
     * @return IIterable A new collection produced by applying $transform to each element in this collection.
     */
    public function map($transform)
    {
        $list = new static;

        foreach ($this->elements as $element) {
            $list->pushBack(call_user_func($transform, $element));
        }

        return $list;
    }

    ////////////////////////////////////////
    // Implementation of IMutableIterable //
    ////////////////////////////////////////

    /**
     * Filter this collection in-place.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to retain, or null to retain all non-null elements.
     */
    public function filter($predicate = null)
    {
        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $elements = new SplDoublyLinkedList;

        foreach ($this->elements as $element) {
            if (call_user_func($predicate, $element)) {
                $elements->push($element);
            }
        }

        $this->elements = $elements;
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
        $elements = new SplDoublyLinkedList;

        foreach ($this->elements as $element) {
            $elements->push(call_user_func($transform, $element));
        }

        $this->elements = $elements;
    }

    /////////////////////////////////
    // Implementation of ISequence //
    /////////////////////////////////

    /**
     * Fetch the first element in the sequence.
     *
     * @return mixed The first element in the sequence.
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
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryFront(&$element)
    {
        if ($this->isEmpty()) {
            return false;
        }
        $element = $this->elements[0];
        return true;
    }

    /**
     * Fetch the last element in the sequence.
     *
     * @return mixed The first element in the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function back()
    {
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }
        return $this->elements[$this->size() - 1];
    }

    /**
     * Fetch the last element in the sequence.
     *
     * @param mixed &$element Assigned the element at the front of collection.
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryBack(&$element)
    {
        if ($this->isEmpty()) {
            return false;
        }
        $element = $this->elements[$this->size() - 1];
        return true;
    }

    /**
     * Create a new sequence with the elements from this sequence in sorted order.
     *
     * @param callable|null $comparator A strcmp style comparator function.
     *
     * @return LinkedList
     */
    public function sorted($comparator = null)
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
     * @return LinkedList The reversed sequence.
     */
    public function reversed()
    {
        $list = new static;

        foreach ($this->elements as $element) {
            $list->pushFront($element);
        }

        return $list;
    }

    /**
     * Create a new sequence by appending the elements in the given sequence to this sequence.
     *
     * @param traversable,... The sequence(s) to append.
     *
     * @return ISequence A new sequence containing all elements from this sequence and $sequence.
     */
    public function join($sequence)
    {
        $list = new static($this->elements);

        foreach (func_get_args() as $sequence) {
            foreach ($sequence as $element) {
                $list->pushBack($element);
            }
        }

        return $list;
    }

    ////////////////////////////////////////
    // Implementation of IMutableSequence //
    ////////////////////////////////////////

    /**
     * Sort this sequence in-place.
     *
     * @param callable|null $comparator A strcmp style comparator function.
     */
    public function sort($comparator = null)
    {
        $elements = $this->elements();

        if (null === $comparator) {
            sort($elements);
        } else {
            usort($elements, $comparator);
        }

        $list = new SplDoublyLinkedList;
        
        foreach ($elements as $element) {
            $list->push($element);
        }

        $this->elements = $list;
    }

    /**
     * Reverse this sequence in-place.
     */
    public function reverse()
    {
        $elements = new SplDoublyLinkedList;

        foreach ($this->elements as $element) {
            $elements->unshift($element);
        }

        $this->elements = $elements;
    }

    /**
     * Appending elements in the given sequence to this sequence.
     *
     * @param traversable,... The sequence(s) to append.
     */
    public function append($sequence)
    {
        foreach (func_get_args() as $sequence) {
            foreach ($sequence as $element) {
                $this->pushBack($element);
            }
        }
    }

    /**
     * Add a new element to the front of the sequence.
     *
     * @param mixed $element The element to prepend.
     */
    public function pushFront($element)
    {
        $this->elements->unshift($element);
    }

    /**
     * Remove and return the element at the front of the sequence.
     *
     * @return mixed The element at the front of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popFront()
    {
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }
        return $this->elements->shift();
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
        $element = $this->elements->shift();
        return true;
    }

    /**
     * Add a new element to the back of the sequence.
     *
     * @param mixed $element The element to append.
     */
    public function pushBack($element)
    {
        $this->elements->push($element);
    }

    /**
     * Remove and return the element at the back of the sequence.
     *
     * @return mixed The element at the back of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popBack()
    {
        if ($this->isEmpty()) {
            throw new Exception\EmptyCollectionException;
        }
        return $this->elements->pop();
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
        $element = $this->elements->pop();
        return true;
    }

    /**
     * Resize the sequence.
     *
     * @param integer $size The new size of the collection.
     * @param mixed $element The value to use for populating new elements when $size > $this->size().
     */
    public function resize($size, $element = null)
    {
        while ($this->size() > $size) {
            $this->popBack();
        }

        while ($this->size() < $size) {
            $this->pushBack($element);
        }
    }

    /////////////////////////////////////
    // Implementation of IRandomAccess //
    /////////////////////////////////////

    /**
     * Fetch the element at the given index.
     *
     * @param mixed $index The index of the element to fetch, if index is a negative number the element that far from the end of the sequence is returned.
     *
     * @return mixed The element at $index.
     * @throws Exception\IndexException if no such index exists.
     */
    public function get($index)
    {
        if ($index < 0) {
            $index += $this->size();
        }

        if ($index < 0 || $index >= $this->size()) {
            throw new Exception\IndexException($index);
        }

        return $this->elements[$index];
    }

    /**
     * Extract a range of elements.
     *
     * It is not guaranteed that the concrete type of the slice collection will match this collection.
     *
     * @param integer $index The index from which the slice will start. If index is a negative number the slice will begin that far from the end of the sequence.
     * @param integer|null $count The maximum number of elements to include in the slice, or null to include all elements from $index to the end of the sequence.
     *
     * @return ISequence The sliced sequence.
     * @throws Exception\IndexException if $index is out of range.
     */
    public function slice($index, $count = null)
    {
        if (null === $count) {
            $end = $this->size();
        } else {
            $end = max(
                $index,
                min(
                    $index + $count,
                    $this->size() + 1
                )
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
     * @param integer $end The index at which the slice will end. If end is a negative number the slice will end that far from the end of the sequence.
     *
     * @return ISequence The sliced sequence.
     * @throws Exception\IndexException if $index is out of range.
     */
    public function range($begin, $end)
    {
        if ($begin < 0) {
            $begin += $this->size();
        }

        if ($end < 0) {
            $end += $this->size();
        }

        if ($begin < 0 || $begin >= $this->size()) {
            throw new Exception\IndexException($begin);
        }

        if ($end < 0 || $end > $this->size()) {
            throw new Exception\IndexException($end);
        }

        $list = new static;

        while ($begin < $end) {
            $list->pushBack($this->elements[$begin++]);
        }

        return $list;
    }

    /**
     * Find the index of the first instance of a particular element in the sequence.
     *
     * @param mixed $element The element to search for.
     *
     * @return integer|null The index of the element, or null if is not present in the sequence.
     */
    public function indexOf($element)
    {
        foreach ($this->elements as $index => $e) {
            if ($element === $e) {
                return $index;
            }
        }
        return null;
    }

    ////////////////////////////////////////////
    // Implementation of IMutableRandomAccess //
    ////////////////////////////////////////////

    /**
     * Replace the element at a particular position in the sequence.
     *
     * @param integer $index The index of the element to set, if index is a negative number the element that far from the end of the sequence is set.
     * @param mixed $element The element to set.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function set($index, $element)
    {
        if ($index < 0) {
            $index += $this->size();
        }

        if ($index < 0 || $index >= $this->size()) {
            throw new Exception\IndexException($index);
        }

        $this->elements[$index] = $element;
    }

    /**
     * Insert an element at a particular index.
     *
     * @param integer $index The index at which the element is inserted, if index is a negative number the element is inserted that far from the end of the sequence.
     * @param mixed $element The element to insert.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function insert($index, $element)
    {
        $this->insertSeq($index, array($element));
    }

    /**
     * Insert a range of elements at a particular index.
     *
     * @param integer $index The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param traversable $elements The elements to insert.
     */
    public function insertSeq($index, $elements)
    {
        if ($index < 0) {
            $index += $this->size();
        }

        if ($index < 0 || $index > $this->size()) {
            throw new Exception\IndexException($index);
        }

        // This is very un-list-like behaviour ...
        if (!is_array($elements)) {
            $elements = iterator_to_array($elements);
        }

        $count = count($elements);
        
        if (0 === $count) {
            return;
        }
        
        $this->append(array_fill(0, $count, null));

        for ($i = $this->size() - 1; $i >= $index + $count; --$i) {
            $this->elements[$i] = $this->elements[$i - $count];
        }

        foreach ($elements as $element) {
            $this->elements[$index++] = $element;
        }
    }

    /**
     * Remove the element at a given index.
     *
     * Elements after the given endex are moved forward by one.
     *
     * @param integer $index The index of the element to remove, if index is a negative number the element that far from the end of the sequence is removed.
     *
     * @return mixed The element at $index before removal.
     * @throws Exception\IndexException if $index is out of range.
     */
    public function remove($index)
    {
        $elements = $this->removeRange($index, $index + 1);
        return current($elements);
    }

    /**
     * Remove a range of elements at a given index.
     *
     * @param integer $index The index of the first element to remove, if index is a negative number the removal begins that far from the end of the sequence.
     * @param integer|null $count The number of elements to remove, or null to remove all elements up to the end of the sequence.
     *
     * @return traversable The elements that are removed.
     * @throws Exception\IndexException if $index is out of range.
     */
    public function removeMany($index, $count = null)
    {
        if (null === $count) {
            $end = $this->size();
        } else {
            $end = max(
                $index,
                min(
                    $index + $count,
                    $this->size() + 1
                )
            );
        }

        return $this->removeRange($index, $end);
    }

    /**
     * Remove a range of elements at a given index.
     *
     * Removes all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer $begin The index of the first element to remove, if $begin is a negative number the removal begins that far from the end of the sequence.
     * @param integer $end The index of the last element to remove, if $end is a negative number the removal ends that far from the end of the sequence.
     *
     * @return traversable The elements that are removed.
     * @throws Exception\IndexException if $index is out of range.
     */
    public function removeRange($begin, $end)
    {
        if ($begin < 0) {
            $begin += $this->size();
        }

        if ($end < 0) {
            $end += $this->size();
        }

        if ($begin < 0 || $begin >= $this->size()) {
            throw new Exception\IndexException($begin);
        }

        if ($end < 0 || $end > $this->size()) {
            throw new Exception\IndexException($end);
        }

        if ($end < $begin) {
            return array();
        }

        $elements = array();

        $count = $end - $begin;

        for ($index = $begin; $index < $end; ++$index) {
            $elements[] = $this->elements[$index];
            if ($index + $count < $this->size()) {
                $this->elements[$index] = $this->elements[$index + $count];
            }
        }

        while ($count--) {
            $this->elements->pop();
        }

        return $elements;
    }

    /**
     * Replace a range of elements with a second set of elements.
     *
     * Replaces all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer $index The index of the first element to replace, if index is a negative number the replace begins that far from the end of the sequence.
     * @param traversable $elements The elements to insert.
     * @param integer|null $count The number of elements to replace, or null to replace all elements up to the end of the sequence.
     *
     * @return traversable The elements that are replaced.
     */
    public function replace($index, $elements, $count = null)
    {
        if ($index < 0) {
            $index += $this->size();
        }

        $removedElements = $this->removeMany($index, $count);
        $this->insertSeq($index, $elements);
        return $removedElements;
    }

    /**
     * Replace a range of elements with a second set of elements.
     *
     * @param integer $begin The index of the first element to replace, if begin is a negative number the replace begins that far from the end of the sequence.
     * @param integer $end  The index of the last element to replace, if end is a negativ enumber the replace ends that far from the end of the sequence.
     * @param traversable $elements The elements to insert.
     *
     * @return traversable The elements that are replaced.
     */
    public function replaceRange($begin, $end, $elements)
    {
        if ($begin < 0) {
            $begin += $this->size();
        }

        if ($end < 0) {
            $end += $this->size();
        }

        $removedElements = $this->removeRange($begin, $end);
        $this->insertSeq($begin, $elements);
        return $removedElements;
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
        if ($index1 < 0) {
            $index1 += $this->size();
        }

        if ($index2 < 0) {
            $index2 += $this->size();
        }

        if ($index1 < 0 || $index1 >= $this->size()) {
            throw new Exception\IndexException($index1);
        }

        if ($index2 < 0 || $index2 >= $this->size()) {
            throw new Exception\IndexException($index2);
        }

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

        $temp = $this->elements[$index1];
        $this->elements[$index1] = $this->elements[$index2];
        $this->elements[$index2] = $temp;

        return true;
    }

    private $elements;
}
