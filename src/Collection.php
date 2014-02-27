<?php
namespace Icecave\Collections;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Icecave\Collections\Iterator\Traits;
use Icecave\Collections\Iterator\TraitsProviderInterface;
use Icecave\Repr\Repr;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use SplDoublyLinkedList;
use SplFixedArray;
use SplHeap;
use SplObjectStorage;
use SplPriorityQueue;
use Traversable;

/**
 * Utility functions for working with arbitrary collection types.
 */
abstract class Collection
{
    /**
     * Check if a collection is empty.
     *
     * @param array|Traversable|CollectionInterface $collection
     *
     * @return boolean True if $collection is contains zero elements; otherwise false.
     */
    public static function isEmpty($collection)
    {
        if ($collection instanceof CollectionInterface) {
            return $collection->isEmpty();
        }

        return 0 === static::size($collection);
    }

    /**
     * Get the number of elements in a collection.
     *
     * @param array|Traversable|Countable|CollectionInterface $collection
     *
     * @return integer The number of elements in $collection
     */
    public static function size($collection)
    {
        $size = static::trySize($collection);

        if (null !== $size) {
            return $size;
        }

        $count = 0;
        foreach ($collection as $value) {
            ++$count;
        }

        return $count;
    }

    /**
     * Attempt to get the number of elements in a collection.
     *
     * @param mixed $collection
     *
     * @return integer|null The number of elements in $collection, or null if the size can not be determined without iterating the entire collection.
     */
    public static function trySize($collection)
    {
        if ($collection instanceof CollectionInterface) {
            return $collection->size();
        } elseif ($collection instanceof Countable) {
            return count($collection);
        } elseif (is_array($collection)) {
            return count($collection);
        }

        return null;
    }

    /**
     * Fetch the value associated with the given key.
     *
     * @param array|Traversable|AssociativeInterface $collection
     * @param mixed                                  $key        The key to fetch.
     *
     * @return mixed                         The associated value.
     * @throws Exception\UnknownKeyException if no such key exists.
     */
    public static function get($collection, $key)
    {
        $value = null;
        if (static::tryGet($collection, $key, $value)) {
            return $value;
        }

        throw new Exception\UnknownKeyException($key);
    }

    /**
     * Fetch the value associated with the given key if it exists.
     *
     * @param array|Traversable|AssociativeInterface $collection
     * @param mixed                                  $key        The key to fetch.
     * @param mixed                                  &$value     Assigned the value associated with $key if it exists.
     *
     * @return boolean True if $key exists and $value was populated; otherwise, false.
     */
    public static function tryGet($collection, $key, &$value)
    {
        if ($collection instanceof AssociativeInterface) {
            return $collection->tryGet($key, $value);
        } elseif (is_array($collection)) {
            if (array_key_exists($key, $collection)) {
                $value = $collection[$key];

                return true;
            }

            return false;
        }

        return static::any(
            $collection,
            function ($k, $v) use ($key, &$value) {
                if ($k === $key) {
                    $value = $v;

                    return true;
                }

                return false;
            }
        );
    }

    /**
     * Fetch the value associated with the given key, or a default value if it does not exist.
     *
     * @param array|Traversable|IterableInterface $collection
     * @param mixed                               $key        The key to fetch.
     * @param mixed                               $default    The default value to return if $key does not exist.
     *
     * @return mixed The value associated with $key, or the $default if nos such key exists.
     */
    public static function getWithDefault($collection, $key, $default = null)
    {
        $value = null;
        if (static::tryGet($collection, $key, $value)) {
            return $value;
        }

        return $default;
    }

    /**
     * Check if the collection contains an element with the given key.
     *
     * @param array|Traversable|IterableInterface $collection
     * @param mixed                               $key        The key to check.
     *
     * @return boolean True if the collection contains the given key; otherwise, false.
     */
    public static function hasKey($collection, $key)
    {
        if ($collection instanceof AssociativeInterface) {
            return $collection->hasKey($key);
        } elseif ($collection instanceof SequenceInterface) {
            return is_int($key) && $key >= 0 && $key < $collection->size();
        } elseif (is_array($collection)) {
            return array_key_exists($key, $collection);
        }

        return static::any(
            $collection,
            function ($k, $v) use ($key) {
                return $k === $key;
            }
        );
    }

