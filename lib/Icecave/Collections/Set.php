<?php
namespace Icecave\Collections;

use Countable;
use Icecave\Collections\Iterator\SetIterator;
use Icecave\Collections\TypeCheck\Typhoon;
use Icecave\Repr\Repr;
use Iterator;

class Set implements MutableIterableInterface, Countable, Iterator
{
    /**
     * @param mixed<mixed>|null $collection An iterable type containing the elements to include in this set, or null to create an empty set.
     * @param callable|null $hashFunction The function to use for generating hashes of elements, or null to use the default.
     */
    public function __construct($collection = null, $hashFunction = null)
    {
        $this->typeCheck = Typhoon::get(__CLASS__, func_get_args());

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
     * @see Set::isEmpty()
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
        $this->typeCheck->__toString(func_get_args());

        if ($this->isEmpty()) {
            return '<Set 0>';
        }

        $elements = array();
        $index = 0;
        foreach ($this->elements as $element) {
            if ($index++ === 3) {
                break;
            }

            $elements[] = Repr::repr($element);
        }

        if ($this->size() > 3) {
            $format = '<Set %d [%s, ...]>';
        } else {
            $format = '<Set %d [%s]>';
        }

        return sprintf(
            $format,
            $this->size(),
            implode(', ', $elements)
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

        $hash = $this->generateHash($value);
        return array_key_exists($hash, $this->elements);
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
    public function filtered($predicate = null)
    {
        $this->typeCheck->filtered(func_get_args());

        if (null === $predicate) {
            $predicate = function ($element) {
                return null !== $element;
            };
        }

        $result = new static(null, $this->hashFunction);

        foreach ($this->elements as $element) {
            if (call_user_func($predicate, $element)) {
                $result->add($element);
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

        $result = new static(null, $this->hashFunction);

        foreach ($this->elements as $element) {
            $result->add(call_user_func($transform, $element));
        }

        return $result;
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
    public function filter($predicate = null)
    {
        $this->typeCheck->filter(func_get_args());

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
    public function apply($transform)
    {
        $this->typeCheck->apply(func_get_args());

        $result = $this->map($transform);
        $this->elements = $result->elements;
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function count()
    {
        $this->typeCheck->count(func_get_args());

        return $this->size();
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function current()
    {
        $this->typeCheck->current(func_get_args());

        return current($this->elements);
    }

    public function key()
    {
        $this->typeCheck->key(func_get_args());

        return $this->current();
    }

    public function next()
    {
        $this->typeCheck->next(func_get_args());

        next($this->elements);
    }

    public function rewind()
    {
        $this->typeCheck->rewind(func_get_args());

        reset($this->elements);
    }

    public function valid()
    {
        $this->typeCheck->valid(func_get_args());

        return null !== key($this->elements);
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    /**
     * Return the first of the given elements that is contained in the set.
     *
     * @param mixed $element The element to search for.
     * @param mixed $additional,... Additional elements to search for.
     *
     * @return mixed The first of the given elements that is contained in the set.
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
     * @param mixed $default The default value to return if no such elements exist.
     * @param mixed $element The element to search for.
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
     * Behaves as per {@see Set::cascade()} except that the elements are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed<mixed> $elements The list of elements.
     *
     * @return mixed The first of the given elements that is contained in the set.
     * @throws Exception\UnknownKeyException if none of the elements exist.
     */
    public function cascadeIterable($elements)
    {
        $this->typeCheck->cascadeIterable(func_get_args());

        foreach ($elements as $element) {
            $hash = $this->generateHash($element);
            if (array_key_exists($hash, $this->elements)) {
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
     * @param mixed $default The default value to return if no such elements exist.
     * @param mixed<mixed> $elements The list of elements.
     *
     * @return mixed The first of the given elements that is contained in the set, or $default if none are found.
     */
    public function cascadeIterableWithDefault($default, $elements)
    {
        $this->typeCheck->cascadeIterableWithDefault(func_get_args());

        foreach ($elements as $element) {
            $hash = $this->generateHash($element);
            if (array_key_exists($hash, $this->elements)) {
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

        $hash = $this->generateHash($element);
        if (array_key_exists($hash, $this->elements)) {
            return false;
        }

        $this->elements[$hash] = $element;
        return true;
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

        $hash = $this->generateHash($element);
        if (array_key_exists($hash, $this->elements)) {
            unset($this->elements[$hash]);
            return true;
        }
        return false;
    }

    /**
     * Compute the union of this set and another.
     *
     * @param mixed $elements The elements of the second set.
     *
     * @return Set A set containing all elements of $this and $elements.
     */
    public function union($elements)
    {
        $this->typeCheck->union(func_get_args());

        $result = clone $this;
        $result->unionInPlace($elements);
        return $result;
    }

    /**
     * Compute the union of this set and another, in place.
     *
     * @param mixed $elements The elements of the second set.
     */
    public function unionInPlace($elements)
    {
        $this->typeCheck->unionInPlace(func_get_args());

        if ($elements instanceof self && $this->hashFunction == $elements->hashFunction) {
            $this->elements += $elements->elements;
        } else {
            foreach ($elements as $element) {
                $this->add($element);
            }
        }
    }

    /**
     * Compute the intersection of this set and another.
     *
     * @param mixed $elements The elements of the second set.
     *
     * @return Set A set containing only the elements present in $this and $elements.
     */
    public function intersect($elements)
    {
        $this->typeCheck->intersect(func_get_args());

        $result = clone $this;
        $result->intersectInPlace($elements);
        return $result;
    }

    /**
     * Compute the intersection of this set and another, in place.
     *
     * @param mixed $elements The elements of the second set.
     */
    public function intersectInPlace($elements)
    {
        $this->typeCheck->intersectInPlace(func_get_args());

        if ($elements instanceof self && $this->hashFunction == $elements->hashFunction) {
            $this->elements = array_intersect_assoc($this->elements, $elements->elements);
        } else {
            $newElements = array();

            foreach ($elements as $element) {
                $hash = $this->generateHash($element);
                if (array_key_exists($hash, $this->elements)) {
                    $newElements[$hash] = $element;
                }
            }

            $this->elements = $newElements;
        }
    }

    /**
     * Compute the complement (or difference) of this set and another.
     *
     * @param mixed $elements The elements of the second set.
     *
     * @return Set A set containing only the elements present in $this, but not $elements.
     */
    public function complement($elements)
    {
        $this->typeCheck->complement(func_get_args());

        $result = clone $this;
        $result->complementInPlace($elements);
        return $result;
    }

    /**
     * Compute the complement (or difference) of this set and another, in place.
     *
     * @param mixed $elements The elements of the second set.
     */
    public function complementInPlace($elements)
    {
        $this->typeCheck->complementInPlace(func_get_args());

        if ($elements instanceof self && $this->hashFunction == $elements->hashFunction) {
            $this->elements = array_diff_assoc($this->elements, $elements->elements);
        } else {
            foreach ($elements as $element) {
                $hash = $this->generateHash($element);
                unset($this->elements[$hash]);
            }
        }
    }

    /**
     * @param mixed $key
     *
     * @return integer|string
     */
    protected function generateHash($key)
    {
        return call_user_func($this->hashFunction, $key);
    }

    private $typeCheck;
    private $hashFunction;
    private $elements;
}
