<?php
namespace Icecave\Collections;

/**
 * An associative collection is a variable-sized collection that supports efficient retrieval of values based on keys.
 *
 * Each element in an associative collection is a 2-tuple of key and value.
 */
interface AssociativeInterface extends IterableInterface
{
    /**
     * Check if the collection contains an element with the given key.
     *
     * @param mixed $key The key to check.
     *
     * @return boolean True if the collection contains the given key; otherwise, false.
     */
    public function hasKey($key);

    /**
     * Fetch the value associated with the given key.
     *
     * @param mixed $key The key to fetch.
     *
     * @return mixed                         The associated value.
     * @throws Exception\UnknownKeyException if no such key exists.
     */
    public function get($key);

    /**
     * Fetch the value associated with the given key if it exists.
     *
     * @param mixed $key    The key to fetch.
     * @param mixed &$value Assigned the value associated with $key if it exists.
     *
     * @return boolean True if $key exists and $value was populated; otherwise, false.
     */
    public function tryGet($key, &$value);

    /**
     * Fetch the value associated with the given key, or a default value if it does not exist.
     *
     * @param mixed $key     The key to fetch.
     * @param mixed $default The default value to return if $key does not exist.
     *
     * @return mixed The value associated with $key, or the $default if nos such key exists.
     */
    public function getWithDefault($key, $default = null);

    /**
     * Return the value associated with the first key that exists.
     *
     * Takes a variable number of keys and searches for each one in order,
     * returns the value associated with the first key that exists.
     *
     * @param mixed $key            The key to search for.
     * @param mixed $additional,... Additional keys to search for.
     *
     * @return mixed                         The value associated with the first key that exists.
     * @throws Exception\UnknownKeyException if none of the keys exist.
     */
    public function cascade($key);

    /**
     * Return the value associated with the first key that exists, or a default value if none of the provided keys exist.
     *
     * @param mixed $default        The default value to return if no such keys exist.
     * @param mixed $key            The key to search for.
     * @param mixed $additional,... Additional keys to search for.
     *
     * @return mixed The value associated with the first key that exists, or $default if none of the keys exist.
     */
    public function cascadeWithDefault($default, $key);

    /**
     * Return the value associated with the first existing key in the given sequence.
     *
     * Behaves as per {@see AssociativeInterface::cascade()} except that the keys are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed<mixed> $keys The list of keys.
     *
     * @return mixed                         The value associated with the first key that exists.
     * @throws Exception\UnknownKeyException if none of the keys exist.
     */
    public function cascadeIterable($keys);

    /**
     * Return the value associated with the first existing key in the given sequence, or a default value if none of the provided keys exist.
     *
     * Behaves as per {@see AssociativeInterface::cascadeDefault()} except that the keys are provided as
     * a traversable (eg, array) instead of via a variable argument list.
     *
     * @param mixed        $default The default value to return if no such keys exist.
     * @param mixed<mixed> $keys    The list of keys.
     *
     * @return mixed The value associated with the first key that exists, or $default if none of the keys exist.
     */
    public function cascadeIterableWithDefault($default, $keys);

    /**
     * Fetch a native array containing the keys in the collection.
     *
     * There is no guarantee that the order of keys will match the order of values produced by {@see AssociativeInterface::values()}.
     *
     * @return array A native array containing the keys in the collection.
     */
    public function keys();

    /**
     * Fetch a native array containing the values in the collection.
     *
     * There is no guarantee that the order of values will match the order of keys produced by {@see AssociativeInterface::keys()}.
     *
     * @return array A native array containing the values in the collection.
     */
    public function values();

    /**
     * Produce a new collection containing the elements of this collection and one or more other collections.
     *
     * Any existing keys are overwritten from left to right.
     * It is not guaranteed that the concrete type of the merged collection will match this collection.
     *
     * @param AssociativeInterface $collection     The collection to combine.
     * @param AssociativeInterface $additional,... Additional collections to combine.
     *
     * @return AssociativeInterface The merged collection.
     */
    public function merge(AssociativeInterface $collection);

    /**
     * Create a new collection containing the elements associated with the provided keys.
     *
     * It is not guaranteed that the concrete type of the projected collection will match this collection.
     *
     * @param mixed $key            The key to include in the new collection.
     * @param mixed $additional,... Ådditional keys to include in the new collection.
     *
     * @return AssociativeInterface The projection of the collection.
     */
    public function project($key);

    /**
     * Create a new collection containing the elements associated with the provided keys.
     *
     * It is not guaranteed that the concrete type of the projected collection will match this collection.
     *
     * @param mixed<mixed> $keys The keys to include in the new collection.
     *
     * @return AssociativeInterface The projection of the collection.
     */
    public function projectIterable($keys);
}