    /**
     * Check if the collection contains an element with the given value.
     *
     * @param array|Traversable|IterableInterface $collection
     * @param mixed                               $value      The value to check.
     *
     * @return boolean True if the collection contains $value; otherwise, false.
     */
    public static function contains($collection, $value)
    {
        if ($collection instanceof IterableInterface) {
            return $collection->contains($value);
        } elseif (is_array($collection)) {
            return false !== array_search($value, $collection, true);
        }

        return static::any(
            $collection,
            function ($k, $v) use ($value) {
                return $v === $value;
            }
        );
    }

    /**
     * Get the keys of a collection.
     *
     * @param array|Traversable|AssociativeInterface|SequenceInterface $collection
     *
     * @return array An array containing the keys of $collection.
     */
    public static function keys($collection)
    {
        if ($collection instanceof AssociativeInterface) {
            return $collection->keys();
        } elseif ($collection instanceof SequenceInterface) {
            return range(0, $collection->size() - 1);
        } elseif (is_array($collection)) {
            return array_keys($collection);
        }

        $keys = array();
        foreach ($collection as $value) {
            $keys[] = $collection->key(); // https://bugs.php.net/bug.php?id=45684
        }

        return $keys;
    }

    /**
     * Get the values of a collection.
     *
     * @param array|Traversable|AssociativeInterface|SequenceInterface $collection
     *
     * @return array An array containing the values of $collection.
     */
    public static function values($collection)
    {
        if ($collection instanceof AssociativeInterface) {
            return $collection->values();
        } elseif ($collection instanceof SequenceInterface) {
            return $collection->elements();
        } elseif (is_array($collection)) {
            return array_values($collection);
        }

        $values = array();
        foreach ($collection as $value) {
            $values[] = $value;
        }

        return $values;
    }

    /**
     * Get the elements of a collection.
     *
     * Elements are 2-tuples of key and value (even for sequential collection types).
     *
     * @param array|Traversable|CollectionInterface $collection
     *
     * @return array An array containing the elements of $collection.
     */
    public static function elements($collection)
    {
        if ($collection instanceof AssociativeInterface) {
            return $collection->elements();
        }

        $elements = array();

        static::each(
            $collection,
            function ($key, $value) use (&$elements) {
                $elements[] = array($key, $value);
            }
        );

        return $elements;
    }

    /**
     * Produce a new collection by applying a transformation to each element.
     *
     * The transform must be a callable with the following signature:
     *  function (mixed $key, mixed $value) { return array($new_key, $new_value); }
     *
     * @param array|Traversable|IterableInterface $collection
     * @param callable                            $transform  The transform to apply to each element.
     * @param array|ArrayAccess                   &$result
     *
     * @return array|ArrayAccess Returns $result.
     */
    public static function map($collection, $transform, &$result = array())
    {
        static::each(
            $collection,
            function ($key, $value) use ($transform, &$result) {
                list($key, $value) = call_user_func($transform, $key, $value);
                $result[$key] = $value;
            }
        );

        return $result;
    }

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     *
     * The predicate must be a callable with the following signature:
     *  function (mixed $key, mixed $value) { return $true_to_retain_element; }
     *
     * @param array|Traversable|IterableInterface $collection
     * @param callable|null                       $predicate  A predicate function used to determine which elements to include, or null to include all non-null elements.
     * @param array|ArrayAccess                   &$result
     *
     * @return array|ArrayAccess Returns $result.
     */
    public static function filter($collection, $predicate, &$result = array())
    {
        static::each(
            $collection,
            function ($key, $value) use ($predicate, &$result) {
                if (call_user_func($predicate, $key, $value)) {
                    $result[$key] = $value;
                }
            }
        );

        return $result;
    }

