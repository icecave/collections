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
        $this->list = new SplDoublyLinkedList;
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    //////////////////////////////////////////
    // Implementation of IMutableCollection //
    //////////////////////////////////////////

    /**
     * Remove all elements from the collection.
     */
    public function clear()
    {
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    /**
     * Fetch a native array containing the elements in the collection.
     *
     * @return array An array containing the elements in the collection.
     */
    public function elements()
    {
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     *
     * It is not guaranteed that the concrete type of the filtered collection will match this collection.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to include, or null to include all non-null elements.
     *
     * @return IIterable The filtered collection.
     */
    public function filtered($predicate = null)
    {
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    /**
     * Fetch the first element in the sequence.
     *
     * @param mixed &$element Assigned the element at the front of collection.
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryFront(&$element)
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Fetch the last element in the sequence.
     *
     * @return mixed The first element in the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function back()
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Fetch the last element in the sequence.
     *
     * @param mixed &$element Assigned the element at the front of collection.
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryBack(&$element)
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Create a new sequence with the elements from this sequence in sorted order.
     *
     * It is not guaranteed that the concrete type of the sorted collection will match this collection.
     *
     * @param callable|null $comparator A strcmp style comparator function.
     *
     * @return ISequence
     */
    public function sorted($comparator = null)
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Create a new sequence with the elements from this sequence in reverse order.
     *
     * It is not guaranteed that the concrete type of the reversed collection will match this collection.
     *
     * @return ISequence The reversed sequence.
     */
    public function reversed()
    {
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    /**
     * Reverse this sequence in-place.
     */
    public function reverse()
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Appending elements in the given sequence to this sequence.
     *
     * @param traversable,... The sequence(s) to append.
     */
    public function append($sequence)
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Add a new element to the front of the sequence.
     *
     * @param mixed $element The element to prepend.
     */
    public function pushFront($element)
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Remove and return the element at the front of the sequence.
     *
     * @return mixed The element at the front of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popFront()
    {
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    /**
     * Add a new element to the back of the sequence.
     *
     * @param mixed $element The element to append.
     */
    public function pushBack($element)
    {
        throw new \Exception('Not Implemented');
    }

    /**
     * Remove and return the element at the back of the sequence.
     *
     * @return mixed The element at the back of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popBack()
    {
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    /**
     * Resize the sequence.
     *
     * @param integer $size The new size of the collection.
     * @param mixed $element The value to use for populating new elements when $size > $this->size().
     */
    public function resize($size, $element = null)
    {
        throw new \Exception('Not Implemented');
    }

    //////////////////////////////////////
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    /**
     * Insert a range of elements at a particular index.
     *
     * @param integer $index The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param traversable $elements The elements to insert.
     */
    public function insertSeq($index, $elements)
    {
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
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
        throw new \Exception('Not Implemented');
    }

    private $list;
}
