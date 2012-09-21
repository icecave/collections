<?php
namespace Icecave\Collections;

// @codeCoverageIgnoreStart

/**
 * A mutable collection is a collection on which elements can be added and removed.
 */
interface IMutableCollection extends ICollection
{
    /**
     * Remove all elements from the collection.
     */
    public function clear();
}
