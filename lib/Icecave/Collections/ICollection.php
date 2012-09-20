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
     * @return boolean true if the collection is empty; otherwise, false.
     */
    public function isEmpty();
}
