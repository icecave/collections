<?php
namespace Icecave\Collections;

/**
 * A mutable collection is a collection on which elements can be added and removed.
 */
interface MutableCollectionInterface extends CollectionInterface
{
    /**
     * Remove all elements from the collection.
     */
    public function clear();
}
