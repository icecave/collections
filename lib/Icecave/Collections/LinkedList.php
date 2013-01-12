<?php
namespace Icecave\Collections;

use Countable;
use Icecave\Collections\TypeCheck\TypeCheck;
use Iterator;
use stdClass;

/**
 * A singly-linked list.
 */
class LinkedList implements MutableRandomAccessInterface, Countable, Iterator
{
    /**
     * @param mixed<mixed>|null $collection An iterable type containing the elements to include in this list, or null to create an empty list.
     */
    public function __construct($collection = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->clear();

        if (null !== $collection) {
            $this->insertMany(0, $collection);
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

            // Otherwise this must be the head ...
            } else {
                $this->head = $newNode;
            }

            // This node is the current node of iteration ...
            if ($node === $this->currentNode) {
                $this->currentNode = $newNode;
            }

            $prev = $node;
            $node = $node->next;
        }

        $this->tail = $prev;
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
        $this->typeCheck->__toString(func_get_args());

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
        $this->typeCheck->clear(func_get_args());

        $this->head = null;
        $this->tail = null;
        $this->size = 0;

        $this->currentNode = null;
        $this->currentIndex = 0;
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
        $this->typeCheck->contains(func_get_args());

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
        $this->typeCheck->filtered(func_get_args());

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
        $this->typeCheck->map(func_get_args());

        $result = new static;

        for ($node = $this->head; null !== $node; $node = $node->next) {
            $result->pushBack(call_user_func($transform, $node->element));
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
        $this->typeCheck->apply(func_get_args());

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
        $this->typeCheck->front(func_get_args());

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
        $this->typeCheck->tryFront(func_get_args());

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
        $this->typeCheck->back(func_get_args());

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
        $this->typeCheck->tryBack(func_get_args());

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
        $this->typeCheck->sorted(func_get_args());

        $result = clone $this;
        $result->sort($comparator);
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
        $this->typeCheck->reversed(func_get_args());

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
        $this->typeCheck->join(func_get_args());

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
    public function sort($comparator = null)
    {
        $this->typeCheck->sort(func_get_args());

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
        $this->rewind();
    }

    /**
     * Reverse this sequence in-place.
     */
    public function reverse()
    {
        $this->typeCheck->reverse(func_get_args());

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

        $this->head = $this->createNode($element, $this->head);

        if (0 === $this->size++) {
            $this->tail = $this->head;
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
        $this->typeCheck->popFront(func_get_args());

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
     * @return mixed                              The element at the back of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popBack()
    {
        $this->typeCheck->popBack(func_get_args());

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
     * @throws Exception\IndexException if no such index exists.
     */
    public function get($index)
    {
        $this->typeCheck->get(func_get_args());

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
        $this->typeCheck->slice(func_get_args());

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
     * @throws Exception\IndexException if $index is out of range.
     */
    public function range($begin, $end)
    {
        $this->typeCheck->range(func_get_args());

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
        $this->typeCheck->indexOf(func_get_args());

        for ($index = 0, $node = $this->head; null !== $node; ++$index, $node = $node->next) {
            if ($element === $node->element) {
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

        $this->removeMany($index, $count);
        $this->insertMany($index, $elements);
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
        $this->typeCheck->swap(func_get_args());

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
        $this->typeCheck->trySwap(func_get_args());

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
        $this->typeCheck->count(func_get_args());

        return $this->size();
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function current()
    {
        $this->typeCheck->current(func_get_args());

        return $this->currentNode->element;
    }

    public function key()
    {
        $this->typeCheck->key(func_get_args());

        return $this->currentIndex;
    }

    public function next()
    {
        $this->typeCheck->next(func_get_args());

        $this->currentNode = $this->currentNode->next;
        ++$this->currentIndex;
    }

    public function rewind()
    {
        $this->typeCheck->rewind(func_get_args());

        $this->currentNode = $this->head;
        $this->currentIndex = 0;
    }

    public function valid()
    {
        $this->typeCheck->valid(func_get_args());

        return null !== $this->currentNode;
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    /**
     * @param integer $index1
     * @param integer $index2
     */
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

    /**
     * @param integer      &$index
     * @param integer|null $max
     */
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

    /**
     * @param mixed         $element
     * @param stdClass|null $next
     */
    protected function createNode($element = null, stdClass $next = null)
    {
        $node = new stdClass;
        $node->next = $next;
        $node->element = $element;

        return $node;
    }

    /**
     * @param integer $index
     */
    protected function nodeAt($index)
    {
        return $this->nodeFrom($this->head, $index);
    }

    /**
     * @param stdClass $node
     * @param integer  $count
     */
    protected function nodeFrom(stdClass $node, $count)
    {
        while ($node && $count--) {
            $node = $node->next;
        }

        return $node;
    }

    /**
     * @param stdClass      $start
     * @param stdClass|null $stop
     */
    protected function cloneNodes(stdClass $start, stdClass $stop = null)
    {
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

    /**
     * @param mixed<mixed> $elements
     */
    protected function createNodes($elements)
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
            }
            $tail = $node;
            ++$size;
        }

        return array($head, $tail, $size);
    }

    private $typeCheck;
    private $head;
    private $tail;
    private $size;
    private $currentNode;
    private $currentIndex;
}
