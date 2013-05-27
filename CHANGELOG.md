# Collections Changelog

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
