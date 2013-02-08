# Collections Changelog

### 0.5.1

* Map and Set collections now retain custom hash function when serializing

### 0.5.0

* All collection types are now serializable
* Added Set::isEqual(), Set::isSuperset(), Set::isSubset(), Set::isStrictSuperset() and Set::isStrictSubset()

### 0.4.0

* All collection types are now clonable
* LinkedList::sort() now uses a merge sort instead of converting to a native array
* Added RandomAccessInterface::indexOfLast(), RandomAccessInterface::find() and RandomAccessInterface::findLast()

### 0.3.0

* Added Typhoon type checks
* Icecave\Repr now used in __toString() methods

### 0.2.0

* Added AssociativeIterator and RandomAccessIterator
* Collections now implement Iterator, Countable and ArrayAccess as appropriate

### 0.1.0

* Initial release
