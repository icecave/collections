<?php
namespace Icecave\Collections;

// @codeCoverageIgnoreStart

/**
 * An associative collection is a variable-sized collection that supports efficient retrieval of values based on keys.
 *
 * Each element in a associative collection is a 2-tuple of key and value.
 *
 * Mutable associative collections support insertion and removal of elements, but differ from sequences in that they do
 * not provide a mechanism for inserting an element at a specific position.
 */
interface IMutableAssociative extends IAssociative, IMutableIterable {

    /**
     * Associate a value with a key.
     *
     * Associates $value with $key regardless of whether or not $key already exists.
     *
     * @see IMutableAssociative::add()
     * @see IMutableAssociative::replace()
     *
     * @param mixed $key The element's key.
     * @param mixed $value The element's value.
     */
    public function set($key, $value);

    /**
     * Associate a value with a new key.
     *
     * Associates $value with $key only if $key does not already exist.
     *
     * @see IMutableAssociative::set()
     * @see IMutableAssociative::replace()
     * @see IMutableAssociative::tryAdd()
     *
     * @param mixed $key The element's key.
     * @param mixed $value The element's value.
     *
     * @throws Exception\DuplicateKeyException if $key already exists.
     */
    public function add($key, $value);

    /**
     * Associate a value with a new key.
     *
     * Associates $value with $key only if $key does not already exist.
     *
     * @see IMutableAssociative::add()
     *
     * @param mixed $key The element's key.
     * @param mixed $value The element's value.
     *
     * @return boolean True if $key did not already exist and the value has been set; otherwise, false.
     */
    public function tryAdd($key, $value);

    /**
     * Associate a new value with an existing key.
     *
     * Associates $value with $key only if $key already exists.
     *
     * @see IMutableAssociative::add()
     * @see IMutableAssociative::set()
     * @see IMutableAssociative::tryReplace()
     *
     * @param mixed $key The element's key.
     * @param mixed $value The element's value.
     *
     * @return mixed The value previously associated with this key.
     * @throws Exception\UnknownKeyException if $key does not already exist.
     */
    public function replace($key, $value);

    /**
     * Associate a new value with an existing key.
     *
     * Associates $value with $key only if $key already exists.
     *
     * @see IMutableAssociative::replace()
     *
     * @param mixed $key The element's key.
     * @param mixed $value The element's value.
     * @param mixed &$previous Assigned the value previously associated with $key.
     *
     * @return boolean True if $key already exists and the new value has been set; otherwise, false.
     */
    public function tryReplace($key, $value, &$previous = null);

    /**
     * Remove an element from the collection.
     *
     * @param mixed $key The key of the element to remove.
     *
     * @return mixed The value associated with this key.
     * @throws Exception\UnknownKeyException if $key does not exist.
     */
    public function remove($key);

    /**
     * Remove an element from the collection.
     *
     * @param mixed $key The key of the element to remove.
     * @param mixed &$value Assigned the value associated with $key if it exists.
     *
     * @return boolean True if the key exists and has been removed; otherwise, false.
     */
    public function tryRemove($key, &$value = null);

    /**
     * Add the elements from one or more other collections to this collection.
     *
     * Any existing keys are overwritten from left to right.
     *
     * @param IAssociative,... $collection The collections to merge.
     */
    public function merge(IAssociative $collection);

    /**
     * Swap the elements associated with two keys.
     *
     * @param mixed $key1 The key of the first element.
     * @param mixed $key2 The key of the second element.
     *
     * @throws Exception\UnknownKeyException if $key1 or $key2 does not already exist.
     */
    public function swap($key1, $key2);

    /**
     * Swap the elements associated with two keys.
     *
     * @param mixed $key1 The key of the first element.
     * @param mixed $key2 The key of the second element.
     *
     * @return boolean True if $key1 and $key2 exist and the swap is successful.
     */
    public function trySwap($key1, $key2);

    /**
     * Move an element from one key to another, replacing the target key if it already exists.
     *
     * @see IMutableAssociative::tryMove()
     * @see IMutableAssociative::rename()
     * @see IMutableAssociative::tryRename()
     *
     * @param mixed $source The existing key.
     * @param mixed $target The new key.
     *
     * @throws Exception\UnknownKeyException if $source does not already exist.
     */
    public function move($source, $target);

    /**
     * Move an element from one key to another, replacing the target key if it already exists.
     *
     * @see IMutableAssociative::move()
     * @see IMutableAssociative::rename()
     * @see IMutableAssociative::tryRename()
     *
     * @param mixed $source The existing key.
     * @param mixed $target The new key.
     *
     * @return boolean True if $source exists and the move is successful.
     */
    public function tryMove($source, $target);

    /**
     * Move an element from one key to another.
     *
     * It is an error if the target key already exists.
     *
     * @see IMutableAssociative::move()
     * @see IMutableAssociative::tryMove()
     * @see IMutableAssociative::tryRename()
     *
     * @param mixed $source The existing key.
     * @param mixed $target The new key.
     *
     * @throws Exception\UnknownKeyException if $source does not already exist.
     * @throws Exception\DuplicateKeyException if $target already exists.
     */
    public function rename($source, $target);

    /**
     * Move an element from one key to another.
     *
     * It is an error if the target key already exists.
     *
     * @see IMutableAssociative::move()
     * @see IMutableAssociative::tryMove()
     * @see IMutableAssociative::rename()
     *
     * @param mixed $source The existing key.
     * @param mixed $target The new key.
     *
     * @return boolean True if $source exists, $target does not exist and the move is successful.
     */
    public function tryRename($source, $target);
}
