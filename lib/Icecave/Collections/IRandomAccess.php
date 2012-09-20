<?php
namespace Icecave\Collections;

// @codeCoverageIgnoreStart

/**
 * A random access sequence is a sequence that provides access to arbitrary elements by their position in the sequence.
 */
interface IRandomAccess extends ISequence {

    /**
     * Fetch the element at the given index.
     *
     * @param mixed $index The index of the element to fetch, if index is a negative number the element that far from the end of the sequence is returned.
     *
     * @return mixed The element at $index.
     * @throws Exception\IndexError Thrown if no such index exists.
     */
    public function get($index);

    /**
     * Extract a range of elements.
     *
     * It is not guaranteed that the concrete type of the slice collection will match this collection.
     *
     * @param integer $index The index from which the slice will start. If index is a negative number the slice will begin that far from the end of the sequence.
     * @param integer|null $count The maximum number of elements to include in the slice, or null to include all elements from $index to the end of the sequence.
     *
     * @return ISequence The sliced sequence.
     * @throws Exception\IndexError Thrown if $index is out of range.
     */
    public function slice($index, $count = null);

    /**
     * Find the index of the first instance of a particular element in the sequence.
     *
     * @param mixed $element The element to search for.
     *
     * @return integer|null The index of the element, or null if is not present in the sequence.
     */
    public function indexOf($element);
}