    /**
     * Invokes the given callback on every element in the collection.
     *
     * This method behaves the same as {@see Collection::map()} except that the return value of the callback is not retained.
     *
     * @param array|Traversable|IterableInterface $collection
     * @param callable                            $callback   The callback to invoke with each element.
     */
    public static function each($collection, $callback)
    {
        static::all(
            $collection,
            function ($key, $value) use ($callback) {
                call_user_func($callback, $key, $value);

                return true;
            }
        );
    }

    /**
     * Returns true if the given predicate returns true for all elements.
     *
     * The loop is short-circuited, exiting after the first element for which the predicate returns false.
     *
     * @param array|Traversable|IterableInterface $collection
     * @param callable                            $predicate
     *
     * @return boolean True if $predicate($key, $value) returns true for all elements; otherwise, false.
     */
    public static function all($collection, $predicate)
    {
        if ($collection instanceof IterableInterface) {
            // Wrap the callback such that a sequential index is produced for the first argument ...
            if ($collection instanceof SequenceInterface) {
                $index = 0;
                $original = $predicate;
                $predicate = function ($value) use (&$index, $original) {
                    return call_user_func($original, $index++, $value);
                };
            }

            return $collection->all($predicate);
        } elseif (is_array($collection)) {
            foreach ($collection as $key => $value) {
                if (!call_user_func($predicate, $key, $value)) {
                    return false;
                }
            }
        } else {
            foreach ($collection as $value) {
                $key = $collection->key(); // https://bugs.php.net/bug.php?id=45684
                if (!call_user_func($predicate, $key, $value)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns true if the given predicate returns true for any element.
     *
     * The loop is short-circuited, exiting after the first element for which the predicate returns false.
     *
     * @param array|Traversable|IterableInterface $collection
     * @param callable                            $predicate
     *
     * @return boolean True if $predicate($key, $value) returns true for any element; otherwise, false.
     */
   public static function any($collection, $predicate)
   {
        return !static::all(
            $collection,
            function ($key, $value) use ($predicate) {
                return !call_user_func($predicate, $key, $value);
            }
        );
    }

    /**
     * Check if a collection contains sequential integer keys.
     *
     * @param array|Traversable|CollectionInterface $collection
     *
     * @return boolean True if the collection contains sequential integer keys; otherwise, false.
     */
    public static function isSequential($collection)
    {
        if ($collection instanceof CollectionInterface) {
            return $collection instanceof SequenceInterface;
        }

        $expectedKey = 0;

        return static::all(
            $collection,
            function ($key, $value) use (&$expectedKey) {
                return $key === $expectedKey++;
            }
        );
    }

    /**
     * Get an iterator for any traversable type.
     *
     * @param mixed<mixed> $collection
     *
     * @return Iterator
     * @throws InvalidArgumentException if no iterator can be produced from the given collection.
     */
    public static function getIterator($collection)
    {
        if (is_array($collection)) {
            $collection = new ArrayIterator($collection);
        } else {
            while ($collection instanceof IteratorAggregate) {
                $collection = $collection->getIterator();
            }
        }

        if (!$collection instanceof Iterator) {
            throw new InvalidArgumentException(
                'Could not produce an iterator for ' . Repr::repr($collection) . '.'
            );
        }

        return $collection;
    }

    /**
     * Add an element to a collection.
     *
     * @param MutableSequenceInterface|QueuedAccessInterface|SetInterface|ArrayAccess|array &$collection The collection to add to.
     * @param mixed                                                                         $element     The element to add.
     */
    public static function addElement(&$collection, $element)
    {
        if ($collection instanceof MutableSequenceInterface) {
            $collection->pushBack($element);
        } elseif ($collection instanceof QueuedAccessInterface) {
            $collection->push($element);
        } elseif ($collection instanceof SetInterface) {
            $collection->add($element);
        } elseif ($collection instanceof ArrayAccess) {
            $collection[] = $element;
        } else {
            $collection[] = $element;
        }
    }

    /**
     * Add elements from one collection to another.
     *
     * @param MutableSequenceInterface|QueuedAccessInterface|SetInterface|ArrayAccess|array &$collection The collection to append to.
     * @param mixed<mixed>                                                                  $elements    The elements to be appended.
     */
    public static function addElements(&$collection, $elements)
    {
        foreach ($elements as $element) {
            static::addElement($collection, $element);
        }
    }

    /**
     * Create a string by joining the elements in a collection.
     *
     * @param string        $separator   The separator string to place between elements.
     * @param mixed<mixed>  $collection  The collection to join.
     * @param string        $emptyResult The result to return when there are no elements in the collection.
     * @param callable|null $transform   The transform to apply to convert each element to a string.
     *
     * @return string A string containing each element in $collection, transformed by $transform, separated by $separator.
     */
    public static function implode(
        $separator,
        $collection,
        $emptyResult = '',
        $transform = null
    ) {
        // Create an identity transform if none is provided ...
        if (null === $transform) {
            $transform = function ($element) {
                return $element;
            };
        }

        $iterator = static::getIterator($collection);
        $iterator->rewind();

        if (!$iterator->valid()) {
            return $emptyResult;
        }

        $result = call_user_func($transform, $iterator->current());
        $iterator->next();

        while ($iterator->valid()) {
            $result .= $separator . call_user_func($transform, $iterator->current());
            $iterator->next();
        }

        return $result;
    }

    /**
     * Split a string based on a separator.
     *
     * @param string                                                                        $separator   The separator string to place between elements.
     * @param string                                                                        $string      The string to split.
     * @param integer|null                                                                  $limit       The maximum number of elements to insert into $collection, or null for unlimited.
     * @param MutableSequenceInterface|QueuedAccessInterface|SetInterface|ArrayAccess|array &$collection The collection to append to, can be any type supported by {@see Collection::addElement()}.
     * @param callable|null                                                                 $transform   The transform to apply to each element before insertion into $collection.
     * @param string|null                                                                   $encoding    The string encoding to use, or null to use the internal encoding ({@see mb_internal_encoding()}).
     *
     * @return mixed<mixed> Returns $collection.
     */
    public static function explode(
        $separator,
        $string,
        $limit = null,
        &$collection = array(),
        $transform = null,
        $encoding = null
    ) {
        // Attempt to auto-detect encoding from $string ...
        if (null === $encoding) {
            $encoding = mb_internal_encoding();
        }

        // Create an identity transform if none is provided ...
        if (null === $transform) {
            $transform = function ($element) {
                return $element;
            };
        }

        $separatorLength = mb_strlen($separator, $encoding);
        $stringLength = mb_strlen($string, $encoding);

        $count = 0;
        $begin = 0;
        $end   = 0;

        while ($end < $stringLength) {

            if ($limit !== null && ++$count >= $limit) {
                $end = $stringLength;
            } else {
                $end = mb_strpos($string, $separator, $begin, $encoding);
                if (false === $end) {
                    $end = $stringLength;
                }
            }

            $element = mb_substr(
                $string,
                $begin,
                $end - $begin,
                $encoding
            );

            static::addElement(
                $collection,
                call_user_func($transform, $element)
            );

            $begin = $end + $separatorLength;
        }

        return $collection;
    }

    /**
     * Fetch the traits for an iterator.
     *
     * @param array|Traversable|Countable|TraitsProviderInterface $iterator
     *
     * @return Traits
     */
    public static function iteratorTraits($iterator)
    {
        if ($iterator instanceof TraitsProviderInterface) {
            return $iterator->iteratorTraits();
        } elseif (is_array($iterator)) {
            return new Traits(true, true);
        } elseif ($iterator instanceof SplDoublyLinkedList) {
            return new Traits(true, true);
        } elseif ($iterator instanceof SplHeap) {
            return new Traits(true, true);
        } elseif ($iterator instanceof SplPriorityQueue) {
            return new Traits(true, true);
        } elseif ($iterator instanceof SplFixedArray) {
            return new Traits(true, true);
        } elseif ($iterator instanceof SplObjectStorage) {
            return new Traits(true, true);
        } else {
            return new Traits($iterator instanceof Countable, false);
        }
    }

    /**
     * Compare two collections.
     *
     * @param mixed<mixed> $lhs
     * @param mixed<mixed> $rhs
     * @param callable     $comparator The comparator to use for size and element comparisons.
     *
     * @return integer The result of the comparison.
     */
    public static function compare($lhs, $rhs, $comparator = 'Icecave\Parity\Parity::compare')
    {
        $lhsSize = static::trySize($lhs);
        $rhsSize = static::trySize($rhs);

        if ($lhsSize !== $rhsSize && null !== $lhsSize && null !== $rhsSize) {
            return call_user_func($comparator, $lhsSize, $rhsSize);
        }

        $lhs = static::getIterator($lhs);
        $rhs = static::getIterator($rhs);

        $lhs->rewind();
        $rhs->rewind();

        while ($lhs->valid() && $rhs->valid()) {
            $cmp = call_user_func($comparator, $lhs->current(), $rhs->current());
            if (0 !== $cmp) {
                return $cmp;
            }

            $lhs->next();
            $rhs->next();
        }

        return call_user_func($comparator, $lhs->valid(), $rhs->valid());
    }

    /**
     * Return the index of the first element in a sorted collection that is not less than the given element.
     *
     * Searches all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param array|ArrayAccess $collection The sorted collection to search.
     * @param mixed             $element    The element to search for.
     * @param callable          $comparator The comparator used to compare elements.
     * @param integer           $begin      The index at which to start the search.
     * @param integer|null      $end        The index at which to stop the search, or null to use the entire collection.
     */
    public static function lowerBound($collection, $element, $comparator, $begin = 0, $end = null)
    {
        if (null === $end) {
            $end = static::size($collection);
        }

        $count = $end - $begin;

        while ($count > 0) {
            $step = intval($count / 2);
            $pivotIndex = $begin + $step;

            if (call_user_func($comparator, $collection[$pivotIndex], $element) < 0) {
                $begin = $pivotIndex + 1;
                $count -= $step + 1;
            } else {
                $count = $step;
            }
        }

        return $begin;
    }

    /**
     * Return the index of the first element in a sorted collection that is greater than the given element.
     *
     * Searches all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param array|ArrayAccess $collection The sorted collection to search.
     * @param mixed             $element    The element to search for.
     * @param callable          $comparator The comparator used to compare elements.
     * @param integer           $begin      The index at which to start the search.
     * @param integer|null      $end        The index at which to stop the search, or null to use the entire collection.
     */
    public static function upperBound($collection, $element, $comparator, $begin = 0, $end = null)
    {
        if (null === $end) {
            $end = static::size($collection);
        }

        $count = $end - $begin;

        while ($count > 0) {
            $step = intval($count / 2);
            $pivotIndex = $begin + $step;

            if (call_user_func($comparator, $collection[$pivotIndex], $element) <= 0) {
                $begin = $pivotIndex + 1;
                $count -= $step + 1;
            } else {
                $count = $step;
            }
        }

        return $begin;
    }

    /**
     * Perform a binary search on a sorted sequence.
     *
     * Searches all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param array|ArrayAccess $collection   The collection to search.
     * @param mixed             $element      The element to search for.
     * @param callable          $comparator   The comparator used to compare elements.
     * @param integer           $begin        The index at which to start the search.
     * @param integer|null      $end          The index at which to stop the search, or null to use the entire collection.
     * @param integer|null      &$insertIndex Assigned the index at which $element must be inserted to maintain sortedness.
     *
     * @return integer|null The index at which an element equal to $element is present in $collection.
     */
    public static function binarySearch($collection, $element, $comparator, $begin = 0, $end = null, &$insertIndex = null)
    {
        if (null === $end) {
            $end = static::size($collection);
        }

        $insertIndex = static::lowerBound($collection, $element, $comparator, $begin, $end);

        if ($insertIndex === $end) {
            return null;
        } elseif (0 !== call_user_func($comparator, $collection[$insertIndex], $element)) {
            return null;
        }

        return $insertIndex;
    }
}
