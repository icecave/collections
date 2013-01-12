<?php
namespace Icecave\Collections;

use PHPUnit_Framework_TestCase;

class VectorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_collection = new Vector;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->_collection->size());
    }

    public function testConstructorWithArray()
    {
        $collection = new Vector(array(1, 2, 3));
        $this->assertSame(array(1, 2, 3), $collection->elements());
    }

    public function testClone()
    {
        $this->_collection->pushBack(1);
        $this->_collection->pushBack(2);
        $this->_collection->pushBack(3);

        $collection = clone $this->_collection;

        $collection->popBack();

        $this->assertSame(array(1, 2), $collection->elements());
        $this->assertSame(array(1, 2, 3), $this->_collection->elements());
    }

    public function testSerialization()
    {
        $this->_collection->pushBack(1);
        $this->_collection->pushBack(2);
        $this->_collection->pushBack(3);

        $packet = serialize($this->_collection);
        $collection = unserialize($packet);

        $this->assertSame($this->_collection->elements(), $collection->elements());
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    public function testSize()
    {
        $this->assertSame(0, $this->_collection->size());

        $this->_collection->pushBack('foo');
        $this->_collection->pushBack('bar');
        $this->_collection->pushBack('spam');

        $this->assertSame(3, $this->_collection->size());

        $this->_collection->clear();

        $this->assertSame(0, $this->_collection->size());
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->_collection->isEmpty());

        $this->_collection->pushBack('foo');

        $this->assertFalse($this->_collection->isEmpty());

        $this->_collection->clear();

        $this->assertTrue($this->_collection->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('<Vector 0>', $this->_collection->__toString());

        $this->_collection->pushBack('foo');
        $this->_collection->pushBack('bar');
        $this->_collection->pushBack('spam');

        $this->assertSame('<Vector 3 ["foo", "bar", "spam"]>', $this->_collection->__toString());

        $this->_collection->pushBack('doom');

        $this->assertSame('<Vector 4 ["foo", "bar", "spam", ...]>', $this->_collection->__toString());
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    public function testClear()
    {
        $this->_collection->pushBack('foo');

        $this->_collection->clear();

        $this->assertTrue($this->_collection->isEmpty());
    }

    /////////////////////////////////////////
    // Implementation of IterableInterface //
    /////////////////////////////////////////

    public function testElements()
    {
        $this->assertSame(array(), $this->_collection->elements());

        $this->_collection->pushBack('foo');
        $this->_collection->pushBack('bar');
        $this->_collection->pushBack('spam');

        $this->assertSame(array('foo', 'bar', 'spam'), $this->_collection->elements());
    }

    public function testContains()
    {
        $this->assertFalse($this->_collection->contains('foo'));

        $this->_collection->pushBack('foo');

        $this->assertTrue($this->_collection->contains('foo'));
    }

    public function testFiltered()
    {
        $this->_collection->reserve(16); // Inflate capacity to test that iteration stops at size().
        $this->_collection->append(array(1, null, 2, null, 3));

        $result = $this->_collection->filtered();

        $this->assertInstanceOf(__NAMESPACE__ . '\Vector', $result);
        $this->assertSame(array(1, 2, 3), $result->elements());
    }

    public function testFilteredWithPredicate()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->filtered(
            function ($element) {
                return $element & 0x1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Vector', $result);
        $this->assertSame(array(1, 3, 5), $result->elements());
    }

    public function testMap()
    {
        $this->_collection->reserve(16); // Inflate capacity to test that iteration stops at size().
        $this->_collection->append(array(1, 2, 3));

        $result = $this->_collection->map(
            function ($element) {
                return $element + 1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Vector', $result);
        $this->assertSame(array(2, 3, 4), $result->elements());
    }

    ////////////////////////////////////////////////
    // Implementation of MutableIterableInterface //
    ////////////////////////////////////////////////

    public function testFilter()
    {
        $this->_collection->reserve(16); // Inflate capacity to test that iteration stops at size().
        $this->_collection->append(array(1, null, 2, null, 3));

        $this->_collection->filter();

        $this->assertSame(array(1, 2, 3), $this->_collection->elements());
    }

    public function testFilterWithPredicate()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $this->_collection->filter(
            function ($element) {
                return $element & 0x1;
            }
        );

        $this->assertSame(array(1, 3, 5), $this->_collection->elements());
    }

    public function testApply()
    {
        $this->_collection->reserve(16); // Inflate capacity to test that iteration stops at size().
        $this->_collection->append(array(1, 2, 3));

        $this->_collection->apply(
            function ($element) {
                return $element + 1;
            }
        );

        $this->assertSame(array(2, 3, 4), $this->_collection->elements());
    }

    /////////////////////////////////////////
    // Implementation of SequenceInterface //
    /////////////////////////////////////////

    public function testFront()
    {
        $this->_collection->append(array('foo', 'bar'));

        $this->assertSame('foo', $this->_collection->front());
    }

    public function testFrontWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException', 'Collection is empty.');
        $this->_collection->front();
    }

    public function testTryFront()
    {
        $this->_collection->append(array('foo', 'bar'));

        $element = null;
        $this->assertTrue($this->_collection->tryFront($element));
        $this->assertSame('foo', $element);
    }

    public function testTryFrontWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->_collection->tryFront($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testBack()
    {
        $this->_collection->append(array('foo', 'bar'));
        $this->assertSame('bar', $this->_collection->back());
    }

    public function testBackWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException', 'Collection is empty.');
        $this->_collection->back();
    }

    public function testTryBack()
    {
        $this->_collection->append(array('foo', 'bar'));

        $element = null;
        $this->assertTrue($this->_collection->tryBack($element));
        $this->assertSame('bar', $element);
    }

    public function testTryBackWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->_collection->tryBack($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testSorted()
    {
        $this->_collection->append(array(3, 2, 1, 5, 4));

        $result = $this->_collection->sorted();

        $this->assertInstanceOf(__NAMESPACE__ . '\Vector', $result);
        $this->assertSame(array(1, 2, 3, 4, 5), $result->elements());
    }

    public function testSortedWithComparator()
    {
        $this->_collection->append(array(3, 2, 1, 5, 4));

        $result = $this->_collection->sorted(
            function ($a, $b) {
                return $b - $a;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Vector', $result);
        $this->assertSame(array(5, 4, 3, 2, 1), $result->elements());
    }

    public function testReversed()
    {
        $this->_collection->reserve(16); // Inflate capacity to test that iteration stops at size().
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->reversed();

        $this->assertInstanceOf(__NAMESPACE__ . '\Vector', $result);
        $this->assertSame(array(5, 4, 3, 2, 1), $result->elements());
    }

    public function testJoin()
    {
        $this->_collection->append(array(1, 2, 3));

        $result = $this->_collection->join(
            array(4, 5, 6),
            array(7, 8, 9)
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Vector', $result);
        $this->assertSame(array(1, 2, 3, 4, 5, 6, 7, 8, 9), $result->elements());
    }

    ////////////////////////////////////////////////
    // Implementation of MutableSequenceInterface //
    ////////////////////////////////////////////////

    public function testSort()
    {
        $this->_collection->append(array(4, 3, 2, 1, 5, 4));

        $this->_collection->sort();

        $this->assertSame(array(1, 2, 3, 4, 4, 5), $this->_collection->elements());
    }

    public function testSortWithComparator()
    {
        $this->_collection->append(array(4, 3, 2, 1, 5, 4));

        $this->_collection->sort(
            function ($a, $b) {
                return $b - $a;
            }
        );

        $this->assertSame(array(5, 4, 4, 3, 2, 1), $this->_collection->elements());
    }

    public function testSortWithEmptyCollection()
    {
        $this->_collection->sort();

        $this->assertSame(array(), $this->_collection->elements());
    }

    public function testSortWithSingleElement()
    {
        $this->_collection->pushBack(1);

        $this->_collection->sort();

        $this->assertSame(array(1), $this->_collection->elements());
    }

    public function testReverse()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $this->_collection->reverse();

        $this->assertSame(array(5, 4, 3, 2, 1), $this->_collection->elements());
    }

    public function testAppend()
    {
        $this->_collection->append(
            array(1, 2, 3),
            array(4, 5, 6)
        );

        $this->assertSame(array(1, 2, 3, 4, 5, 6), $this->_collection->elements());
    }

    public function testPushFront()
    {
        $this->_collection->pushFront(1);
        $this->_collection->pushFront(2);
        $this->_collection->pushFront(3);

        $this->assertSame(array(3, 2, 1), $this->_collection->elements());
    }

    public function testPopFront()
    {
        $this->_collection->append(array(1, 2, 3));

        $this->assertSame(1, $this->_collection->popFront());
        $this->assertSame(array(2, 3), $this->_collection->elements());
    }

    public function testPopFrontWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException', 'Collection is empty.');
        $this->_collection->popFront();
    }

    public function testTryPopFront()
    {
        $this->_collection->append(array(1, 2, 3));

        $element = null;
        $this->assertTrue($this->_collection->tryPopFront($element));
        $this->assertSame(1, $element);
        $this->assertSame(array(2, 3), $this->_collection->elements());
    }

    public function testTryPopFrontWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->_collection->tryPopFront($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testPushBack()
    {
        $this->_collection->pushBack(1);
        $this->_collection->pushBack(2);
        $this->_collection->pushBack(3);

        $this->assertSame(array(1, 2, 3), $this->_collection->elements());
    }

    public function testPopBack()
    {
        $this->_collection->append(array(1, 2, 3));

        $this->assertSame(3, $this->_collection->popBack());
        $this->assertSame(array(1, 2), $this->_collection->elements());
    }

    public function testPopBackWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException', 'Collection is empty.');
        $this->_collection->popBack();
    }

    public function testTryPopBack()
    {
        $this->_collection->append(array(1, 2, 3));

        $element = null;
        $this->assertTrue($this->_collection->tryPopBack($element));
        $this->assertSame(3, $element);
        $this->assertSame(array(1, 2), $this->_collection->elements());
    }

    public function testTryPopBackWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->_collection->tryPopBack($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testResize()
    {
        $this->_collection->resize(3);

        $this->assertSame(array(null, null, null), $this->_collection->elements());
    }

    public function testResizeWithValue()
    {
        $this->_collection->resize(3, 'foo');

        $this->assertSame(array('foo', 'foo', 'foo'), $this->_collection->elements());
    }

    public function testResizeToSmallerSize()
    {
        $this->_collection->append(array(1, 2, 3));

        $this->_collection->resize(2);

        $this->assertSame(array(1, 2), $this->_collection->elements());
    }

    //////////////////////////////////////////////
    // Implementation of RandomAccessInterface //
    /////////////////////////////////////////////

    public function testGet()
    {
        $this->_collection->append(array(1, 2, 3));

        $this->assertSame(2, $this->_collection->get(1));
    }

    public function testGetWithNegativeIndex()
    {
        $this->_collection->append(array(1, 2, 3));

        $this->assertSame(3, $this->_collection->get(-1));
    }

    public function testGetWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 0 is out of range.');
        $this->_collection->get(0);
    }

    public function testSlice()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->slice(2);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(3, 4, 5), $result->elements());
    }

    public function testSliceWithCount()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->slice(1, 3);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(2, 3, 4), $result->elements());
    }

    public function testSliceWithCountOverflow()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->slice(2, 100);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(3, 4, 5), $result->elements());
    }

    public function testSliceWithNegativeCount()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->slice(1, -3);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(), $result->elements());
    }

    public function testSliceWithNegativeIndex()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->slice(-2);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(4, 5), $result->elements());
    }

    public function testSliceWithNegativeIndexAndCount()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->slice(-3, 2);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(3, 4), $result->elements());
    }

    public function testSliceWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->slice(1);
    }

    public function testRange()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->range(1, 3);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(2, 3), $result->elements());
    }

    public function testRangeWithNegativeIndices()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->range(-3, -1);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(3, 4), $result->elements());
    }

    public function testRangeWithEndBeforeBegin()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $result = $this->_collection->range(3, 1);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(), $result->elements());
    }

    public function testRangeWithInvalidBegin()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $result = $this->_collection->range(1, 3);
    }

    public function testRangeWithInvalidEnd()
    {
        $this->_collection->append(array(1, 2, 3, 4, 5));

        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 100 is out of range.');
        $result = $this->_collection->range(1, 100);
    }

    public function testIndexOf()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(1, $this->_collection->indexOf('bar'));
    }

    public function testIndexOfWithStartIndex()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(1, $this->_collection->indexOf('bar', 1));

        $this->_collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(3, $this->_collection->indexOf('bar', 2));
    }

    public function testIndexOfWithNoMatch()
    {
        $this->_collection->reserve(16); // Inflate capacity to test that iteration stops at size().
        $this->assertNull($this->_collection->indexOf('foo'));

        $this->_collection->pushBack('bar');
        $this->assertNull($this->_collection->indexOf('foo'));
    }

    public function testIndexOfLast()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(3, $this->_collection->indexOfLast('bar'));
    }

    public function testIndexOfLastWithStartIndex()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(3, $this->_collection->indexOfLast('bar', 3));

        $this->_collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(1, $this->_collection->indexOfLast('bar', 2));
    }

    public function testIndexOfLastWithNoMatch()
    {
        $this->_collection->reserve(16); // Inflate capacity to test that iteration stops at size().
        $this->assertNull($this->_collection->indexOfLast('foo'));

        $this->_collection->pushBack('bar');
        $this->assertNull($this->_collection->indexOfLast('foo'));
    }

    public function testFind()
    {
        $comparator = function ($element) {
            return $element === 'bar';
        };

        $this->_collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(1, $this->_collection->find($comparator));
    }

    public function testFindLast()
    {
        $comparator = function ($element) {
            return $element === 'bar';
        };

        $this->_collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(3, $this->_collection->findLast($comparator));
    }

    ////////////////////////////////////////////////////
    // Implementation of MutableRandomAccessInterface //
    ////////////////////////////////////////////////////

    public function testSet()
    {
        $this->_collection->append(array('foo', 'bar', 'spam'));

        $this->_collection->set(1, 'goose');

        $this->assertSame(array('foo', 'goose', 'spam'), $this->_collection->elements());
    }

    public function testSetWithNegativeIndex()
    {
        $this->_collection->append(array('foo', 'bar', 'spam'));

        $this->_collection->set(-2, 'goose');

        $this->assertSame(array('foo', 'goose', 'spam'), $this->_collection->elements());
    }

    public function testSetWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 0 is out of range.');
        $this->_collection->set(0, 'bar');
    }

    public function testInsert()
    {
        $this->_collection->append(array('foo', 'spam'));

        $this->_collection->insert(1, 'bar');

        $this->assertSame(array('foo', 'bar', 'spam'), $this->_collection->elements());
    }

    public function testInsertWithNegativeIndex()
    {
        $this->_collection->append(array('foo', 'spam'));

        $this->_collection->insert(-1, 'bar');

        $this->assertSame(array('foo', 'bar', 'spam'), $this->_collection->elements());
    }

    public function testInsertAtEnd()
    {
        $this->_collection->append(array('foo', 'spam'));

        $this->_collection->insert($this->_collection->size(), 'bar');

        $this->assertSame(array('foo', 'spam', 'bar'), $this->_collection->elements());
    }

    public function testInsertWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->insert(1, 'foo');
    }

    public function testInsertMany()
    {
        $this->_collection->append(array('foo', 'spam'));

        $this->_collection->insertMany(1, array('bar', 'frob'));

        $this->assertSame(array('foo', 'bar', 'frob', 'spam'), $this->_collection->elements());
    }

    public function testInsertManyAtEnd()
    {
        $this->_collection->append(array('foo', 'spam'));

        $this->_collection->insertMany($this->_collection->size(), array('bar', 'frob'));

        $this->assertSame(array('foo', 'spam', 'bar', 'frob'), $this->_collection->elements());
    }

    public function testInsertManyWithEmptyElements()
    {
        $this->_collection->append(array('foo', 'spam'));

        $this->_collection->insertMany(1, array());

        $this->assertSame(array('foo', 'spam'), $this->_collection->elements());
    }

    public function testInsertManyWithNegativeIndex()
    {
        $this->_collection->append(array('foo', 'spam'));

        $this->_collection->insertMany(-1, array('bar', 'frob'));

        $this->assertSame(array('foo', 'bar', 'frob', 'spam'), $this->_collection->elements());
    }

    public function testInsertManyWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->insertMany(1, array('bar', 'frob'));
    }

    public function testRemove()
    {
        $this->_collection->append(array('foo', 'bar', 'spam'));

        $this->_collection->remove(1);

        $this->assertSame(array('foo', 'spam'), $this->_collection->elements());
    }

    public function testRemoveWithNegativeIndex()
    {
        $this->_collection->append(array('foo', 'bar', 'spam'));

        $this->_collection->remove(-2);

        $this->assertSame(array('foo', 'spam'), $this->_collection->elements());
    }

    public function testRemoveWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->remove(1);
    }

    public function testRemoveMany()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->removeMany(1);

        $this->assertSame(array('foo'), $this->_collection->elements());
    }

    public function testRemoveManyWithCount()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->removeMany(1, 2);

        $this->assertSame(array('foo', 'doom'), $this->_collection->elements());
    }

    public function testRemoveManyWithCountOverflow()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->removeMany(1, 100);

        $this->assertSame(array('foo'), $this->_collection->elements());
    }

    public function testRemoveManyWithNegativeIndex()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->removeMany(-3, 2);

        $this->assertSame(array('foo', 'doom'), $this->_collection->elements());
    }

    public function testRemoveManyWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->removeMany(1, 2);
    }

    public function testRemoveRange()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom', 'frob'));

        $this->_collection->removeRange(1, 3);

        $this->assertSame(array('foo', 'doom', 'frob'), $this->_collection->elements());
    }

    public function testRemoveRangeToEnd()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->removeRange(1, 3);

        $this->assertSame(array('foo', 'doom'), $this->_collection->elements());
    }

    public function testRemoveRangeWithNegativeIndex()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->removeRange(-3, -1);

        $this->assertSame(array('foo', 'doom'), $this->_collection->elements());
    }

    public function testRemoveRangeWithEndBeforeBegin()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->removeRange(3, 1);

        $this->assertSame(array('foo', 'bar', 'spam', 'doom'), $this->_collection->elements());
    }

    public function testRemoveRangeWithInvalidBegin()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->removeRange(1, 2);
    }

    public function testRemoveRangeWithInvalidEnd()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 100 is out of range.');
        $this->_collection->removeRange(1, 100);
    }

    public function testReplace()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->replace(1, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b'), $this->_collection->elements());
    }

    public function testReplaceWithCount()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->replace(1, array('a', 'b'), 2);

        $this->assertSame(array('foo', 'a', 'b', 'doom'), $this->_collection->elements());
    }

    public function testReplaceWithCountOverflow()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->replace(1, array('a', 'b'), 100);

        $this->assertSame(array('foo', 'a', 'b'), $this->_collection->elements());
    }

    public function testReplaceWithNegativeIndex()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->replace(-3, array('a', 'b'), 2);

        $this->assertSame(array('foo', 'a', 'b', 'doom'), $this->_collection->elements());
    }

    public function testReplaceWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->replace(1, array());
    }

    public function testReplaceRange()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->replaceRange(1, 3, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b', 'doom'), $this->_collection->elements());
    }

    public function testReplaceRangeWithNegativeIndices()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->replaceRange(-3, -1, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b', 'doom'), $this->_collection->elements());
    }

    public function testReplaceRangeWithZeroLength()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->replaceRange(1, 1, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b', 'bar', 'spam', 'doom'), $this->_collection->elements());
    }

    public function testReplaceRangeWithEndBeforeBegin()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->replaceRange(1, 0, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b', 'bar', 'spam', 'doom'), $this->_collection->elements());
    }

    public function testReplaceRangeWithInvalidBegin()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->replaceRange(1, 2, array());
    }

    public function testReplaceRangeWithInvalidEnd()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 100 is out of range.');
        $this->_collection->replaceRange(1, 100, array());
    }

    public function testSwap()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->swap(1, 2);

        $this->assertSame(array('foo', 'spam', 'bar', 'doom'), $this->_collection->elements());
    }

    public function testSwapWithNegativeIndices()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->_collection->swap(-1, -2);

        $this->assertSame(array('foo', 'bar', 'doom', 'spam'), $this->_collection->elements());
    }

    public function testSwapWithInvalidIndex1()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 1 is out of range.');
        $this->_collection->swap(1, 2);
    }

    public function testSwapWithInvalidIndex2()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 100 is out of range.');
        $this->_collection->swap(1, 100);
    }

    public function testTrySwap()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->assertTrue($this->_collection->trySwap(1, 2));

        $this->assertSame(array('foo', 'spam', 'bar', 'doom'), $this->_collection->elements());
    }

    public function testTrySwapWithNegativeIndices()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->assertTrue($this->_collection->trySwap(-1, -2));

        $this->assertSame(array('foo', 'bar', 'doom', 'spam'), $this->_collection->elements());
    }

    public function testTrySwapWithInvalidIndex1()
    {
        $this->assertFalse($this->_collection->trySwap(1, 2));
    }

    public function testTrySwapWithInvalidIndex2()
    {
        $this->_collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->assertFalse($this->_collection->trySwap(1, 100));
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function testCount()
    {
        $this->assertSame(0, count($this->_collection));

        $this->_collection->pushBack('foo');
        $this->_collection->pushBack('bar');
        $this->_collection->pushBack('spam');

        $this->assertSame(3, count($this->_collection));

        $this->_collection->clear();

        $this->assertSame(0, count($this->_collection));
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function testIteration()
    {
        $input = array(1, 2, 3, 4, 5);

        $this->_collection->append($input);

        $result = iterator_to_array($this->_collection);

        $this->assertSame($input, $result);
    }

    ///////////////////////////////////
    // Implementation of ArrayAccess //
    ///////////////////////////////////

    public function testOffsetExists()
    {
        $this->assertFalse(isset($this->_collection[0]));

        $this->_collection->pushBack('foo');

        $this->assertTrue(isset($this->_collection[0]));
    }

    public function testOffsetGet()
    {
        $this->_collection->pushBack('foo');

        $this->assertSame('foo', $this->_collection[0]);
    }

    public function testOffsetGetFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException', 'Index 0 is out of range.');

        $this->_collection[0];
    }

    public function testOffsetSet()
    {
        $this->_collection[] = 'foo';

        $this->assertSame(array('foo'), $this->_collection->elements());

        $this->_collection[0] = 'bar';

        $this->assertSame(array('bar'), $this->_collection->elements());
    }

    public function testOffsetUnset()
    {
        $this->_collection->append(array('foo', 'bar', 'spam'));

        unset($this->_collection[1]);

        $this->assertSame(array('foo', 'spam'), $this->_collection->elements());
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    public function testCapacity()
    {
        $this->assertSame(0, $this->_collection->capacity());

        $this->_collection->pushBack('foo');

        $this->assertSame(1, $this->_collection->capacity());

        $this->_collection->pushBack('foo');

        $this->assertSame(2, $this->_collection->capacity());

        $this->_collection->pushBack('foo');

        $this->assertSame(4, $this->_collection->capacity());

        $this->_collection->pushBack('foo');

        $this->assertSame(4, $this->_collection->capacity());
    }

    public function testReserve()
    {
        $this->_collection->reserve(10);
        $this->_collection->reserve(5);
        $this->assertSame(10, $this->_collection->capacity());
    }

    public function testShrink()
    {
        $this->_collection->reserve(10);

        $this->_collection->pushBack('foo');

        $this->_collection->shrink();

        $this->assertSame(1, $this->_collection->capacity());
    }
}
