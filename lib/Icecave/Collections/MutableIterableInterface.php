<?php
namespace Icecave\Collections;

/**
 * A mutable collection is a collection on which elements can be added and removed.
 */
interface MutableIterableInterface extends IterableInterface, MutableCollectionInterface
{
    /**
     * Filter this collection in-place.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to retain, or null to retain all non-null elements.
     */
    public function filterInPlace($predicate = null);

    /**
     * Replace each element in the collection with the result of a transformation on that element.
     *
     * The new elements produced by the transform must be the same type.
     *
     * @param callable $transform The transform to apply to each element.
     */
    public function mapInPlace($transform);
}
