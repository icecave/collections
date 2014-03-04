# Collections Changelog

### 1.1.0 (2014-03-04)

* **[FIXED]** `Collection::getIterator()` now correctly resolves nested `IteratorAggregate` iterators
* **[NEW]** Added `Set::pop()` and `Map::pop()`
* **[IMPROVED]** `LinkedList` is now implemented as a doubly-linked-list, the original implementation is available as `SinglyLinkedList`
* **[IMPROVED]** Updated autoloader to [PSR-4](http://www.php-fig.org/psr/psr-4/)

### 1.0.0 (2014-01-17)

* Stable release (no API changes since 1.0.0-alpha.1).

### 1.0.0-alpha.1 (2013-10-20)

* **[BC]** Removed `HashMap` and `HashSet`

### 0.8.0 (2013-09-23)

* **[BC]** Changed `Vector`, `Map`, `HashMap` and `LinkedList` to implement `IteratorAggregate` instead of `Iterator` to prevent issues with nested iteration
* **[NEW]** Added `Collection::trySize()`

### 0.7.1 (2013-09-21)

* **[FIXED]** Fixed issue with `Vector::compare()` whereby equal vectors with differing internal array sizes were not considered equal

### 0.7.0 (2013-08-25)

This release includes numerous naming changes to improve consistency across the project. Please be aware that some new
methods and classes have been added with the same name as existing methods which have been renamed.

* **[BC]** The `mbstring` extension is now required
* **[BC]** Renamed `MutableAssociativeInterface::merge()` to `mergeInPlace()`
* **[BC]** Renamed `AssociativeInterface::combine()` to `merge()`
* **[BC]** Renamed `MutableIterableInterface::filter()` to `filterInPlace()`
* **[BC]** Renamed `IterableInterface::filtered()` to `filter()`
* **[BC]** Renamed `MutableSequenceInterface::sort()` to `sortInPlace()`
* **[BC]** Renamed `MutableSequenceInterface::reverse()` to `reverseInPlace()`
* **[BC]** Renamed `SequenceInterface::sorted()` to `sort()`
* **[BC]** Renamed `SequenceInterface::reverse()` to `reversed()`
* **[BC]** Moved `AssociativeKeyGenerator` into `Icecave\Collections\Detail` namespace (it is not part of the public API)
* **[BC]** Added begin index parameter to `RandomAccessInterface::indexOfLast()` and `findLast()`
* **[BC]** Swapped the order of parameters to `PriorityQueue::__construct()`
* **[BC]** The `Map` class has been renamed to `HashMap`
* **[BC]** The `Set` class has been renamed to `HashSet`
* **[BC]** Renamed `Set::isEqual()` to `Set::isEqualSet()`
* **[BC]** All set-specific operations (union, diff, etc) can now only operate on sets of the same type and hash/comparator function
* **[BC]** Renamed `Set::isStrictSuperSet()` and `isStrictSubSet()` to `isProperSuperSet()` and `isProperSubSet()`, respectively
* **[FIXED]** Fixed issue that prevented pushing elements into a `PriorityQueue` with an explicit priority that is weakly equivalent to null (eg, 0)
* **[FIXED]** Fixed issues with `Vector::filter()` and `join()` whereby additional null values were added to the collection
* **[FIXED]** Fixed several issues with `Vector` whereby `count()` is called on iterable arguments that are not necessarily countable
* **[NEW]** `Collection` class provides collection type agnostic operations for generic programming
* **[NEW]** All collections now implement `ExtendedComparableInterface` from [Icecave\Parity](https://github.com/IcecaveStudios/parity)
* **[NEW]** Added `Map`, a comparator based associative container
* **[NEW]** Added `Set`, a comparator based set container
* **[NEW]** Added `SetInterface`, which adds `isIntersecting()`, `addMany()`, `removeMany()`, `symmetricDiff()` and `symmetricDiffInPlace()`
* **[NEW]** Added an iterator traits system (see `Collection::iteratorTraits`)
* **[NEW]** Added `create()` static method to each collection for constructing from elements as variable arguments
* **[NEW]** Added `MutableRandomAccessInterface::insertRange()`
* **[IMPROVED]** Prioritizer parameter to `PriorityQueue::__construct()` now defaults to the identity function
* **[IMPROVED]** Added optional end index parameter to `RandomAccessInterface::indexOf()` and `find()`
* **[IMPROVED]** `Vector` now implements `SeekableIterator`

### 0.6.0 (2013-02-25)

* **[NEW]** Added `IterableInterface::partition()`, `each()`, `any()` and `all()`
* **[FIXED]** Fixed issue setting/returning previous element in `Map::replace()` and `tryReplace()`
* **[FIXED]** `Set` iteration now produces an sequential integer key

### 0.5.1 (2013-02-07)

* **[FIXED]** `Map` and `Set` collections now retain custom hash function when serializing

### 0.5.0 (2013-01-13)

* **[NEW]** All collection types are now serializable
* **[NEW]** Added `Set::isEqual()`, `isSuperset()`, `isSubset()`, `isStrictSuperset()` and `isStrictSubset()`

### 0.4.0 (2013-01-11)

* **[NEW]** All collection types are now clonable
* **[NEW]** Added `RandomAccessInterface::indexOfLast()`, `find()` and `findLast()`
* **[IMPROVED]** `LinkedList::sort()` now uses a merge sort instead of converting to a native array

### 0.3.0 (2013-01-10)

* **[IMPROVED]** Added Typhoon type checks
* **[IMPROVED]** [Icecave\Repr](https://github.com/IcecaveStudios/repr) now used in __toString() methods

### 0.2.0 (2013-01-10)

* **[NEW]** Added `AssociativeIterator` and `RandomAccessIterator`
* **[NEW]** Collections now implement `Iterator`, `Countable` and `ArrayAccess` as appropriate

### 0.1.0 (2013-01-10)

* Initial release
