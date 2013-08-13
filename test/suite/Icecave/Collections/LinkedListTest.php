<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use Icecave\Collections\Iterator\Traits;
use PHPUnit_Framework_TestCase;

class LinkedListTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->collection = new LinkedList;
    }

    public function tearDown()
    {
        // Verify size ...
        $this->assertSame(count($this->collection->elements()), $this->collection->size());

        // Verify tail node ...
        $head = Liberator::liberate($this->collection)->head;
        $tail = Liberator::liberate($this->collection)->tail;

        for ($node = $head; $node && null !== $node->next; $node = $node->next) {
            // no-op ...
        }
        $this->assertSame($tail, $node);
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->collection->size());
    }

    public function testConstructorWithArray()
    {
        $collection = new LinkedList(array(1, 2, 3));
        $this->assertSame(array(1, 2, 3), $collection->elements());
    }

    public function testClone()
    {
        $this->collection->pushBack(1);
        $this->collection->pushBack(2);
        $this->collection->pushBack(3);

        $this->collection->rewind();
        $this->collection->next();

        $collection = clone $this->collection;

        // Check that currentNode is updated correctly ...
        $this->assertSame(2, $collection->current());
        $this->assertSame(2, $this->collection->current());

        $collection->popBack();

        $this->assertSame(array(1, 2), $collection->elements());
        $this->assertSame(array(1, 2, 3), $this->collection->elements());
    }

    public function testSerialization()
    {
        $this->collection->pushBack(1);
        $this->collection->pushBack(2);
        $this->collection->pushBack(3);

        $packet = serialize($this->collection);
        $collection = unserialize($packet);

        $this->assertSame($this->collection->elements(), $collection->elements());
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    public function testSize()
    {
        $this->assertSame(0, $this->collection->size());

        $this->collection->pushBack('foo');
        $this->collection->pushBack('bar');
        $this->collection->pushBack('spam');

        $this->assertSame(3, $this->collection->size());

        $this->collection->clear();

        $this->assertSame(0, $this->collection->size());
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->collection->isEmpty());

        $this->collection->pushBack('foo');

        $this->assertFalse($this->collection->isEmpty());

        $this->collection->clear();

        $this->assertTrue($this->collection->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('<LinkedList 0>', $this->collection->__toString());

        $this->collection->pushBack('foo');
        $this->collection->pushBack('bar');
        $this->collection->pushBack('spam');

        $this->assertSame('<LinkedList 3 ["foo", "bar", "spam"]>', $this->collection->__toString());

        $this->collection->pushBack('doom');

        $this->assertSame('<LinkedList 4 ["foo", "bar", "spam", ...]>', $this->collection->__toString());
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    public function testClear()
    {
        $this->collection->pushBack('foo');

        $this->collection->clear();

        $this->assertTrue($this->collection->isEmpty());
    }

    //////////////////////////////////////////////
    // Implementation of IteratorTraitsProvider //
    //////////////////////////////////////////////

    public function testIteratorTraits()
    {
        $this->assertEquals(new Traits(true, true), $this->collection->iteratorTraits());
    }

    /////////////////////////////////////////
    // Implementation of IterableInterface //
    /////////////////////////////////////////

    public function testElements()
    {
        $this->assertSame(array(), $this->collection->elements());

        $this->collection->pushBack('foo');
        $this->collection->pushBack('bar');
        $this->collection->pushBack('spam');

        $this->assertSame(array('foo', 'bar', 'spam'), $this->collection->elements());
    }

    public function testContains()
    {
        $this->assertFalse($this->collection->contains('foo'));

        $this->collection->pushBack('foo');
        $this->collection->pushBack('bar');

        $this->assertTrue($this->collection->contains('bar'));
    }

    public function testFilter()
    {
        $this->collection->append(array(1, null, 2, null, 3));

        $result = $this->collection->filter();

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $result);
        $this->assertSame(array(1, 2, 3), $result->elements());
    }

    public function testFilterWithPredicate()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->filter(
            function ($element) {
                return $element & 0x1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $result);
        $this->assertSame(array(1, 3, 5), $result->elements());
    }

    public function testMap()
    {
        $this->collection->append(array(1, 2, 3));

        $result = $this->collection->map(
            function ($element) {
                return $element + 1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $result);
        $this->assertSame(array(2, 3, 4), $result->elements());
    }

    public function testPartition()
    {
        $this->collection->append(array(1, 2, 3));

        $result = $this->collection->partition(
            function ($element) {
                return $element < 3;
            }
        );

        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));

        list($left, $right) = $result;

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $left);
        $this->assertSame(array(1, 2), $left->elements());

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $right);
        $this->assertSame(array(3), $right->elements());
    }

    public function testEach()
    {
        $calls = array();
        $callback = function ($element) use (&$calls) {
            $calls[] = func_get_args();
        };

        $this->collection->append(array(1, 2, 3));

        $this->collection->each($callback);

        $expected = array(
            array(1),
            array(2),
            array(3),
        );

        $this->assertSame($expected, $calls);
    }

    public function testAll()
    {
        $this->collection->append(array(1, 2, 3));

        $this->assertTrue(
            $this->collection->all(
                function ($element) {
                    return is_int($element);
                }
            )
        );

        $this->assertFalse(
            $this->collection->all(
                function ($element) {
                    return $element > 2;
                }
            )
        );
    }

    public function testAny()
    {
        $this->collection->append(array(1, 2, 3));

        $this->assertTrue(
            $this->collection->any(
                function ($element) {
                    return $element > 2;
                }
            )
        );

        $this->assertFalse(
            $this->collection->any(
                function ($element) {
                    return is_float($element);
                }
            )
        );
    }

    ////////////////////////////////////////////////
    // Implementation of MutableIterableInterface //
    ////////////////////////////////////////////////

    public function testFilterInPlace()
    {
        $this->collection->append(array(null, 1, null, 2, null, 3));

        $this->collection->filterInPlace();

        $this->assertSame(array(1, 2, 3), $this->collection->elements());
    }

    public function testFilterInPlaceWithPredicate()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $this->collection->filterInPlace(
            function ($element) {
                return $element & 0x1;
            }
        );

        $this->assertSame(array(1, 3, 5), $this->collection->elements());
    }

    public function testMapInPlace()
    {
        $this->collection->append(array(1, 2, 3));

        $this->collection->mapInPlace(
            function ($element) {
                return $element + 1;
            }
        );

        $this->assertSame(array(2, 3, 4), $this->collection->elements());
    }

    /////////////////////////////////////////
    // Implementation of SequenceInterface //
    /////////////////////////////////////////

    public function testFront()
    {
        $this->collection->append(array('foo', 'bar'));

        $this->assertSame('foo', $this->collection->front());
    }

    public function testFrontWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->collection->front();
    }

    public function testTryFront()
    {
        $this->collection->append(array('foo', 'bar'));

        $element = null;
        $this->assertTrue($this->collection->tryFront($element));
        $this->assertSame('foo', $element);
    }

    public function testTryFrontWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->collection->tryFront($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testBack()
    {
        $this->collection->append(array('foo', 'bar'));
        $this->assertSame('bar', $this->collection->back());
    }

    public function testBackWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->collection->back();
    }

    public function testTryBack()
    {
        $this->collection->append(array('foo', 'bar'));

        $element = null;
        $this->assertTrue($this->collection->tryBack($element));
        $this->assertSame('bar', $element);
    }

    public function testTryBackWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->collection->tryBack($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testSort()
    {
        $this->collection->append(array(3, 2, 1, 5, 4));

        $result = $this->collection->sort();

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $result);
        $this->assertSame(array(1, 2, 3, 4, 5), $result->elements());

        // Original should be unchanged.
        $this->assertSame(array(3, 2, 1, 5, 4), $this->collection->elements());
    }

    public function testSortWithComparator()
    {
        $this->collection->append(array(3, 2, 1, 5, 4));

        $result = $this->collection->sort(
            function ($a, $b) {
                return $b - $a;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $result);
        $this->assertSame(array(5, 4, 3, 2, 1), $result->elements());

        // Original should be unchanged.
        $this->assertSame(array(3, 2, 1, 5, 4), $this->collection->elements());
    }

    public function testReverse()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->reverse();

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $result);
        $this->assertSame(array(5, 4, 3, 2, 1), $result->elements());
    }

    public function testJoin()
    {
        $this->collection->append(array(1, 2, 3));

        $result = $this->collection->join(
            array(4, 5, 6),
            array(7, 8, 9)
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\LinkedList', $result);
        $this->assertSame(array(1, 2, 3, 4, 5, 6, 7, 8, 9), $result->elements());
    }

    ////////////////////////////////////////////////
    // Implementation of MutableSequenceInterface //
    ////////////////////////////////////////////////

    public function testSortInPlace()
    {
        $this->collection->append(array(4, 3, 2, 1, 5, 4));

        $this->collection->sortInPlace();

        $this->assertSame(array(1, 2, 3, 4, 4, 5), $this->collection->elements());
    }

    public function testSortInPlaceWithComparator()
    {
        $this->collection->append(array(4, 3, 2, 1, 5, 4));

        $this->collection->sortInPlace(
            function ($a, $b) {
                return $b - $a;
            }
        );

        $this->assertSame(array(5, 4, 4, 3, 2, 1), $this->collection->elements());
    }

    public function testSortInPlaceWithEmptyCollection()
    {
        $this->collection->sortInPlace();

        $this->assertSame(array(), $this->collection->elements());
    }

    public function testSortInPlaceWithSingleElement()
    {
        $this->collection->pushBack(1);

        $this->collection->sortInPlace();

        $this->assertSame(array(1), $this->collection->elements());
    }

    public function testReverseInPlace()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $this->collection->reverseInPlace();

        $this->assertSame(array(5, 4, 3, 2, 1), $this->collection->elements());
    }

    public function testAppend()
    {
        $this->collection->append(
            array(1, 2, 3),
            array(4, 5, 6)
        );

        $this->assertSame(array(1, 2, 3, 4, 5, 6), $this->collection->elements());
    }

    public function testPushFront()
    {
        $this->collection->pushFront(1);
        $this->collection->pushFront(2);
        $this->collection->pushFront(3);

        $this->assertSame(array(3, 2, 1), $this->collection->elements());
    }

    public function testPopFront()
    {
        $this->collection->append(array(1, 2, 3));

        $this->assertSame(1, $this->collection->popFront());
        $this->assertSame(array(2, 3), $this->collection->elements());
    }

    public function testPopFrontLastElement()
    {
        $this->collection->append(array(1));

        $this->assertSame(1, $this->collection->popFront());
        $this->assertSame(array(), $this->collection->elements());
    }

    public function testPopFrontWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->collection->popFront();
    }

    public function testTryPopFront()
    {
        $this->collection->append(array(1, 2, 3));

        $element = null;
        $this->assertTrue($this->collection->tryPopFront($element));
        $this->assertSame(1, $element);
        $this->assertSame(array(2, 3), $this->collection->elements());
    }

    public function testTryPopFrontWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->collection->tryPopFront($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testPushBack()
    {
        $this->collection->pushBack(1);
        $this->collection->pushBack(2);
        $this->collection->pushBack(3);

        $this->assertSame(array(1, 2, 3), $this->collection->elements());
    }

    public function testPopBack()
    {
        $this->collection->append(array(1, 2, 3));

        $this->assertSame(3, $this->collection->popBack());
        $this->assertSame(array(1, 2), $this->collection->elements());
    }

    public function testPopBackLastElement()
    {
        $this->collection->append(array(1));

        $this->assertSame(1, $this->collection->popBack());
        $this->assertSame(array(), $this->collection->elements());
    }

    public function testPopBackWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->collection->popBack();
    }

    public function testTryPopBack()
    {
        $this->collection->append(array(1, 2, 3));

        $element = null;
        $this->assertTrue($this->collection->tryPopBack($element));
        $this->assertSame(3, $element);
        $this->assertSame(array(1, 2), $this->collection->elements());
    }

    public function testTryPopBackWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->collection->tryPopBack($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testResize()
    {
        $this->collection->resize(3);

        $this->assertSame(array(null, null, null), $this->collection->elements());
    }

    public function testResizeWithValue()
    {
        $this->collection->resize(3, 'foo');

        $this->assertSame(array('foo', 'foo', 'foo'), $this->collection->elements());
    }

    public function testResizeToSmallerSize()
    {
        $this->collection->append(array(1, 2, 3));

        $this->collection->resize(2);

        $this->assertSame(array(1, 2), $this->collection->elements());
    }

    //////////////////////////////////////////////
    // Implementation of RandomAccessInterface //
    /////////////////////////////////////////////

    public function testGet()
    {
        $this->collection->append(array(1, 2, 3));

        $this->assertSame(2, $this->collection->get(1));
    }

    public function testGetWithNegativeIndex()
    {
        $this->collection->append(array(1, 2, 3));

        $this->assertSame(3, $this->collection->get(-1));
    }

    public function testGetWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->get(0);
    }

    public function testSlice()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->slice(2);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(3, 4, 5), $result->elements());
    }

    public function testSliceWithCount()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->slice(1, 3);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(2, 3, 4), $result->elements());
    }

    public function testSliceWithNegativeCount()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->slice(1, -3);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(), $result->elements());
    }

    public function testSliceWithNegativeIndex()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->slice(-2);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(4, 5), $result->elements());
    }

    public function testSliceWithNegativeIndexAndCount()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->slice(-3, 2);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(3, 4), $result->elements());
    }

    public function testRange()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->range(1, 3);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(2, 3), $result->elements());
    }

    public function testRangeWithNegativeIndices()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->range(-3, -1);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(3, 4), $result->elements());
    }

    public function testRangeWithEndBeforeBegin()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->range(3, 1);

        $this->assertInstanceOf(__NAMESPACE__ . '\SequenceInterface', $result);
        $this->assertSame(array(), $result->elements());
    }

    public function testIndexOf()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(1, $this->collection->indexOf('bar'));
    }

    public function testIndexOfWithStartIndex()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(1, $this->collection->indexOf('bar', 1));

        $this->collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(3, $this->collection->indexOf('bar', 2));
    }

    public function testIndexOfWithNoMatch()
    {
        $this->assertNull($this->collection->indexOf('foo'));

        $this->collection->pushBack('bar');
        $this->assertNull($this->collection->indexOf('foo'));
    }

    public function testIndexOfLast()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(3, $this->collection->indexOfLast('bar'));
    }

    public function testIndexOfLastWithStartIndex()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(3, $this->collection->indexOfLast('bar', 3));

        $this->collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(1, $this->collection->indexOfLast('bar', 2));
    }

    public function testIndexOfLastWithNoMatch()
    {
        $this->assertNull($this->collection->indexOfLast('foo'));

        $this->collection->pushBack('bar');
        $this->assertNull($this->collection->indexOfLast('foo'));
    }

    public function testFind()
    {
        $comparator = function ($element) {
            return $element === 'bar';
        };

        $this->collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(1, $this->collection->find($comparator));
    }

    public function testFindLast()
    {
        $comparator = function ($element) {
            return $element === 'bar';
        };

        $this->collection->append(array('foo', 'bar', 'spam', 'bar', 'doom'));
        $this->assertSame(3, $this->collection->findLast($comparator));
    }

    ////////////////////////////////////////////////////
    // Implementation of MutableRandomAccessInterface //
    ////////////////////////////////////////////////////

    public function testSet()
    {
        $this->collection->append(array('foo', 'bar', 'spam'));

        $this->collection->set(1, 'goose');

        $this->assertSame(array('foo', 'goose', 'spam'), $this->collection->elements());
    }

    public function testSetWithNegativeIndex()
    {
        $this->collection->append(array('foo', 'bar', 'spam'));

        $this->collection->set(-2, 'goose');

        $this->assertSame(array('foo', 'goose', 'spam'), $this->collection->elements());
    }

    public function testSetWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->set(0, 'bar');
    }

    public function testInsert()
    {
        $this->collection->append(array('foo', 'spam'));

        $this->collection->insert(1, 'bar');

        $this->assertSame(array('foo', 'bar', 'spam'), $this->collection->elements());
    }

    public function testInsertWithNegativeIndex()
    {
        $this->collection->append(array('foo', 'spam'));

        $this->collection->insert(-1, 'bar');

        $this->assertSame(array('foo', 'bar', 'spam'), $this->collection->elements());
    }

    public function testInsertAtEnd()
    {
        $this->collection->append(array('foo', 'spam'));

        $this->collection->insert($this->collection->size(), 'bar');

        $this->assertSame(array('foo', 'spam', 'bar'), $this->collection->elements());
    }

    public function testInsertWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->insert(1, 'foo');
    }

    public function testInsertMany()
    {
        $this->collection->append(array('foo', 'spam'));

        $this->collection->insertMany(1, array('bar', 'frob'));

        $this->assertSame(array('foo', 'bar', 'frob', 'spam'), $this->collection->elements());
    }

    public function testInsertManyAtBegin()
    {
        $this->collection->append(array('foo', 'spam'));

        $this->collection->insertMany(0, array('bar', 'frob'));

        $this->assertSame(array('bar', 'frob', 'foo', 'spam'), $this->collection->elements());
    }

    public function testInsertManyAtEnd()
    {
        $this->collection->append(array('foo', 'spam'));

        $this->collection->insertMany($this->collection->size(), array('bar', 'frob'));

        $this->assertSame(array('foo', 'spam', 'bar', 'frob'), $this->collection->elements());
    }

    public function testInsertManyWithEmptyElements()
    {
        $this->collection->append(array('foo', 'spam'));

        $this->collection->insertMany(1, array());

        $this->assertSame(array('foo', 'spam'), $this->collection->elements());
    }

    public function testInsertManyWithNegativeIndex()
    {
        $this->collection->append(array('foo', 'spam'));

        $this->collection->insertMany(-1, array('bar', 'frob'));

        $this->assertSame(array('foo', 'bar', 'frob', 'spam'), $this->collection->elements());
    }

    public function testInsertManyWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->insertMany(1, array('bar', 'frob'));
    }

    public function testRemove()
    {
        $this->collection->append(array('foo', 'bar', 'spam'));

        $this->collection->remove(1);

        $this->assertSame(array('foo', 'spam'), $this->collection->elements());
    }

    public function testRemoveWithNegativeIndex()
    {
        $this->collection->append(array('foo', 'bar', 'spam'));

        $this->collection->remove(-2);

        $this->assertSame(array('foo', 'spam'), $this->collection->elements());
    }

    public function testRemoveWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->remove(1);
    }

    public function testRemoveMany()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->removeMany(1);

        $this->assertSame(array('foo'), $this->collection->elements());
    }

    public function testRemoveManyEverything()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->removeMany(0);

        $this->assertSame(array(), $this->collection->elements());
    }

    public function testRemoveManyWithCount()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->removeMany(1, 2);

        $this->assertSame(array('foo', 'doom'), $this->collection->elements());
    }

    public function testRemoveManyWithCountToEnd()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->removeMany(1, 3);

        $this->assertSame(array('foo'), $this->collection->elements());
    }

    public function testRemoveManyWithNegativeIndex()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->removeMany(-3, 2);

        $this->assertSame(array('foo', 'doom'), $this->collection->elements());
    }

    public function testRemoveManyWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->removeMany(1, 2);
    }

    public function testRemoveRange()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom', 'frob'));

        $this->collection->removeRange(1, 3);

        $this->assertSame(array('foo', 'doom', 'frob'), $this->collection->elements());
    }

    public function testRemoveRangeToEnd()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->removeRange(1, 3);

        $this->assertSame(array('foo', 'doom'), $this->collection->elements());
    }

    public function testRemoveRangeWithNegativeIndex()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->removeRange(-3, -1);

        $this->assertSame(array('foo', 'doom'), $this->collection->elements());
    }

    public function testRemoveRangeWithEndBeforeBegin()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->removeRange(3, 1);

        $this->assertSame(array('foo', 'bar', 'spam', 'doom'), $this->collection->elements());
    }

    public function testRemoveRangeWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->removeRange(1, 2);
    }

    public function testReplace()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->replace(1, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b'), $this->collection->elements());
    }

    public function testReplaceWithCount()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->replace(1, array('a', 'b'), 2);

        $this->assertSame(array('foo', 'a', 'b', 'doom'), $this->collection->elements());
    }

    public function testReplaceWithNegativeIndex()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->replace(-3, array('a', 'b'), 2);

        $this->assertSame(array('foo', 'a', 'b', 'doom'), $this->collection->elements());
    }

    public function testReplaceWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->replace(1, array());
    }

    public function testReplaceRange()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->replaceRange(1, 3, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b', 'doom'), $this->collection->elements());
    }

    public function testReplaceRangeWithNegativeIndices()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->replaceRange(-3, -1, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b', 'doom'), $this->collection->elements());
    }

    public function testReplaceRangeWithZeroLength()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->replaceRange(1, 1, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b', 'bar', 'spam', 'doom'), $this->collection->elements());
    }

    public function testReplaceRangeWithEndBeforeBegin()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->replaceRange(1, 0, array('a', 'b'));

        $this->assertSame(array('foo', 'a', 'b', 'bar', 'spam', 'doom'), $this->collection->elements());
    }

    public function testReplaceRangeWithInvalidIndex()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->replaceRange(1, 2, array());
    }

    public function testSwap()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->swap(1, 2);

        $this->assertSame(array('foo', 'spam', 'bar', 'doom'), $this->collection->elements());
    }

    public function testSwapWithNegativeIndices()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->collection->swap(-1, -2);

        $this->assertSame(array('foo', 'bar', 'doom', 'spam'), $this->collection->elements());
    }

    public function testSwapWithInvalidIndex1()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->swap(1, 2);
    }

    public function testSwapWithInvalidIndex2()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->setExpectedException(__NAMESPACE__ . '\Exception\IndexException');
        $this->collection->swap(1, 100);
    }

    public function testTrySwap()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->assertTrue($this->collection->trySwap(1, 2));

        $this->assertSame(array('foo', 'spam', 'bar', 'doom'), $this->collection->elements());
    }

    public function testTrySwapWithNegativeIndices()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->assertTrue($this->collection->trySwap(-1, -2));

        $this->assertSame(array('foo', 'bar', 'doom', 'spam'), $this->collection->elements());
    }

    public function testTrySwapWithInvalidIndex1()
    {
        $this->assertFalse($this->collection->trySwap(1, 2));
    }

    public function testTrySwapWithInvalidIndex2()
    {
        $this->collection->append(array('foo', 'bar', 'spam', 'doom'));

        $this->assertFalse($this->collection->trySwap(1, 100));
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function testCount()
    {
        $this->assertSame(0, count($this->collection));

        $this->collection->pushBack('foo');
        $this->collection->pushBack('bar');
        $this->collection->pushBack('spam');

        $this->assertSame(3, count($this->collection));

        $this->collection->clear();

        $this->assertSame(0, count($this->collection));
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function testIteration()
    {
        $input = array(1, 2, 3, 4, 5);

        $this->collection->append($input);

        $result = iterator_to_array($this->collection);

        $this->assertSame($input, $result);
    }
}
