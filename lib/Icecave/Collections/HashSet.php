<?php
namespace Icecave\Collections;

use ArrayIterator;
use Countable;
use Icecave\Collections\Iterator\SequentialKeyIterator;
use Icecave\Collections\Iterator\Traits;
use Icecave\Collections\TypeCheck\TypeCheck;
use Icecave\Repr\Repr;
use InvalidArgumentException;
use IteratorAggregate;
use Serializable;

/**
 * An iterable collection with unique elements.
 */
class HashSet implements MutableIterableInterface, Countable, IteratorAggregate, Serializable
{
    /**
     * @param mixed<mixed>|null $collection   An iterable type containing the elements to include in this set, or null to create an empty set.
     * @param callable|null     $hashFunction The function to use for generating hashes of elements, or null to use the default.
     */
    public function __construct($collection = null, $hashFunction = null)
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        if (null === $hashFunction) {
            $hashFunction = new AssociativeKeyGenerator;
        }

        $this->hashFunction = $hashFunction;
        $this->elements = array();

        if (null !== $collection) {
            foreach ($collection as $element) {
                $this->add($element);
            }
        }
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    /**
     * Fetch the number of elements in the collection.
     *
     * @see HashSet::isEmpty()
     *
     * @return integer The number of elements in the collection.
     */
    public function size()
    {
        $this->typeCheck->size(func_get_args());

        return count($this->elements);
    }

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty()
    {
        $this->typeCheck->isEmpty(func_get_args());

        return empty($this->elements);
    }

