<?php
namespace Icecave\Collections;

use Countable;
use Icecave\Collections\Iterator\Traits;
use Icecave\Parity\Comparator\DeepComparator;
use Icecave\Parity\Comparator\ObjectIdentityComparator;
use Icecave\Parity\Comparator\StrictPhpComparator;
use Icecave\Parity\Exception\NotComparableException;
use Icecave\Repr\Repr;
use InvalidArgumentException;
use IteratorAggregate;
use Serializable;

/**
 * An iterable collection with unique elements.
 */
class Set implements SetInterface, IteratorAggregate, Serializable
{
    /**
     * @param mixed<mixed>|null $elements   An iterable type containing the elements to include in this set, or null to create an empty set.
     * @param callable|null     $comparator The function to use for comparing elements, or null to use the default.
     */
    public function __construct($elements = null, $comparator = null)
    {
        if (null === $comparator) {
            $comparator = new ObjectIdentityComparator(
                new DeepComparator(
                    new StrictPhpComparator
                )
            );
        }

        $this->comparator = $comparator;
        $this->elements = new Vector;

        if (null !== $elements) {
            $this->addMany($elements);
        }
    }

    public function __clone()
    {
        $this->elements = clone $this->elements;
    }

    /**
     * Create a Set.
     *
     * @param mixed $element,... Elements to include in the collection.
     *
     * @return Set
     */
    public static function create()
    {
        return new static(func_get_args());
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    /**
     * Fetch the number of elements in the collection.
     *
     * @see Set::isEmpty()
     *
     * @return integer The number of elements in the collection.
     */
    public function size()
    {
        return $this->elements->size();
    }

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty()
    {
        return $this->elements->isEmpty();
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
            return '<Set 0>';
        } elseif ($this->size() > 3) {
            $format = '<Set %d [%s, ...]>';
        } else {
            $format = '<Set %d [%s]>';
        }

        return sprintf(
            $format,
            $this->size(),
            implode(
                ', ',
                $this
                    ->elements
                    ->slice(0, 3)
                    ->map('Icecave\Repr\Repr::repr')
                    ->elements()
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
        $this->elements->clear();
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
        return $this->elements->elements();
    }

    /**
     * Check if the collection contains an element with the given value.
     *
     * @param mixed $element The value to check.
     *
     * @return boolean True if the collection contains $element; otherwise, false.
     */
    public function contains($element)
    {
        return null !== $this->binarySearch($element);
    }

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     *
     * The predicate must be a callable with the following signature:
     *  function (mixed $element) { return $true_to_retain_element; }
     *
     * @param callable|null $predicate A predicate function used to determine which elements to include, or null to include all non-null elements.
     *
     * @return Set The filtered collection.
     */
    public function filter($predicate = null)
    {
        $result = $this->createSet();
        $result->elements = $this->elements->filter($predicate);

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
        $result = $this->createSet();
        $result->addMany(
            $this->elements->map($transform)
        );

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
        $left  = $this->createSet();
        $right = $this->createSet();

        foreach ($this->elements as $element) {
            if (call_user_func($predicate, $element)) {
                $left->elements->pushBack($element);
            } else {
                $right->elements->pushBack($element);
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
        $this->elements->each($callback);
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
        return $this->elements->all($predicate);
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
        return $this->elements->any($predicate);
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
        $this->elements->filterInPlace($predicate);
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
        $this->elements->mapInPlace($transform);
        $this->elements->sortInPlace($this->comparator);
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function count()
    {
        return $this->size();
    }

    /////////////////////////////////////////
    // Implementation of IteratorAggregate //
    /////////////////////////////////////////

    public function getIterator()
    {
        return $this->elements;
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
        return serialize(
            array(
                $this->elements(),
                $this->comparator
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
        list($elements, $comparator) = unserialize($packet);
        $this->__construct(null, $comparator);
        $this->elements->append($elements);
    }

    ////////////////////////////////////
    // Implementation of SetInterface //
    ////////////////////////////////////

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
        $elements = func_get_args();
        $default = array_shift($elements);

        return $this->cascadeIterableWithDefault($default, $elements);
    }

    /**
     * Return the first of the given elements that is contained in the set.
     *
     * Behaves as per {@see Set::cascade()} except that the elements are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed<mixed> $elements The list of elements.
     *
     * @return mixed                         The first of the given elements that is contained in the set.
     * @throws Exception\UnknownKeyException if none of the elements exist.
     */
    public function cascadeIterable($elements)
    {
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
     * Behaves as per {@see Set::cascadeDefault()} except that the elements are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed        $default  The default value to return if no such elements exist.
     * @param mixed<mixed> $elements The list of elements.
     *
     * @return mixed The first of the given elements that is contained in the set, or $default if none are found.
     */
    public function cascadeIterableWithDefault($default, $elements)
    {
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
        $insertIndex = null;

        if (null !== $this->binarySearch($element, 0, $insertIndex)) {
            return false;
        }

        $this->elements->insert($insertIndex, $element);

        return true;
    }

    /**
     * Add multiple elements to the set.
     *
     * @see Set::unionInPlace() may be faster when adding all elements from a another set.
     *
     * @param mixed<mixed> $elements The elements to add.
     */
    public function addMany($elements)
    {
        foreach ($elements as $element) {
            $this->add($element);
        }
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
        $index = $this->binarySearch($element);

        if (null === $index) {
            return false;
        }

        $this->elements->remove($index);

        return true;
    }

    /**
     * Remove multiple elements from the set.
     *
     * @see Set::diffInPlace() may be faster when removing all elements from a another set.
     *
     * @param mixed<mixed> $elements The elements to remote.
     */
    public function removeMany($elements)
    {
        foreach ($elements as $element) {
            $this->remove($element);
        }
    }

    /**
     * Remove and return an element from the set.
     *
     * There is no guarantee as to which element will be returned.
     *
     * @return mixed                              The element.
     * @throws Exception\EmptyCollectionException if the collection is empty.
     */
    public function pop()
    {
        return $this->elements->popBack();
    }

    /**
     * Check if this set is equal to another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains the same elements as $set; otherwise false.
     */
    public function isEqualSet(SetInterface $set)
    {
        $this->assertCompatible($set);

        if ($this->size() !== $set->size()) {
            return false;
        } elseif ($this->isEmpty()) {
            return true;
        }

        // All elements in $set are greater than all elements in $this ...
        if ($this->compareElements($this->elements->back(), $set->elements->front()) < 0) {
            return false;
        // All elements in $this are greater than all elements in $set ...
        } elseif ($this->compareElements($set->elements->back(), $this->elements->front()) < 0) {
            return false;
        }

        foreach ($this->elements as $index => $element) {
            if (0 !== $this->compareElements($element, $set->elements[$index])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if this set is a superset of another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains all of the elements in $set; otherwise, false.
     */
    public function isSuperSet(SetInterface $set)
    {
        $this->assertCompatible($set);

        // Everything is a super-set of the empty set ...
        if ($set->isEmpty()) {
            return true;
        // $this cannot be a superset if it has less elements ...
        } elseif ($this->size() < $set->size()) {
            return false;
        // $this can not be a superset if its first element is greater than the first element of $set ...
        } elseif ($this->compareElements($this->elements->front(), $set->elements->front()) > 0) {
            return false;
        // $this can not be a superset if its last element is less than the last element of $set ...
        } elseif ($this->compareElements($this->elements->back(), $set->elements->back()) < 0) {
            return false;
        }

        $lhsIndex = 0;
        $rhsIndex = 0;

        while (true) {
            $cmp = $this->compareElements(
                $this->elements[$lhsIndex++],
                $set->elements[$rhsIndex]
            );

            if ($cmp > 0) {
                return false;
            } elseif (0 === $cmp && ++$rhsIndex === $set->size()) {
                break;
            }
        };

        return true;
    }

    /**
     * Check if this set is a subset of another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains only elements present in $set; otherwise, false.
     */
    public function isSubSet(SetInterface $set)
    {
        $this->assertCompatible($set);

        return $set->isSuperSet($this);
    }

    /**
     * Check if this set is a proper superset of another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains all of elements in $set, but is not equal to $set; otherwise, false.
     */
    public function isProperSuperSet(SetInterface $set)
    {
        $this->assertCompatible($set);

        // Everything is a super-set of the empty set ...
        if (!$this->isEmpty() && $set->isEmpty()) {
            return true;
        // $this cannot be a superset if it has less or same number of elements ...
        } elseif ($this->size() <= $set->size()) {
            return false;
        } else {
            return $this->isSuperSet($set);
        }
    }

    /**
     * Check if this set is a proper subset of another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains only elements present in $set, but is not equal to $set; otherwise, false.
     */
    public function isProperSubSet(SetInterface $set)
    {
        $this->assertCompatible($set);

        return $set->isProperSuperSet($this);
    }

    /**
     * Check if this set is intersecting another.
     *
     * @param SetInterface $set The set to compare against.
     *
     * @return boolean True if this set contains one or more elements present in $set; otherwise false.
     */
    public function isIntersecting(SetInterface $set)
    {
        $this->assertCompatible($set);

        // Nothing intersects with the empty set ...
        if ($this->isEmpty() || $set->isEmpty()) {
            return false;
        }

        $cmp = $this->compareElements(
            $this->elements->back(),
            $set->elements->front()
        );

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            return false;
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            return true;
        }

        $cmp = $this->compareElements(
            $set->elements->back(),
            $this->elements->front()
        );

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            return false;
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            return true;
        }

        $lhsIndex = 0;
        $rhsIndex = 0;

        while ($lhsIndex !== $this->size() && $rhsIndex !== $set->size()) {
            $cmp = $this->compareElements(
                $this->elements[$lhsIndex],
                $set->elements[$rhsIndex]
            );

            if ($cmp < 0) {
                ++$lhsIndex;
            } elseif ($cmp > 0) {
                ++$rhsIndex;
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Compute the union of this set and another.
     *
     * @param SetInterface $set The second set.
     *
     * @return Set A set containing all elements of $this and $elements.
     */
    public function union(SetInterface $set)
    {
        $this->assertCompatible($set);

        //
        // Union with an empty set is always the non-empty set ...
        //
        if ($this->isEmpty()) {
            return clone $set;
        } elseif ($set->isEmpty()) {
            return clone $this;
        }

        $result = $this->createSet();
        $result->elements->reserve(max($this->size(), $set->size()));

        //
        // Compare tail/head ...
        //
        $cmp = $this->compareElements($this->elements->back(), $set->elements->front());

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            $result->elements->append($this->elements);
            $result->elements->append($set->elements);

            return $result;
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            $result->elements->append($this->elements);
            $result->elements->append($set->elements->slice(1));

            return $result;
        }

        //
        // Compare head/tail ...
        //
        $cmp = $this->compareElements($set->elements->back(), $this->elements->front());

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            $result->elements->append($set->elements);
            $result->elements->append($this->elements);

            return $result;
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            $result->elements->append($set->elements);
            $result->elements->append($this->elements->slice(1));

            return $result;
        }

        //
        // Merge both sides ...
        //
        $lhsIndex = 0;
        $rhsIndex = 0;

        while (true) {
            $cmp = $this->compareElements(
                $this->elements[$lhsIndex],
                $set->elements[$rhsIndex]
            );

            // Element from $this is less, add it next ..
            if ($cmp < 0) {
                $result->elements->pushBack($this->elements[$lhsIndex]);
                ++$lhsIndex;

            // Element from $set is less, add it next ..
            } elseif ($cmp > 0) {
                $result->elements->pushBack($set->elements[$rhsIndex]);
                ++$rhsIndex;

            // Elements are equivalent, add to the result and advance index for both sides ...
            } else {
                $result->elements->pushBack($this->elements[$lhsIndex]);
                ++$lhsIndex;
                ++$rhsIndex;
            }

            // Reached the end of $this first, append the rest of $set ...
            if ($lhsIndex === $this->size()) {
                if ($rhsIndex !== $set->size()) {
                    $result->elements->append($set->elements->slice($rhsIndex));
                }
                break;
            // Reached the end of $set first, append the rest of $this ...
            } elseif ($rhsIndex === $set->size()) {
                $result->elements->append($this->elements->slice($lhsIndex));
                break;
            }
        }

        return $result;
    }

    /**
     * Compute the union of this set and another, in place.
     *
     * @param SetInterface $set The second set.
     */
    public function unionInPlace(SetInterface $set)
    {
        $this->assertCompatible($set);

        //
        // Union with an empty set is always the non-empty set ...
        //
        if ($this->isEmpty()) {
            $this->elements->append($set->elements);

            return;
        } elseif ($set->isEmpty()) {
            return;
        }

        $this->elements->reserve($set->size());

        //
        // Compare tail/head ...
        //
        $cmp = $this->compareElements($this->elements->back(), $set->elements->front());

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            $this->elements->append($set->elements);

            return;
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            $this->elements->append($set->elements->slice(1));

            return;
        }

        //
        // Compare head/tail ...
        //
        $cmp = $this->compareElements($set->elements->back(), $this->elements->front());

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            $this->elements->insertMany(0, $set->elements);

            return;
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            $this->elements->insertMany(0, $set->elements->range(0, -1));

            return;
        }

        //
        // Merge elements from $set into $this ...
        //

        $lhsIndex = 0;
        $rhsIndex = 0;

        while (true) {
            $cmp = $this->compareElements(
                $this->elements[$lhsIndex],
                $set->elements[$rhsIndex]
            );

            // Element from $set is less, add it next ..
            if ($cmp > 0) {
                $this->elements->insert($lhsIndex, $set->elements[$rhsIndex]);
                ++$rhsIndex;

            // Elements are equivalent ...
            } elseif (0 === $cmp) {
                ++$rhsIndex;
            }

            if (++$lhsIndex === $this->size()) {
                if ($rhsIndex !== $set->size()) {
                    $this->elements->append($set->elements->slice($rhsIndex));
                }
                break;
            } elseif ($rhsIndex === $set->size()) {
                break;
            }
        }
    }

    /**
     * Compute the intersection of this set and another.
     *
     * @param SetInterface $set The second set.
     *
     * @return Set A set containing only the elements present in $this and $elements.
     */
    public function intersect(SetInterface $set)
    {
        $this->assertCompatible($set);

        //
        // Intersection with empty set is always empty ..
        //
        if ($this->isEmpty() || $set->isEmpty()) {
            return $this->createSet();
        }

        //
        // Compare tail/head ...
        //
        $cmp = $this->compareElements($this->elements->back(), $set->elements->front());

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            return $this->createSet();
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            return $this->createSet(
                array($this->elements->back())
            );
        }

        //
        // Compare head/tail ...
        //
        $cmp = $this->compareElements($set->elements->back(), $this->elements->front());

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            return $this->createSet();
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            return $this->createSet(
                array($this->elements->front())
            );
        }

        //
        // Build intersection from both sides ...
        //
        $lhsIndex = 0;
        $rhsIndex = 0;

        $result = $this->createSet();

        while ($lhsIndex !== $this->size() && $rhsIndex !== $set->size()) {
            $cmp = $this->compareElements($this->elements[$lhsIndex], $set->elements[$rhsIndex]);

            if ($cmp < 0) {
                ++$lhsIndex;
            } elseif ($cmp > 0) {
                ++$rhsIndex;
            } else {
                $result->elements->pushBack($this->elements[$lhsIndex]);
                ++$lhsIndex;
                ++$rhsIndex;
            }
        }

        return $result;
    }

    /**
     * Compute the intersection of this set and another, in place.
     *
     * @param SetInterface $set The second set.
     */
    public function intersectInPlace(SetInterface $set)
    {
        $this->assertCompatible($set);

        //
        // Intersection with empty set is always empty set ...
        //
        if ($this->isEmpty()) {
            return;
        } elseif ($set->isEmpty()) {
            $this->clear();

            return;
        }

        //
        // Compare tail/head ...
        //
        $cmp = $this->compareElements($this->elements->back(), $set->elements->front());

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            $this->clear();

            return;
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            $this->elements->popfront();

            return;
        }

        //
        // Compare head/tail ...
        //
        $cmp = $this->compareElements($set->elements->back(), $this->elements->front());

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            $this->clear();

            return;
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            $this->elements->popBack();

            return;
        }

        //
        // Remove elements not in $set ...
        //
        $lhsIndex = $this->size() - 1;
        $rhsIndex = $set->size() - 1;

        while ($lhsIndex >= 0 && $rhsIndex >= 0) {
            $cmp = $this->compareElements($this->elements[$lhsIndex], $set->elements[$rhsIndex]);

            if ($cmp < 0) {
                --$rhsIndex;
            } elseif ($cmp > 0) {
                $this->elements->remove($lhsIndex--);
            } else {
                --$lhsIndex;
                --$rhsIndex;
            }
        }

        // Remove any remaining elements ...
        if ($lhsIndex >= 0) {
            $this->elements->removeMany(0, $lhsIndex + 1);
        }
    }

    /**
     * Compute the difference (or complement) of this set and another.
     *
     * @param SetInterface $set The second set.
     *
     * @return Set A set containing only the elements present in $this, but not $elements.
     */
    public function diff(SetInterface $set)
    {
        $this->assertCompatible($set);

        //
        // Empty set produces empty set ...
        //
        if ($this->isEmpty()) {
            return $this->createSet();
        }

        //
        // Diff to empty set produces $this ...
        //
        if ($set->isEmpty()) {
            return clone $this;
        }

        //
        // Compare tail/head ...
        //
        $cmp = $this->compareElements($this->elements->back(), $set->elements->front());

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            return clone $this;
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            $result = clone $this;
            $result->elements->popBack();

            return $result;
        }

        //
        // Compare head/tail ...
        //
        $cmp = $this->compareElements($set->elements->back(), $this->elements->front());

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            return clone $this;
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            $result = clone $this;
            $result->elements->popFront();

            return $result;
        }

        //
        // Build diff from both sides ...
        //
        $lhsIndex = 0;
        $rhsIndex = 0;

        $result = $this->createSet();
        $result->elements->reserve($this->size() - $set->size());

         while (true) {
            $cmp = $this->compareElements($this->elements[$lhsIndex], $set->elements[$rhsIndex]);

            if ($cmp < 0) {
                $result->elements->pushBack($this->elements[$lhsIndex]);
                ++$lhsIndex;
            } elseif ($cmp > 0) {
                ++$rhsIndex;
            } else {
                ++$lhsIndex;
                ++$rhsIndex;
            }

            if ($rhsIndex === $set->size()) {
                if ($lhsIndex !== $this->size()) {
                    $result->elements->append($this->elements->slice($lhsIndex));
                }
                break;
            } elseif ($lhsIndex === $this->size()) {
                break;
            }
        }

        return $result;
    }

    /**
     * Compute the difference (or complement) of this set and another, in place.
     *
     * @param SetInterface $set The second set.
     */
    public function diffInPlace(SetInterface $set)
    {
        $this->assertCompatible($set);

        //
        // Empty set produces empty set ...
        // Diff to empty set produces $this ...
        //
        if ($this->isEmpty() || $set->isEmpty()) {
            return;
        }

        //
        // Compare tail/head ...
        //
        $cmp = $this->compareElements($this->elements->back(), $set->elements->front());

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            return;
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            $this->elements->popBack();

            return;
        }

        //
        // Compare head/tail ...
        //
        $cmp = $this->compareElements($set->elements->back(), $this->elements->front());

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            return;
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            $this->elements->popFront();

            return;
        }

