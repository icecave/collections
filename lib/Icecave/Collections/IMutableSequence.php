<?php
namespace Icecave\Collections;

// @codeCoverageIgnoreStart

/**
 * A Sequence is a variable-sized collection whose elements are arranged in a strict linear order.
 *
 * Mutable sequences support insertion and removal of elements.
 */
interface IMutableSequence extends ISequence, IMutableIterable {

    /**
     * Sort this sequence in-place.
     *
     * @param callable|null $comparitor A strcmp style comparitor function.
     */
    public function sort($comparitor = null);

    /**
     * Reverse this sequence in-place.
     */
    public function reverse();

    /**
     * Appending elements in the given sequence to this sequence.
     *
     * @param traversable,... The sequence(s) to append.
     */
    public function append($sequence);

    /**
     * Add a new element to the front of the sequence.
     *
     * @param mixed $element The element to prepend.
     */
    public function pushFront($element);

    /**
     * Remove and return the element at the front of the sequence.
     *
     * @return mixed The element at the front of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popFront();

    /**
     * Remove the element at the front of the sequence.
     *
     * @param mixed &$element Assigned the removed element.
     *
     * @return boolean True if the front element is removed and assigned to $element; otherwise, false.
     */
    public function tryPopFront(&$element = null);

    /**
     * Add a new element to the back of the sequence.
     *
     * @param mixed $element The element to append.
     */
    public function pushBack($element);

    /**
     * Remove and return the element at the back of the sequence.
     *
     * @return mixed The element at the back of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function popBack();

    /**
     * Remove the element at the back of the sequence.
     *
     * @param mixed &$element Assigned the removed element.
     *
     * @return boolean True if the back element is removed and assigned to $element; otherwise, false.
     */
    public function tryPopBack(&$element = null);

    /**
     * Resize the sequence.
     *
     * @param integer $size The new size of the collection.
     * @param mixed $element The value to use for populating new elements when $size > $this->size().
     */
    public function resize($size, $element = null);
}
