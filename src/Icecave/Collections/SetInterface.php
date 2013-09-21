<?php
namespace Icecave\Collections;

use Countable;

/**
 * An iterable collection with unique elements.
 */
interface SetInterface extends MutableIterableInterface, Countable
{
    /**
     * Return the first of the given elements that is contained in the set.
     *
     * @param mixed $element        The element to search for.
     * @param mixed $additional,... Additional elements to search for.
     *
     * @return mixed                         The first of the given elements that is contained in the set.
     * @throws Exception\UnknownKeyException if none of the elements exist.
     */
    public function cascade($element);

    /**
     * Return the first of the given elements that is contained in the set, or a default if none are found.
     *
     * @param mixed $default        The default value to return if no such elements exist.
     * @param mixed $element        The element to search for.
     * @param mixed $additional,... Additional elements to search for.
     *
     * @return mixed The first of the given elements that is contained in the set, or $default if none are found.
     */
    public function cascadeWithDefault($default, $element);

    /**
     * Return the first of the given elements that is contained in the set.
     *
     * Behaves as per {@see SetInterface::cascade()} except that the elements are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed<mixed> $elements The list of elements.
     *
     * @return mixed                         The first of the given elements that is contained in the set.
     * @throws Exception\UnknownKeyException if none of the elements exist.
     */
    public function cascadeIterable($elements);

    /**
     * Return the first of the given elements that is contained in the set, or a default if none are found.
     *
     * Behaves as per {@see SetInterface::cascadeDefault()} except that the elements are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed        $default  The default value to return if no such elements exist.
     * @param mixed<mixed> $elements The list of elements.
     *
     * @return mixed The first of the given elements that is contained in the set, or $default if none are found.
     */
    public function cascadeIterableWithDefault($default, $elements);

    /**
     * Add an element to the set.
     *
     * @param mixed $element The element to add.
     *
     * @return boolean True if the element was added to the set, or false if the set already contained the element.
     */
    public function add($element);

    /**
     * Add multiple elements to the set.
     *
     * @see SetInterface::unionInPlace() may be faster when adding all elements from a another set.
     *
     * @param mixed<mixed> $elements The elements to add.
     */
    public function addMany($elements);

    /**
     * Remove an element from the set, if it exists.
     *
     * @param mixed $element The element to remove.
     *
     * @return boolean True if the element was removed from the set, or false if the set dot not contain the element.
     */
    public function remove($element);

    /**
     * Remove multiple elements from the set.
     *
     * @see SetInterface::diffInPlace() may be faster when removing all elements from a another set.
     *
     * @param mixed<mixed> $elements The elements to remote.
     */
    public function removeMany($elements);

    /**
     * Check if this set is equal to another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains the same elements as $set; otherwise false.
     */
    public function isEqualSet(SetInterface $set);

    /**
     * Check if this set is a superset of another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains all of the elements in $set; otherwise, false.
     */
    public function isSuperSet(SetInterface $set);

    /**
     * Check if this set is a subset of another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains only elements present in $set; otherwise, false.
     */
    public function isSubSet(SetInterface $set);

    /**
     * Check if this set is a proper superset of another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains all of elements in $set, but is not equal to $set; otherwise, false.
     */
    public function isProperSuperSet(SetInterface $set);

    /**
     * Check if this set is a proper subset of another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains only elements present in $set, but is not equal to $set; otherwise, false.
     */
    public function isProperSubSet(SetInterface $set);

    /**
     * Check if this set is intersecting another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains one or more elements present in $set; otherwise false.
     */
    public function isIntersecting(SetInterface $set);

    /**
     * Compute the union of this set and another.
     *
     * @param SetInterface $set The second set.
     *
     * @return SetInterface A set containing all elements of $this and $elements.
     */
    public function union(SetInterface $set);

    /**
     * Compute the union of this set and another, in place.
     *
     * @param SetInterface $set The second set.
     */
    public function unionInPlace(SetInterface $set);

    /**
     * Compute the intersection of this set and another.
     *
     * @param SetInterface $set The second set.
     *
     * @return SetInterface A set containing only the elements present in $this and $elements.
     */
    public function intersect(SetInterface $set);

    /**
     * Compute the intersection of this set and another, in place.
     *
     * @param SetInterface $set The second set.
     */
    public function intersectInPlace(SetInterface $set);

    /**
     * Compute the difference (or complement) of this set and another.
     *
     * @param SetInterface $set The second set.
     *
     * @return SetInterface A set containing only the elements present in $this, but not $elements.
     */
    public function diff(SetInterface $set);

    /**
     * Compute the difference (or complement) of this set and another, in place.
     *
     * @param SetInterface $set The second set.
     */
    public function diffInPlace(SetInterface $set);

    /**
     * Compute the symmetric difference (or complement) of this set and another.
     *
     * The symmetric difference is the set of elements which are in either of the sets and not in their intersection.
     *
     * @param SetInterface $set The second set.
     *
     * @return SetInterface A set containing only the elements present in $this, or $elements, but not both.
     */
    public function symmetricDiff(SetInterface $set);

    /**
     * Compute the symmetric difference (or complement) of this set and another, in place.
     *
     * The symmetric difference is the set of elements which are in either of the sets and not in their intersection.
     *
     * @param SetInterface $set The second set.
     */
    public function symmetricDiffInPlace(SetInterface $set);
}
