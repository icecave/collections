# Collections

[![Build Status]](https://travis-ci.org/IcecaveStudios/collections)
[![Test Coverage]](https://coveralls.io/r/IcecaveStudios/collections?branch=develop)
[![SemVer]](http://semver.org)

**Collections** provides a set of collection types loosely inspired by the .NET runtime and the C++ standard template
library.

* Install via [Composer](http://getcomposer.org) package [icecave/collections](https://packagist.org/packages/icecave/collections)
* Read the [API documentation](http://icecavestudios.github.io/collections/artifacts/documentation/api/)

## Rationale

PHP has long been lacking formal, performant collection types. The addition of the heap-centric collections to the SPL
has gone some way to addressing this problem but has fallen short in some regards. For example,
[SplDoublyLinkedList](http://www.php.net/manual/en/class.spldoublylinkedlist.php) does not expose some of the operations
that linked lists are designed to solve efficiently, such as insertion and deletion operations in the middle of the
collection. There are also several broken abstractions. One example is [SplQueue](http://php.net/manual/en/class.splqueue.php)
which exposes methods for manipulating both the head and tail of the queue.

## Concepts

* [Collection](src/Icecave/Collections/CollectionInterface.php): A collection is an object that stores other objects (called elements).
* [Mutable Collection](src/Icecave/Collections/MutableCollectionInterface.php): A mutable collection is a collection on which elements can be added and removed.
* [Iterable](src/Icecave/Collections/IterableInterface.php): Iterable collections allow sequential access to the elements without modifying the collection.
* [Mutable Iterable](src/Icecave/Collections/MutableIterableInterface.php): An iterable collection that can be modified in place.
* [Sequence](src/Icecave/Collections/SequenceInterface.php): A sequence is a variable-sized collection whose elements are arranged in a strict linear order.
* [Mutable Sequence](src/Icecave/Collections/MutableSequenceInterface.php): A sequence that supports insertion and removal of elements.
* [Random Access Sequence](src/Icecave/Collections/RandomAccessInterface.php): A sequence that provides access to elements by position.
* [Mutable Random Access Sequence](src/Icecave/Collections/MutableRandomAccessInterface.php): A sequence that allows insertion & removal of elements by position.
* [Associative Collection](src/Icecave/Collections/AssociativeInterface.php): A variable-sized collection that supports efficient retrieval of values based on keys.
* [Mutable Associative Collection](src/Icecave/Collections/MutableAssociativeInterface.php): An associative collection that supports insertion and removal of elements.
* [Queued Access](src/Icecave/Collections/QueuedAccessInterface.php): A F/LIFO buffer (ie, stacks and queues).
* [Set](src/Icecave/Collections/SetInterface.php): Un-ordered, iterable collection with unique elements.

## Collections

* [Vector](src/Icecave/Collections/Vector.php): A mutable sequence with efficient access by position and iteration.
* [LinkedList](src/Icecave/Collections/LinkedList.php): A mutable sequence with efficient addition and removal of elements.
* [Map](src/Icecave/Collections/Map.php): Associative collections with efficient access by key.
* [Set](src/Icecave/Collections/Set.php): Iterable collections with unique elements.
* [Queue](src/Icecave/Collections/Queue.php): A first-in/first-out (FIFO) queue of elements.
* [PriorityQueue](src/Icecave/Collections/PriorityQueue.php): A prioritized first-in/first-out (FIFO) queue of elements.
* [Stack](src/Icecave/Collections/Stack.php): A last-in/first-out (LIFO) stack of elements.

## Iterators

* [AssociativeIterator](src/Icecave/Collections/Iterator/AssociativeIterator.php): An iterator for iterating any associative collection.
* [RandomAccessIterator](src/Icecave/Collections/Iterator/RandomAccessIterator.php): An iterator for iterating any random access collection.
* [SequentialKeyIterator](src/Icecave/Collections/Iterator/SequentialKeyIterator.php): An iterator adaptor for producing sequential integer keys.

## Serialization

The provided collection types support [serialization](http://au1.php.net/manual/en/function.serialize.php), so long as
the elements contained within the collection are also serializable.

## Cloning

The provided collection implementations support [cloning](http://php.net/manual/en/language.oop5.cloning.php). Cloning a
collection produces a copy of the collection containing the same elements. The elements themselves are not cloned.

<!-- references -->
[Build Status]: http://img.shields.io/travis/IcecaveStudios/collections/develop.svg
[Test Coverage]: http://img.shields.io/coveralls/IcecaveStudios/collections/develop.svg
[SemVer]: http://img.shields.io/:semver-1.1.0-brightgreen.svg