    /**
     * Fetch a string representation of the collection.
     *
     * The string may not describe all elements of the collection, but should at least
     * provide information on the type and state of the collection.
     *
     * @return string A string representation of the collection.
     */
    public function __toString()
    {
        if ($this->isEmpty()) {
            return '<HashSet 0>';
        } elseif ($this->size() > 3) {
            $format = '<HashSet %d [%s, ...]>';
            $elements = array_slice($this->elements, 0, 3);
        } else {
            $format = '<HashSet %d [%s]>';
            $elements = $this->elements;
        }

        return sprintf(
            $format,
            $this->size(),
            implode(
                ', ',
                array_map(
                    'Icecave\Repr\Repr::repr',
                    $elements
                )
            )
        );
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    /**
     * Remove all elements from the collection.
     */
    public function clear()
    {
        $this->typeCheck->clear(func_get_args());

        $this->elements = array();
    }

    //////////////////////////////////////////////
    // Implementation of IteratorTraitsProvider //
    //////////////////////////////////////////////

    /**
     * Return traits describing the collection's iteration capabilities.
     *
     * @return Traits
     */
    public function iteratorTraits()
    {
        $this->typeCheck->iteratorTraits(func_get_args());

        return new Traits(true, true);
    }

    /////////////////////////////////////////
    // Implementation of IterableInterface //
    /////////////////////////////////////////

    /**
     * Fetch a native array containing the elements in the collection.
     *
     * @return array An array containing the elements in the collection.
     */
    public function elements()
    {
        $this->typeCheck->elements(func_get_args());

        return array_values($this->elements);
    }

    /**
     * Check if the collection contains an element with the given value.
     *
     * @param mixed $value The value to check.
     *
     * @return boolean True if the collection contains $value; otherwise, false.
     */
    public function contains($value)
    {
        $this->typeCheck->contains(func_get_args());

        return array_key_exists(
            $this->generateHash($value),
            $this->elements
        );
    }

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     *
     * The predicate must be a callable with the following signature:
     *  function (mixed $element) { return $true_to_retain_element; }
     *
     * @param callable|null $predicate A predicate function used to determine which elements to include, or null to include all non-null elements.
     *
     * @return HashSet The filtered collection.
     */
    public function filter($predicate = null)
    {
        $this->typeCheck->filter(func_get_args());

        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $result = $this->createSet();

        foreach ($this->elements as $hash => $element) {
            if (call_user_func($predicate, $element)) {
                $result->elements[$hash] = $element;
            }
        }

        return $result;
    }

    /**
     * Produce a new collection by applying a transformation to each element.
     *
     * The new elements produced by the transform need not be of the same type.
     * It is not guaranteed that the concrete type of the resulting collection will match this collection.
     *
     * The transform must be a callable with the following signature:
     *  function (mixed $element) { return $new_element; }
     *
     * @param callable $transform The transform to apply to each element.
     *
     * @return IterableInterface A new collection produced by applying $transform to each element in this collection.
     */
    public function map($transform)
    {
        $this->typeCheck->map(func_get_args());

        $result = $this->createSet();

        foreach ($this->elements as $hash => $element) {
            $result->elements[$hash] = call_user_func($transform, $element);
        }

        return $result;
    }

    /**
     * Partitions this collection into two collections according to a predicate.
     *
     * It is not guaranteed that the concrete type of the partitioned collections will match this collection.
     *
     * @param callable $predicate A predicate function used to determine which partitioned collection to place the elements in.
     *
     * @return tuple<IterableInterface,IterableInterface> A 2-tuple containing the partitioned collections. The first collection contains the element for which the predicate returned true.
     */
    public function partition($predicate)
    {
        $this->typeCheck->partition(func_get_args());

        $left  = $this->createSet();
        $right = $this->createSet();

        foreach ($this->elements as $hash => $element) {
            if (call_user_func($predicate, $element)) {
                $left->elements[$hash] = $element;
            } else {
                $right->elements[$hash] = $element;
            }
        }

        return array($left, $right);
    }

    /**
     * Invokes the given callback on every element in the collection.
     *
     * This method behaves the same as {@see IterableInterface::map()} except that the return value of the callback is not retained.
     *
     * @param callable $callback The callback to invoke with each element.
     */
    public function each($callback)
    {
        $this->typeCheck->each(func_get_args());

        foreach ($this->elements as $element) {
            call_user_func($callback, $element);
        }
    }

    /**
     * Returns true if the given predicate returns true for all elements.
     *
     * The loop is short-circuited, exiting after the first element for which the predicate returns false.
     *
     * @param callable $predicate
     *
     * @return boolean True if $predicate($element) returns true for all elements; otherwise, false.
     */
    public function all($predicate)
    {
        $this->typeCheck->all(func_get_args());

        foreach ($this->elements as $element) {
            if (!call_user_func($predicate, $element)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if the given predicate returns true for any element.
     *
     * The loop is short-circuited, exiting after the first element for which the predicate returns false.
     *
     * @param callable $predicate
     *
     * @return boolean True if $predicate($element) returns true for any element; otherwise, false.
     */
    public function any($predicate)
    {
        $this->typeCheck->any(func_get_args());

        foreach ($this->elements as $element) {
            if (call_user_func($predicate, $element)) {
                return true;
            }
        }

        return false;
    }

    ////////////////////////////////////////////////
    // Implementation of MutableIterableInterface //
    ////////////////////////////////////////////////

    /**
     * Filter this collection in-place.
     *
     * The predicate must be a callable with the following signature:
     *  function (mixed $element) { return $true_to_retain_element; }
     *
     * @param callable|null $predicate A predicate function used to determine which elements to retain, or null to retain all elements with non-null values.
     */
    public function filterInPlace($predicate = null)
    {
        $this->typeCheck->filterInPlace(func_get_args());

        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        foreach ($this->elements as $hash => $element) {
            if (!call_user_func($predicate, $element)) {
                unset($this->elements[$hash]);
            }
        }
    }

    /**
     * Replace each element in the collection with the result of a transformation on that element.
     *
     * The new elements produced by the transform must be the same type.
     *
     * The transform must be a callable with the following signature:
     *  function (mixed $element) { return $element; }
     *
     * @param callable $transform The transform to apply to each element.
     */
    public function mapInPlace($transform)
    {
        $this->typeCheck->mapInPlace(func_get_args());

        foreach ($this->elements as $hash => &$element) {
            $element = call_user_func($transform, $element);
        }
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function count()
    {
        $this->typeCheck->count(func_get_args());

        return $this->size();
    }

    /////////////////////////////////////////
    // Implementation of IteratorAggregate //
    /////////////////////////////////////////

    public function getIterator()
    {
        $this->typeCheck->getIterator(func_get_args());

        return new SequentialKeyIterator(
            new ArrayIterator($this->elements)
        );
    }

    ////////////////////////////////////
    // Implementation of Serializable //
    ////////////////////////////////////

    /**
     * Serialize the collection.
     *
     * @return string The serialized data.
     */
    public function serialize()
    {
        $this->typeCheck->serialize(func_get_args());

        return serialize(
            array(
                $this->elements(),
                $this->hashFunction
            )
        );
    }

    /**
     * Unserialize collection data.
     *
     * @param string $packet The serialized data.
     */
    public function unserialize($packet)
    {
        TypeCheck::get(__CLASS__)->unserialize(func_get_args());

        list($elements, $hashFunction) = unserialize($packet);
        $this->__construct($elements, $hashFunction);
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    /**
     * Return the first of the given elements that is contained in the set.
     *
     * @param mixed $element        The element to search for.
     * @param mixed $additional,... Additional elements to search for.
     *
     * @return mixed                         The first of the given elements that is contained in the set.
     * @throws Exception\UnknownKeyException if none of the elements exist.
     */
    public function cascade($element)
    {
        $this->typeCheck->cascade(func_get_args());

        return $this->cascadeIterable(func_get_args());
    }

    /**
     * Return the first of the given elements that is contained in the set, or a default if none are found.
     *
     * @param mixed $default        The default value to return if no such elements exist.
     * @param mixed $element        The element to search for.
     * @param mixed $additional,... Additional elements to search for.
     *
     * @return mixed The first of the given elements that is contained in the set, or $default if none are found.
     */
    public function cascadeWithDefault($default, $element)
    {
        $this->typeCheck->cascadeWithDefault(func_get_args());

        $elements = func_get_args();
        $default = array_shift($elements);

        return $this->cascadeIterableWithDefault($default, $elements);
    }

    /**
     * Return the first of the given elements that is contained in the set.
     *
     * Behaves as per {@see HashSet::cascade()} except that the elements are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed<mixed> $elements The list of elements.
     *
     * @return mixed                         The first of the given elements that is contained in the set.
     * @throws Exception\UnknownKeyException if none of the elements exist.
     */
    public function cascadeIterable($elements)
    {
        $this->typeCheck->cascadeIterable(func_get_args());

        foreach ($elements as $element) {
            if ($this->contains($element)) {
                return $element;
            }
        }

        throw new Exception\UnknownKeyException($element);
    }

    /**
     * Return the first of the given elements that is contained in the set, or a default if none are found.
     *
     * Behaves as per {@see HashSet::cascadeDefault()} except that the elements are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed        $default  The default value to return if no such elements exist.
     * @param mixed<mixed> $elements The list of elements.
     *
     * @return mixed The first of the given elements that is contained in the set, or $default if none are found.
     */
    public function cascadeIterableWithDefault($default, $elements)
    {
        $this->typeCheck->cascadeIterableWithDefault(func_get_args());

        foreach ($elements as $element) {
            if ($this->contains($element)) {
                return $element;
            }
        }

        return $default;
    }

    /**
     * Add an element to the set.
     *
     * @param mixed $element The element to add.
     *
     * @return boolean True if the element was added to the set, or false if the set already contained the element.
     */
    public function add($element)
    {
        $this->typeCheck->add(func_get_args());

        $size = $this->size();
        $hash = $this->generateHash($element);

        $this->elements[$hash] = $element;

        return $size < $this->size();
    }

    /**
     * Remove an element from the set, if it exists.
     *
     * @param mixed $element The element to remove.
     *
     * @return boolean True if the element was removed from the set, or false if the set dot not contain the element.
     */
    public function remove($element)
    {
        $this->typeCheck->remove(func_get_args());

        $size = $this->size();
        $hash = $this->generateHash($element);

        unset($this->elements[$hash]);

        return $size > $this->size();
    }

    /**
     * Check if this set is equal to another.
     *
     * @param HashSet $set The set to compare against.
     *
     * @return boolean True if this set contains the same elements as $set; otherwise false.
     */
    public function isEqualSet(HashSet $set)
    {
        $this->typeCheck->isEqualSet(func_get_args());

        $this->assertCompatible($set);

        if ($this->size() !== $set->size()) {
            return false;
        }

        foreach ($set->elements as $hash => $element) {
            if (!array_key_exists($hash, $this->elements)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if this set is a superset of another.
     *
     * @param HashSet $set The set to compare against.
     *
     * @return boolean True if this set contains all of the elements in $set; otherwise, false.
     */
    public function isSuperSet(HashSet $set)
    {
        $this->typeCheck->isSuperSet(func_get_args());

        $this->assertCompatible($set);

        if ($this->size() < $set->size()) {
            return false;
        }

        foreach ($set->elements as $hash => $element) {
            if (!array_key_exists($hash, $this->elements)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if this set is a subset of another.
     *
     * @param HashSet $set The set to compare against.
     *
     * @return boolean True if this set contains only elements present in $set; otherwise, false.
     */
    public function isSubSet(HashSet $set)
    {
        $this->typeCheck->isSubSet(func_get_args());

        $this->assertCompatible($set);

        return $set->isSuperSet($this);
    }

    /**
     * Check if this set is a proper superset of another.
     *
     * @param HashSet $set The set to compare against.
     *
     * @return boolean True if this set contains all of elements in $set, but is not equal to $set; otherwise, false.
     */
    public function isProperSuperSet(HashSet $set)
    {
        $this->typeCheck->isProperSuperSet(func_get_args());

        $this->assertCompatible($set);

        if ($this->size() <= $set->size()) {
            return false;
        }

        foreach ($set->elements as $hash => $element) {
            if (!array_key_exists($hash, $this->elements)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if this set is a proper subset of another.
     *
     * @param HashSet $set The set to compare against.
     *
     * @return boolean True if this set contains only elements present in $set, but is not equal to $set; otherwise, false.
     */
    public function isProperSubSet(HashSet $set)
    {
        $this->typeCheck->isProperSubSet(func_get_args());

        $this->assertCompatible($set);

        return $set->isProperSuperSet($this);
    }

    /**
     * Check if this set is intersecting another.
     *
     * @param HashSet $set The set to compare against.
     *
     * @return boolean True if this set contains one or more elements present in $set; otherwise false.
     */
    public function isIntersecting(HashSet $set)
    {
        $this->typeCheck->isIntersecting(func_get_args());

        $this->assertCompatible($set);

        foreach ($set->elements as $hash => $element) {
            if (array_key_exists($hash, $this->elements)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compute the union of this set and another.
     *
     * @param HashSet $set The second set.
     *
     * @return HashSet A set containing all elements of $this and $elements.
     */
    public function union(HashSet $set)
    {
        $this->typeCheck->union(func_get_args());

        $this->assertCompatible($set);

        $result = $this->createSet();
        $result->elements = $this->elements + $set->elements;

        return $result;
    }

    /**
     * Compute the union of this set and another, in place.
     *
     * @param HashSet $set The second set.
     */
    public function unionInPlace(HashSet $set)
    {
        $this->typeCheck->unionInPlace(func_get_args());

        $this->assertCompatible($set);

        $this->elements += $set->elements;
    }

    /**
     * Compute the intersection of this set and another.
     *
     * @param HashSet $set The second set.
     *
     * @return HashSet A set containing only the elements present in $this and $elements.
     */
    public function intersect(HashSet $set)
    {
        $this->typeCheck->intersect(func_get_args());

        $this->assertCompatible($set);

        $result = $this->createSet();
        $result->elements = array_intersect_key($this->elements, $set->elements);

        return $result;
    }

    /**
     * Compute the intersection of this set and another, in place.
     *
     * @param HashSet $set The second set.
     */
    public function intersectInPlace(HashSet $set)
    {
        $this->typeCheck->intersectInPlace(func_get_args());

        $this->assertCompatible($set);

        foreach ($this->elements as $hash => $element) {
            if (!array_key_exists($hash, $set->elements)) {
                unset($this->elements[$hash]);
            }
        }
    }

    /**
     * Compute the difference (or complement) of this set and another.
     *
     * @param HashSet $set The second set.
     *
     * @return HashSet A set containing only the elements present in $this, but not $elements.
     */
    public function diff(HashSet $set)
    {
        $this->typeCheck->diff(func_get_args());

        $this->assertCompatible($set);

        $result = $this->createSet();
        $result->elements = array_diff_key($this->elements, $set->elements);

        return $result;
    }

    /**
     * Compute the difference (or complement) of this set and another, in place.
     *
     * @param HashSet $set The second set.
     */
    public function diffInPlace(HashSet $set)
    {
        $this->typeCheck->diffInPlace(func_get_args());

        $this->assertCompatible($set);

        foreach ($set->elements as $hash => $element) {
            unset($this->elements[$hash]);
        }
    }

    /**
     * Compute the symmetric difference (or complement) of this set and another.
     *
     * The symmetric difference is the set of elements which are in either of the sets and not in their intersection.
     *
     * @param HashSet $set The second set.
     *
     * @return HashSet A set containing only the elements present in $this, or $elements, but not both.
     */
    public function symmetricDiff(HashSet $set)
    {
        $this->typeCheck->symmetricDiff(func_get_args());

        $this->assertCompatible($set);

        $result = $this->union($set);
        $result->diffInPlace(
            $this->intersect($set)
        );

        return $result;
    }

    /**
     * Compute the symmetric difference (or complement) of this set and another, in place.
     *
     * The symmetric difference is the set of elements which are in either of the sets and not in their intersection.
     *
     * @param HashSet $set The second set.
     */
    public function symmetricDiffInPlace(HashSet $set)
    {
        $this->typeCheck->symmetricDiffInPlace(func_get_args());

        $this->assertCompatible($set);

        $intersection = $this->intersect($set);
        $this->unionInPlace($set);
        $this->diffInPlace($intersection);
    }

    /**
     * @param mixed $key
     *
     * @return integer|string
     */
    private function generateHash($key)
    {
        return call_user_func($this->hashFunction, $key);
    }

    /**
     * @return HashSet
     */
    private function createSet()
    {
        return new self(null, $this->hashFunction);
    }

    /**
     * @param HashSet $set
     */
    private function assertCompatible(HashSet $set)
    {
        if ($set->hashFunction != $this->hashFunction) {
            throw new InvalidArgumentException('The given set does not use the same hashing algorithm.');
        }
    }

    private $typeCheck;
    private $hashFunction;
    private $elements;
    private $index;
}
