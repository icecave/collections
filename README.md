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
* Use a [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
  compatible autoloader (namespace 'Icecave\Collections' in the 'lib' directory)

## Concepts

* [ICollection](tree/master/lib/Icecave/Collections/ICollection.php): A collection is an object that stores other objects (called elements).
* [IMutableCollection](tree/master/lib/Icecave/Collections/IMutableCollection.php): A mutable collection is a collection on which elements can be added and removed.
* [IIterable](tree/master/lib/Icecave/Collections/IIterable.php): Iterable collections allow (at the very least) sequential access to the elements without modifying the collection.
* [IMutableIterable](tree/master/lib/Icecave/Collections/IMutableIterable.php): An interable collection that can be modified in place.
* [ISequence](tree/master/lib/Icecave/Collections/ISequence.php): A sequence is a variable-sized collection whose elements are arranged in a strict linear order.
* [IMutableSequence](tree/master/lib/Icecave/Collections/IMutableSequence.php): A sequence that supports insertion and removal of elements.
* [IRandomAccess](tree/master/lib/Icecave/Collections/IRandomAccess.php): A sequence that provides access to arbitrary elements by their position in the sequence.
* [IMutableRandomAccess](tree/master/lib/Icecave/Collections/IMutableRandomAccess.php): A sequence that allows for insertion and removal of arbitrary elements by their position in the sequence.
* [IAssociative](tree/master/lib/./Collections/ICollection.php): A variable-sized collection that supports efficient retrieval of values based on keys.
* [IMutableAssociative](tree/master/lib/Icecave/Collections/IMutableAssociative.php): An associative collection that supports insertion and removal of elements.
* [IStack](tree/master/lib/Icecave/Collections/IStack.php): A LIFO stack.
* [IQueue](tree/master/lib/Icecave/Collections/IQueue.php): A FIFO queue.

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