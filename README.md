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

## Concepts

* [Collection](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/ICollection.php): A collection is an object that stores other objects (called elements).
* [Mutable Collection](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IMutableCollection.php): A mutable collection is a collection on which elements can be added and removed.
* [Iterable](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IIterable.php): Iterable collections allow (at the very least) sequential access to the elements without modifying the collection.
* [Mutable Iterable](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IMutableIterable.php): An interable collection that can be modified in place.
* [Sequence](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/ISequence.php): A sequence is a variable-sized collection whose elements are arranged in a strict linear order.
* [Mutable Sequence](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IMutableSequence.php): A sequence that supports insertion and removal of elements.
* [Random Access Sequence](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IRandomAccess.php): A sequence that provides access to arbitrary elements by their position in the sequence.
* [Mutable Random Access Sequence](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IMutableRandomAccess.php): A sequence that allows for insertion and removal of arbitrary elements by their position in the sequence.
* [Associative Collection](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/Collections/ICollection.php): A variable-sized collection that supports efficient retrieval of values based on keys.
* [Mutable Associative Collection](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IMutableAssociative.php): An associative collection that supports insertion and removal of elements.
* [Stack](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IStack.php): A LIFO stack.
* [Queue](/IcecaveStudios/collections/blob/master/lib/Icecave/Collections/IQueue.php): A FIFO queue.

## Models

* Vector
* List
* Map
* MultiMap
* Set
* MultiSet
* Queue
* PriorityQueue
* Stack
