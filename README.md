# Collections

[![Build Status](https://secure.travis-ci.org/IcecaveStudios/collections.png)](http://travis-ci.org/IcecaveStudios/collections)

**Collections** provides a set of PHP collection types loosely inspired by the .NET runtime and the C++ standard template library.

## Installation

**Collections** requires PHP 5.3.

### With [Composer](http://getcomposer.org/)

* Add 'icecave/collections' to the project's composer.json dependencies
* Run `php composer.phar install`

### Bare installation

* Clone from GitHub: `git clone git://github.com/IcecaveStudios/collections.git`
* Use a [PSR-0](https://github.com/php-fig/fig-standards//IcecaveStudios/collections/blob/master/accepted/PSR-0.md)
  compatible autoloader (namespace 'Icecave\Collections' in the 'lib' directory)

## Rationale

PHP has long been lacking formal, performant collection types. The addition of the heap-centric collections to the SPL has gone some way to addressing this problem but has fallen short in some regards. For example, [SplDoublyLinkedList](http://www.php.net/manual/en/class.spldoublylinkedlist.php) does not expose some of the operations that linked lists are designed to solve efficiently, such as insertion and deletion operations in the middle of the collection. There are also several broken abstractions. One example is [SplQueue](http://php.net/manual/en/class.splqueue.php) which exposes methods for manipulating both the head and tail of the queue.

## Concepts

* [Collection](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/CollectionInterface.php): A collection is an object that stores other objects (called elements).
* [Mutable Collection](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/MutableCollectionInterface.php): A mutable collection is a collection on which elements can be added and removed.
* [Iterable](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IterableInterface.php): Iterable collections allow (at the very least) sequential access to the elements without modifying the collection.
* [Mutable Iterable](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/MutableIterableInterface.php): An iterable collection that can be modified in place.
* [Sequence](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/SequenceInterface.php): A sequence is a variable-sized collection whose elements are arranged in a strict linear order.
* [Mutable Sequence](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/MutableSequenceInterface.php): A sequence that supports insertion and removal of elements.
* [Random Access Sequence](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/RandomAccessInterface.php): A sequence that provides access to elements by their position in the sequence.
* [Mutable Random Access Sequence](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/MutableRandomAccessInterface.php): A sequence that allows for insertion / removal of elements by their position in the sequence.
* [Associative Collection](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/AssociativeInterface.php): A variable-sized collection that supports efficient retrieval of values based on keys.
* [Mutable Associative Collection](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/MutableAssociativeInterface.php): An associative collection that supports insertion and removal of elements.
* [Queued Access](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/QueuedAccessInterface.php): A F/LIFO buffer (ie, stacks and queues).

## Collections

* [Vector](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/Vector.php): A mutable sequence with efficient access by position and iteration.
* [LinkedList](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/LinkedList.php): A mutable sequence with efficient addition and removal of elements.
* [Map](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/Map.php): An associative collection with efficient access by key.
* [Set](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/Set.php): An iterable collection with unique elements.
* [Queue](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/Queue.php): A FIFO queue.
* [PriorityQueue](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/PriorityQueue.php): A prioritized queue.
* [Stack]((/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/Stack.php): A LIFO stack.

## Iterators

* [AssociativeIterator](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/Iterator/AssociativeIterator.php): An iterator for iterating any associative collection.
* [RandomAccessIterator](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/Iterator/RandomAccessIterator.php): An iterator for iterating any random access collection.
