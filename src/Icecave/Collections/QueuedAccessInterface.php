<?php
namespace Icecave\Collections;

/**
 * The common interface between queues and stacks.
 */
interface QueuedAccessInterface extends MutableCollectionInterface
{
    /**
     * Fetch the element next to be returned by pop(), without removing it from the collection.
     *
     * @return mixed                              The next element.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function next();

    /**
    * Fetch the element next to be returned by pop(), without removing it from the collection.
     *
     * @param mixed &$element Assigned the next element.
     *
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryNext(&$element);

    /**
     * Add a new element.
     *
     * @param mixed $element The element to add.
     */
    public function push($element);

    /**
     * Remove and return the next element.
     *
     * @return mixed                              The next element.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function pop();

    /**
    * Remove the next element.
     *
     * @param mixed &$element Assigned the removed element.
     *
     * @return boolean True if the next element is removed and assigned to $element; otherwise, false.
     */
    public function tryPop(&$element = null);
}