        //
        // Remove elements that are in $set ...
        //
        $lhsIndex = $this->size() - 1;
        $rhsIndex = $set->size() - 1;

        while ($lhsIndex >= 0 && $rhsIndex >= 0) {
            $cmp = $this->compareElements($this->elements[$lhsIndex], $set->elements[$rhsIndex]);

            if ($cmp < 0) {
                --$rhsIndex;
            } elseif ($cmp > 0) {
                --$lhsIndex;
            } else {
                $this->elements->remove($lhsIndex);
                --$lhsIndex;
                --$rhsIndex;
            }
        }
    }

    /**
     * Compute the symmetric difference (or complement) of this set and another.
     *
     * The symmetric difference is the set of elements which are in either of the sets and not in their intersection.
     *
     * @param SetInterface $set The second set.
     *
     * @return Set A set containing only the elements present in $this, or $elements, but not both.
     */
    public function symmetricDiff(SetInterface $set)
    {
        $this->assertCompatible($set);

        //
        // Symmetric diff with an empty set is always the non-empty set ...
        //
        if ($this->isEmpty()) {
            return clone $set;
        } elseif ($set->isEmpty()) {
            return clone $this;
        }

        $result = $this->createSet();

        //
        // Compare tail/head ...
        //
        $cmp = $this->compareElements($this->elements->back(), $set->elements->front());

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            $result->elements->append($this->elements);
            $result->elements->append($set->elements);

            return $result;
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            $result->elements->append($this->elements->range(0, -1));
            $result->elements->append($set->elements->slice(1));

            return $result;
        }

        //
        // Compare head/tail ...
        //
        $cmp = $this->compareElements($set->elements->back(), $this->elements->front());

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            $result->elements->append($set->elements);
            $result->elements->append($this->elements);

            return $result;
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            $result->elements->append($set->elements->range(0, -1));
            $result->elements->append($this->elements->slice(1));

            return $result;
        }

        //
        // Build symmetric diff from both sides ...
        //
        $lhsIndex = 0;
        $rhsIndex = 0;

        while (true) {
            $cmp = $this->compareElements($this->elements[$lhsIndex], $set->elements[$rhsIndex]);

            // Element from $this is less, add it next ..
            if ($cmp < 0) {
                $result->elements->pushBack($this->elements[$lhsIndex]);
                ++$lhsIndex;

            // Element from $set is less, add it next ..
            } elseif ($cmp > 0) {
                $result->elements->pushBack($set->elements[$rhsIndex]);
                ++$rhsIndex;

            // Elements are equivalent, add to the result and advance index for both sides ...
            } else {
                ++$lhsIndex;
                ++$rhsIndex;
            }

            // Reached the end of $this first, append the rest of $set ...
            if ($lhsIndex === $this->size()) {
                if ($rhsIndex !== $set->size()) {
                    $result->elements->append($set->elements->slice($rhsIndex));
                }
                break;
            // Reached the end of $set first, append the rest of $this ...
            } elseif ($rhsIndex === $set->size()) {
                $result->elements->append($this->elements->slice($lhsIndex));
                break;
            }
        }

        return $result;
    }

    /**
     * Compute the symmetric difference (or complement) of this set and another, in place.
     *
     * The symmetric difference is the set of elements which are in either of the sets and not in their intersection.
     *
     * @param SetInterface $set The second set.
     */
    public function symmetricDiffInPlace(SetInterface $set)
    {
        $this->assertCompatible($set);

        //
        // Symmetric diff with an empty set is always the non-empty set ...
        //
        if ($this->isEmpty()) {
            $this->elements->append($set->elements);

            return;
        } elseif ($set->isEmpty()) {
            return;
        }

        //
        // Compare tail/head ...
        //
        $cmp = $this->compareElements($this->elements->back(), $set->elements->front());

        // All elements in $set are greater than all elements in $this ...
        if ($cmp < 0) {
            $this->elements->append($set->elements);

            return;
        // Tail of $this equal to head of $set ...
        } elseif (0 === $cmp) {
            $this->elements->popBack();
            $this->elements->append($set->elements->slice(1));

            return;
        }

        //
        // Compare head/tail ...
        //
        $cmp = $this->compareElements($set->elements->back(), $this->elements->front());

        // All elements in $this are greater than all elements in $set ...
        if ($cmp < 0) {
            $this->elements->insertMany(0, $set->elements);

            return;
        // Tail of $set equal to head of $this ...
        } elseif (0 === $cmp) {
            $this->elements->popFront();
            $this->elements->insertMany(0, $set->elements->range(0, -1));

            return;
        }

        //
        // Add/remove elements to build symmetric diff in place ...
        //
        $lhsIndex = 0;
        $rhsIndex = 0;

        while (true) {
            $cmp = $this->compareElements($this->elements[$lhsIndex], $set->elements[$rhsIndex]);

            // Element from $this is less ...
            if ($cmp < 0) {
                ++$lhsIndex;

            // Element from $set is less ...
            } elseif ($cmp > 0) {
                $this->elements->insert($lhsIndex, $set->elements[$rhsIndex]);
                ++$lhsIndex;
                ++$rhsIndex;

            // Elements are equivalent, add to the result and advance index for both sides ...
            } else {
                $this->elements->remove($lhsIndex);
                ++$rhsIndex;
            }

            // Reached the end of $this first, append the rest of $set ...
            if ($lhsIndex === $this->size()) {
                if ($rhsIndex !== $set->size()) {
                    $this->elements->append($set->elements->slice($rhsIndex));
                }
                break;
            // Reached the end of $set first, append the rest of $this ...
            } elseif ($rhsIndex === $set->size()) {
                break;
            }
        }
    }

    ///////////////////////////////////////////
    // Implementation of ComparableInterface //
    ///////////////////////////////////////////

    /**
     * Compare this object with another value, yielding a result according to the following table:
     *
     * +--------------------+---------------+
     * | Condition          | Result        |
     * +--------------------+---------------+
     * | $this == $value    | $result === 0 |
     * | $this < $value     | $result < 0   |
     * | $this > $value     | $result > 0   |
     * +--------------------+---------------+
     *
     * @param mixed $value The value to compare.
     *
     * @return integer                                         The result of the comparison.
     * @throws Icecave\Parity\Exception\NotComparableException Indicates that the implementation does not know how to compare $this to $value.
     */
    public function compare($value)
    {
        if (!$this->canCompare($value)) {
            throw new NotComparableException($this, $value);
        }

        return Collection::compare($this->elements, $value->elements);
    }

    /////////////////////////////////////////////////////
    // Implementation of RestrictedComparableInterface //
    /////////////////////////////////////////////////////

    /**
     * Check if $this is able to be compared to another value.
     *
     * A return value of false indicates that calling $this->compare($value)
     * will throw an exception.
     *
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this can be compared to $value.
     */
    public function canCompare($value)
    {
        return is_object($value)
            && __CLASS__ === get_class($value)
            && $this->comparator == $value->comparator;
    }

    ///////////////////////////////////////////////////
    // Implementation of ExtendedComparableInterface //
    ///////////////////////////////////////////////////

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this == $value.
     */
    public function isEqualTo($value)
    {
        return $this->compare($value) === 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this != $value.
     */
    public function isNotEqualTo($value)
    {
        return $this->compare($value) !== 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this < $value.
     */
    public function isLessThan($value)
    {
        return $this->compare($value) < 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this > $value.
     */
    public function isGreaterThan($value)
    {
        return $this->compare($value) > 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this <= $value.
     */
    public function isLessThanOrEqualTo($value)
    {
        return $this->compare($value) <= 0;
    }

    /**
     * @param mixed $value The value to compare.
     *
     * @return boolean True if $this >= $value.
     */
    public function isGreaterThanOrEqualTo($value)
    {
        return $this->compare($value) >= 0;
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    /**
     * @param mixed $lhs
     * @param mixed $rhs
     *
     * @return integer
     */
    private function compareElements($lhs, $rhs)
    {
        return call_user_func($this->comparator, $lhs, $rhs);
    }

    /**
     * @param mixed<mixed>|null $elements
     *
     * @return Set
     */
    private function createSet($elements = null)
    {
        return new self($elements, $this->comparator);
    }

    /**
     * @param SetInterface $set
     */
    private function assertCompatible(SetInterface $set)
    {
        $className = get_class($this);

        if (!$set instanceof $className) {
            throw new InvalidArgumentException('The given set is not an instance of ' . $className . '.');
        } elseif ($set->comparator != $this->comparator) {
            throw new InvalidArgumentException('The given set does not use the same comparator.');
        }
    }

    /**
     * @param mixed        $element
     * @param integer      $begin
     * @param integer|null &$insertIndex
     *
     * @return integer|null
     */
    private function binarySearch($element, $begin = 0, &$insertIndex = null)
    {
        return Collection::binarySearch(
            $this->elements,
            $element,
            $this->comparator,
            $begin,
            null,
            $insertIndex
        );
    }

    private $comparator;
    private $elements;
}
