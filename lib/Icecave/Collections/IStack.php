<?php
namespace Icecave\Collections;

// @codeCoverageIgnoreStart

interface IStack extends IMutableCollection {

    /**
     * Fetch the element at the top of the stack.
     *
     * @return mixed The element at the top of the stack.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function top();

    /**
     * Fetch the element at the top of the stack.
     *
     * @param mixed &$element Assigned the element at the top of the stack.
     * @return boolean True is the element exists and was assigned to $element; otherwise, false.
     */
    public function tryTop(&$element);

    /**
     * Add a new element to the end of the stack.
     *
     * @param mixed $element The element to add.
     */
    public function push($element);

    /**
     * Remove and return the element at the top of the stack.
     *
     * @return mixed The element at the top of the sequence.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function pop();

    /**
     * Remove the element at the top of the stack.
     *
     * @param mixed &$element Assigned the removed element.
     *
     * @return boolean True if the top element is removed and assigned to $element; otherwise, false.
     */
    public function tryPop(&$element = null);
}
