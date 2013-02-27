<?php
namespace Icecave\Collections;

use ArrayAccess;
use Countable;
use Icecave\Collections\Iterator\Traits;
use Icecave\Collections\Iterator\TraitsProviderInterface;
use Icecave\Collections\TypeCheck\TypeCheck;
use SplDoublyLinkedList;
use SplHeap;
use SplPriorityQueue;
use SplFixedArray;
use SplObjectStorage;
use Traversable;

/**
 * Utility function for working with arbitrary collection types.
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
        TypeCheck::get(__CLASS__)->isEmpty(func_get_args());

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
        TypeCheck::get(__CLASS__)->size(func_get_args());

        if ($collection instanceof CollectionInterface) {
            return $collection->size();
        } elseif ($collection instanceof Countable) {
            return count($collection);
        } elseif (is_array($collection)) {
            return count($collection);
        }

        $count = 0;
        foreach ($collection as $value) {
            ++$count;
        }

        return $count;
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
        TypeCheck::get(__CLASS__)->get(func_get_args());

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
        TypeCheck::get(__CLASS__)->tryGet(func_get_args());

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
        TypeCheck::get(__CLASS__)->getWithDefault(func_get_args());

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
        TypeCheck::get(__CLASS__)->hasKey(func_get_args());

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
        TypeCheck::get(__CLASS__)->contains(func_get_args());

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
        TypeCheck::get(__CLASS__)->keys(func_get_args());

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
        TypeCheck::get(__CLASS__)->values(func_get_args());

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
        TypeCheck::get(__CLASS__)->elements(func_get_args());

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
        TypeCheck::get(__CLASS__)->map(func_get_args());

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
    public static function filtered($collection, $predicate, &$result = array())
    {
        TypeCheck::get(__CLASS__)->filtered(func_get_args());

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
        TypeCheck::get(__CLASS__)->each(func_get_args());

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
        TypeCheck::get(__CLASS__)->all(func_get_args());

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
        TypeCheck::get(__CLASS__)->any(func_get_args());

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
        TypeCheck::get(__CLASS__)->isSequential(func_get_args());

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
     * Fetch the traits for an iterator.
     *
     * @param array|Traversable|Countable|TraitsProviderInterface $iterator
     *
     * @return Traits
     */
    public static function iteratorTraits($iterator)
    {
        TypeCheck::get(__CLASS__)->iteratorTraits(func_get_args());

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
}
