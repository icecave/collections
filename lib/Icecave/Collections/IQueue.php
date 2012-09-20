<?php
namespace Icecave\Collections;

// @codeCoverageIgnoreStart

/**
 * A FIFO queue.
 */
interface IQueue extends IMutableCollection {

    /**
     * Fetch the element at the front of the queue.
     *
     * @return mixed The element at the front of the queue.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function front();

    /**
     * Fetch the element at the front of the queue.
     *
     * @param mixed &$element Assigned the element at the front of the queue.
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryFront(&$element);

    /**
     * Add a new element to the end of the queue.
     *
     * @param mixed $element The element to add.
     */
    public function push($element);

    /**
     * Remove and return the element at the front of the queue.
     *
     * @return mixed The element at the front of the queue.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function pop();

    /**
     * Remove the element at the front of the queue.
     *
     * @param mixed &$element Assigned the removed element.
     *
     * @return boolean True if the front element is removed and assigned to $element; otherwise, false.
     */
    public function tryPop(&$element = null);
}
