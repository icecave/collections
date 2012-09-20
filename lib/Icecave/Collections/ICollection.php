<?php
namespace Icecave\Collections;

// @codeCoverageIgnoreStart

/**
 * A collection is an object that stores other objects (called elements).
 */
interface ICollection {

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty();

    /**
     * Fetch a string representation of the collection.
     *
     * The string may not describe all elements of the collection, but should at least
     * provide information on the type and state of the collection.
     *
     * @return string A string representation of the collection.
     */
    public function __toString();
}
