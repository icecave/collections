<?php
namespace Icecave\Collections;

use Countable;
use Icecave\Collections\Iterator\SetIterator;
use Icecave\Repr\Repr;
use Iterator;

class Set implements MutableIterableInterface, Countable, Iterator
{
    /**
     * @param traversable|null $collection An iterable type containing the elements to include in this set, or null to create an empty set.
     * @param callable|null $hashFunction The function to use for generating hashes of elements, or null to use the default.
     */
    public function __construct($collection = null, $hashFunction = null)
    {
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
        return count($this->elements);
    }

    /**
     * Check if the collection is empty.
     *
     * @return boolean True if the collection is empty; otherwise, false.
     */
    public function isEmpty()
    {
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
        $result = $this->map($transform);
        $this->elements = $result->elements;
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function count()
    {
        return $this->size();
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function current()
    {
        return current($this->elements);
    }

    public function key()
    {
        return $this->current();
    }

    public function next()
    {
        next($this->elements);
    }

    public function rewind()
    {
        reset($this->elements);
    }

    public function valid()
    {
        return null !== key($this->elements);
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    /**
     * Return the first of the given elements that is contained in the set.
     *
     * @param mixed,... $element The elements to search for.
     *
     * @return mixed The first of the given elements that is contained in the set.
     * @throws Exception\UnknownKeyException if none of the elements exist.
     */
    public function cascade($element)
    {
        return $this->cascadeIterable(func_get_args());
    }

    /**
     * Return the first of the given elements that is contained in the set, or a default if none are found.
     *
     * @param mixed,... $element The elements to search for.
     * @param mixed $default The default value to return if no such elements exist.
     *
     * @return mixed The first of the given elements that is contained in the set, or $default if none are found.
     */
    public function cascadeWithDefault($element, $default)
    {
        $elements = func_get_args();
        $default = array_pop($elements);

        return $this->cascadeIterableWithDefault($elements, $default);
    }

    /**
     * Return the first of the given elements that is contained in the set.
     *
     * Behaves as per {@see Set::cascade()} except that the elements are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param traversable $elements The list of elements.
     *
     * @return mixed The first of the given elements that is contained in the set.
     * @throws Exception\UnknownKeyException if none of the elements exist.
     */
    public function cascadeIterable($elements)
    {
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
     * @param traversable $elements The list of elements.
     * @param mixed $default The default value to return if no such elements exist.
     *
     * @return mixed The first of the given elements that is contained in the set, or $default if none are found.
     */
    public function cascadeIterableWithDefault($elements, $default = null)
    {
        foreach ($elements as $element) {
            $hash = $this->generateHash($element);
            if (array_key_exists($hash, $this->elements)) {
                return $element;
            }
        }

        return $default;
    }

    public function add($element)
    {
        $hash = $this->generateHash($element);
        if (array_key_exists($hash, $this->elements)) {
            return false;
        }

        $this->elements[$hash] = $element;
        return true;
    }

    public function remove($element)
    {
        $hash = $this->generateHash($element);
        if (array_key_exists($hash, $this->elements)) {
            unset($this->elements[$hash]);
            return true;
        }
        return false;
    }

    public function union($elements)
    {
        $result = clone $this;
        $result->unionInPlace($elements);
        return $result;
    }

    public function unionInPlace($elements)
    {
        if ($elements instanceof self && $this->hashFunction == $elements->hashFunction) {
            $this->elements += $elements->elements;
        } else {
            foreach ($elements as $element) {
                $this->add($element);
            }
        }
    }

    public function intersect($elements)
    {
        $result = clone $this;
        $result->intersectInPlace($elements);
        return $result;
    }

    public function intersectInPlace($elements)
    {
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

    public function complement($elements)
    {
        $result = clone $this;
        $result->complementInPlace($elements);
        return $result;
    }

    public function complementInPlace($elements)
    {
        if ($elements instanceof self && $this->hashFunction == $elements->hashFunction) {
            $this->elements = array_diff_assoc($this->elements, $elements->elements);
        } else {
            foreach ($elements as $element) {
                $hash = $this->generateHash($element);
                unset($this->elements[$hash]);
            }
        }
    }

    protected function generateHash($key)
    {
        return call_user_func($this->hashFunction, $key);
    }

    private $hashFunction;
    private $elements;
}
