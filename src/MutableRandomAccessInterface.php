<?php
namespace Icecave\Collections;

/**
 * A mutable random access sequence is a sequence that allows for insertion and removal of elements by their position in the sequence.
 */
interface MutableRandomAccessInterface extends RandomAccessInterface, MutableSequenceInterface
{
    /**
     * Replace the element at a particular position in the sequence.
     *
     * @param integer $index   The index of the element to set, if index is a negative number the element that far from the end of the sequence is set.
     * @param mixed   $element The element to set.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function set($index, $element);

    /**
     * Insert an element at a particular index.
     *
     * @param integer $index   The index at which the element is inserted, if index is a negative number the element is inserted that far from the end of the sequence.
     * @param mixed   $element The element to insert.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function insert($index, $element);

    /**
     * Insert all elements from another collection at a particular index.
     *
     * @param integer      $index    The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     */
    public function insertMany($index, $elements);

    /**
     * Insert a sub-range of another collection at a particular index.
     *
     * This method generally provides an optimised form of:
     *
     *    $this->insertMany($index, $elements->range($begin, $end))
     *
     * And as such, the implementation may require $elements to be the same type as $this.
     *
     * Replaces all elements from the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer               $index    The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param RandomAccessInterface $elements The elements to insert.
     * @param integer               $begin    The index of the first element from $elements to insert, if begin is a negative number the removal begins that far from the end of the sequence.
     * @param integer               $end|null The index of the last element to $elements to insert, if end is a negative number the removal ends that far from the end of the sequence.
     *
     * @throws Exception\IndexException if $index, $begin or $end is out of range.
     */
    public function insertRange($index, RandomAccessInterface $elements, $begin, $end = null);

    /**
     * Remove the element at a given index.
     *
     * Elements after the given endex are moved forward by one.
     *
     * @param integer $index The index of the element to remove, if index is a negative number the element that far from the end of the sequence is removed.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function remove($index);

    /**
     * Remove a range of elements at a given index.
     *
     * @param integer      $index The index of the first element to remove, if index is a negative number the removal begins that far from the end of the sequence.
     * @param integer|null $count The number of elements to remove, or null to remove all elements up to the end of the sequence.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function removeMany($index, $count = null);

    /**
     * Remove a range of elements between two indices.
     *
     * Removes all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer $begin The index of the first element to remove, if begin is a negative number the removal begins that far from the end of the sequence.
     * @param integer $end   The index of the last element to remove, if end is a negative number the removal ends that far from the end of the sequence.
     *
     * @throws Exception\IndexException if $begin or $end is out of range.
     */
    public function removeRange($begin, $end);

    /**
     * Replace a range of elements with a second set of elements.
     *
     * @param integer      $index    The index of the first element to replace, if index is a negative number the replace begins that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     * @param integer|null $count    The number of elements to replace, or null to replace all elements up to the end of the sequence.
     */
    public function replace($index, $elements, $count = null);

    /**
     * Replace a range of elements with a second set of elements.
     *
     * Replaces all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer      $begin    The index of the first element to replace, if begin is a negative number the replace begins that far from the end of the sequence.
     * @param integer      $end      The index of the last element to replace, if end is a negativ enumber the replace ends that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     */
    public function replaceRange($begin, $end, $elements);

    /**
     * Swap the elements at two index positions.
     *
     * @param integer $index1 The index of the first element.
     * @param integer $index2 The index of the second element.
     *
     * @throws Exception\IndexException if $index1 or $index2 is out of range.
     */
    public function swap($index1, $index2);

    /**
     * Swap the elements at two index positions.
     *
     * @param integer $index1 The index of the first element.
     * @param integer $index2 The index of the second element.
     *
     * @return boolean True if $index1 and $index2 are in range and the swap is successful.
     */
    public function trySwap($index1, $index2);
}
