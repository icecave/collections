<?php
namespace Icecave\Collections;

use stdClass;

/**
 * A singly-linked list.
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
            $this->insertMany(0, $collection);
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
            ->map('Icecave\Collections\Support\Stringify::stringify');

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

    //////////////////////////////////////////
    // Implementation of IMutableCollection //
    //////////////////////////////////////////

    /**
     * Remove all elements from the collection.
     */
    public function clear()
    {
        $this->head = null;
        $this->tail = null;
        $this->size = 0;
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
        return $this->size;
    }

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
    public function filtered($predicate = null)
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
     * @return IIterable A new collection produced by applying $transform to each element in this collection.
     */
    public function map($transform)
    {
        $result = new static;

        for ($node = $this->head; null !== $node; $node = $node->next) {
            $result->pushBack(call_user_func($transform, $node->element));
        }

        return $result;
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

        $node = $this->head;
        $prev = null;

        while ($node) {

            // Keep the node ...
            if (call_user_func($predicate, $node->element)) {
                $prev = $node;
            // Don't keep the node, and it's the first one ...
            } elseif (null === $prev) {
                $this->head = $node->next;
                --$this->size;
            // Don't keep the node ...
            } else {
                $prev->next = $node->next;
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
    public function apply($transform)
    {
        for ($node = $this->head; null !== $node; $node = $node->next) {
            $node->element = call_user_func($transform, $node->element);
        }
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

        return $this->head->element;
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

        $element = $this->head->element;

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

        return $this->tail->element;
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
    public function sorted($comparator = null)
    {
        $elements = $this->elements();

        if (null === $comparator) {
            sort($elements);
        } else {
            usort($elements, $comparator);
        }

        $result = new static;
        list($result->head, $result->tail, $result->size) = $this->createNodes($elements);

        return $result;
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
        $result = new static;

        for ($node = $this->head; null !== $node; $node = $node->next) {
            $result->pushFront($node->element);
        }

        return $result;
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
        $result = new static;
        list($result->head, $result->tail, $result->size) = $this->cloneNodes($this->head);

        foreach (func_get_args() as $sequence) {
            $result->insertMany($result->size, $sequence);
        }

        return $result;
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

        list($this->head, $this->tail, $this->size) = $this->createNodes($elements);
    }

    /**
     * Reverse this sequence in-place.
     */
    public function reverse()
    {
        $prev = null;
        $node = $this->head;

        while ($node) {
            $next = $node->next;
            $node->next = $prev;
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
     * @param traversable,... The sequence(s) to append.
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
        }
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

        $element    = $this->head->element;
        $this->head = $this->head->next;

        if (0 === --$this->size) {
            $this->tail = null;
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
        $node = $this->createNode($element);

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
     * @return mixed The element at the back of the sequence.
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
     * @param integer $size The new size of the collection.
     * @param mixed $element The value to use for populating new elements when $size > $this->size().
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
        $this->validateIndex($index);

        return $this->nodeAt($index)->element;
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
     * @param integer $end The index at which the slice will end. If end is a negative number the slice will end that far from the end of the sequence.
     *
     * @return ISequence The sliced sequence.
     * @throws Exception\IndexException if $index is out of range.
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
     * @param mixed $element The element to search for.
     *
     * @return integer|null The index of the element, or null if is not present in the sequence.
     */
    public function indexOf($element)
    {
        for ($index = 0, $node = $this->head; null !== $node; ++$index, $node = $node->next) {
            if ($element === $node->element) {
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
        $this->validateIndex($index);

        $this->nodeAt($index)->element = $element;
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
        $this->insertMany($index, array($element));
    }

    /**
     * Insert a range of elements at a particular index.
     *
     * @param integer $index The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param traversable $elements The elements to insert.
     */
    public function insertMany($index, $elements)
    {
        $this->validateIndex($index, $this->size);

        list($head, $tail, $size) = $this->createNodes($elements);

        if (null === $head) {
            return;
        } elseif (0 === $this->size) {
            $this->head = $head;
            $this->tail = $tail;
        } elseif (0 === $index) {
            $tail->next = $this->head;
            $this->head = $head;
        } elseif ($this->size === $index) {
            $this->tail->next = $head;
            $this->tail = $tail;
        } else {
            $node = $this->nodeAt($index - 1);
            $tail->next = $node->next;
            $node->next = $head;
        }

        $this->size += $size;
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
     * @param integer $index The index of the first element to remove, if index is a negative number the removal begins that far from the end of the sequence.
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
            $node->next = $this->nodeFrom($node, $count + 1);
            $this->size -= $count;

        // Remove everything ...
        } elseif (0 === $index) {
            $this->clear();

        // Remove everything to the end ...
        } else {
            $node = $this->nodeAt($index - 1);
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
     * @param integer $end The index of the last element to remove, if $end is a negative number the removal ends that far from the end of the sequence.
     *
     * @throws Exception\IndexException if $index is out of range.
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
     * Replaces all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer $index The index of the first element to replace, if index is a negative number the replace begins that far from the end of the sequence.
     * @param traversable $elements The elements to insert.
     * @param integer|null $count The number of elements to replace, or null to replace all elements up to the end of the sequence.
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
     * @param integer $begin The index of the first element to replace, if begin is a negative number the replace begins that far from the end of the sequence.
     * @param integer $end  The index of the last element to replace, if end is a negativ enumber the replace ends that far from the end of the sequence.
     * @param traversable $elements The elements to insert.
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

    protected function doSwap($index1, $index2)
    {
        $a = min($index1, $index2);
        $b = max($index1, $index2);

        $node1 = $this->nodeAt($a);
        $node2 = $this->nodeFrom($node1, $b - $a);

        $element        = $node1->element;
        $node1->element = $node2->element;
        $node2->element = $element;
    }

    protected function validateIndex(&$index, $max = null)
    {
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

    protected function createNode($element = null, $next = null)
    {
        $node = new stdClass;
        $node->next = $next;
        $node->element = $element;
        return $node;
    }

    protected function nodeAt($index) {
        return $this->nodeFrom($this->head, $index);
    }

    protected function nodeFrom($node, $count) {
        while ($node && $count--) {
            $node = $node->next;
        }
        return $node;
    }

    protected function cloneNodes($start, $stop = null) {
        $head = null;
        $tail = null;
        $size = 0;

        for ($node = $start; $stop !== $node; $node = $node->next) {
            $n = $this->createNode($node->element);
            if (null === $head) {
                $head = $n;
            } else {
                $tail->next = $n;
            }
            $tail = $n;
            ++$size;
        }

        return array($head, $tail, $size);
    }

    protected function createNodes($elements) {
        $head = null;
        $tail = null;
        $size = 0;

        foreach ($elements as $element) {
            $node = $this->createNode($element);
            if (null === $head) {
                $head = $node;
            } else {
                $tail->next = $node;
            }
            $tail = $node;
            ++$size;
        }

        return array($head, $tail, $size);
    }

    private $head;
    private $tail;
    private $size;
}
