<?php
namespace Icecave\Collections;

// @codeCoverageIgnoreStart

/**
 * Iterable collections allow (at the very least) sequential access to the elements without modifying the collection.
 *
 * In general there is no guarantee that the elements of a collection are stored in any definite order;
 * the order might, in fact, be different upon each iteration through the collection. However, some
 * specific collection types may provide such guarantees.
 */
interface IIterable extends ICollection
{
    /**
     * Fetch the number of elements in the collection.
     *
     * @see ICollection::empty()
     *
     * @return integer The number of elements in the collection.
     */
    public function size();

    /**
     * Fetch a native array containing the elements in the collection.
     *
     * @return array An array containing the elements in the collection.
     */
    public function elements();

    /**
     * Check if the collection contains an element.
     *
     * @param mixed $element The element to check.
     *
     * @return boolean True if the collection contains $element; otherwise, false.
     */
    public function contains($element);

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     *
     * It is not guaranteed that the concrete type of the filtered collection will match this collection.
     *
     * @param callable|null $predicate A predicate function used to determine which elements to include, or null to include all non-null elements.
     *
     * @return IIterable The filtered collection.
     */
    public function filtered($predicate = null);

    /**
     * Produce a new collection by applying a transformation to each element.
     *
     * The new elements produced by the transform need not be of the same type.
     * It is not guaranteed that the concrete type of the resulting collection will match this collection.
     *
     * @param callable $transform The transform to apply to each element.
     *
     * @return IIterable A new collection produced by applying $transform to each element in this collection.
     */
    public function map($transform);
}
