<?php
namespace Icecave\Collections;

use Icecave\Collections\Iterator\Traits;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

abstract class AbstractLinkedListTest extends PHPUnit_Framework_TestCase
{
    abstract public function className();

    abstract public function verifyLinkIntegrity($collection);

    public function createCollection()
    {
        $reflector = new ReflectionClass($this->className());

        return $reflector->newInstanceArgs(func_get_args());
    }

    public function setUp()
    {
        $this->className = $this->className();
        $this->collection = $this->createCollection();
        $classNameAtoms = explode('\\', $this->className);
        $this->localClassName = end($classNameAtoms);
    }

    public function tearDown()
    {
        $this->verifyLinkIntegrity($this->collection);
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->collection->size());
    }

    public function testConstructorWithArray()
    {
        $this->collection = $this->createCollection(array(1, 2, 3));
        $this->assertSame(array(1, 2, 3), $this->collection->elements());
    }

    public function testClone()
    {
        $this->collection->pushBack(1);
        $this->collection->pushBack(2);
        $this->collection->pushBack(3);

        $collection = clone $this->collection;
        $collection->popBack();

        $this->assertSame(array(1, 2), $collection->elements());
        $this->assertSame(array(1, 2, 3), $this->collection->elements());

        $this->verifyLinkIntegrity($collection);
    }

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/71
     */
    public function testCloneDoesNotCorruptOriginalList()
    {
        $this->collection->pushBack('xxx');
        $this->collection->pushBack('yyy');
        $this->collection->pushBack('zzz');

        $collection = clone $this->collection;

        $collection->set(0, 'aaa');
        $collection->set(1, 'bbb');
        $collection->set(2, 'ccc');

        $this->assertSame(
            array('xxx', 'yyy', 'zzz'),
            $this->collection->elements()
        );
    }

    public function testCreate()
    {
        $method = [$this->className, 'create'];
        $this->collection = $method(1, 2, 3);

        $this->assertInstanceOf($this->className, $this->collection);
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
        $this->assertSame('<' . $this->localClassName . ' 0>', $this->collection->__toString());

        $this->collection->pushBack('foo');
        $this->collection->pushBack('bar');
        $this->collection->pushBack('spam');

        $this->assertSame('<' . $this->localClassName . ' 3 ["foo", "bar", "spam"]>', $this->collection->__toString());

        $this->collection->pushBack('doom');

        $this->assertSame('<' . $this->localClassName . ' 4 ["foo", "bar", "spam", ...]>', $this->collection->__toString());
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

        $this->assertInstanceOf($this->className, $result);
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

        $this->assertInstanceOf($this->className, $result);
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

        $this->assertInstanceOf($this->className, $result);
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

        $this->assertInstanceOf($this->className, $left);
        $this->assertSame(array(1, 2), $left->elements());

        $this->assertInstanceOf($this->className, $right);
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

        $this->assertInstanceOf($this->className, $result);
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

        $this->assertInstanceOf($this->className, $result);
        $this->assertSame(array(5, 4, 3, 2, 1), $result->elements());

        // Original should be unchanged.
        $this->assertSame(array(3, 2, 1, 5, 4), $this->collection->elements());
    }

    public function testReverse()
    {
        $this->collection->append(array(1, 2, 3, 4, 5));

        $result = $this->collection->reverse();

        $this->assertInstanceOf($this->className, $result);
        $this->assertSame(array(5, 4, 3, 2, 1), $result->elements());
    }

    public function testJoin()
    {
        $this->collection->append(array(1, 2, 3));

        $result = $this->collection->join(
            array(4, 5, 6),
            array(7, 8, 9)
        );

        $this->assertInstanceOf($this->className, $result);
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

    /**
     * @dataProvider getIndexOfData
     */
    public function testIndexOf($elements, $element, $begin, $end, $expectedIndex)
    {
        $this->collection->append($elements);

        $index = $this->collection->indexOf($element, $begin, $end);
        $this->assertSame($expectedIndex, $index);
    }

    /**
     * @dataProvider getIndexOfLastData
     */
    public function testIndexOfLast($elements, $element, $begin, $end, $expectedIndex)
    {
        $this->collection->append($elements);

        $index = $this->collection->indexOfLast($element, $begin, $end);
        $this->assertSame($expectedIndex, $index);
    }

    /**
     * @dataProvider getIndexOfData
     */
    public function testFind($elements, $element, $begin, $end, $expectedIndex)
    {
        $this->collection->append($elements);

        $predicate = function ($value) use ($element) {
            return $value === $element;
        };

        $index = $this->collection->find($predicate, $begin, $end);
        $this->assertSame($expectedIndex, $index);
    }

    /**
     * @dataProvider getIndexOfLastData
     */
    public function testFindLast($elements, $element, $begin, $end, $expectedIndex)
    {
        $this->collection->append($elements);

        $predicate = function ($value) use ($element) {
            return $value === $element;
        };

        $index = $this->collection->findLast($predicate, $begin, $end);
        $this->assertSame($expectedIndex, $index);
    }

    public function getIndexOfData()
    {
        $elements = array('foo', 'bar', 'spam', 'bar', 'doom');

        return array(
            'empty'          => array(array(),   'foo',  0, null, null),
            'match'          => array($elements, 'bar',  0, null, 1),
            'no match'       => array($elements, 'grob', 0, null, null),
            'begin index'    => array($elements, 'bar',  2, null, 3),
            'range match'    => array($elements, 'bar',  1, 3,    1),
            'range no match' => array($elements, 'bar',  2, 3,    null),
        );
    }

    public function getIndexOfLastData()
    {
        $elements = array('foo', 'bar', 'spam', 'bar', 'doom');

        return array(
            'empty'          => array(array(),   'foo',  0, null, null),
            'match'          => array($elements, 'bar',  0, null, 3),
            'no match'       => array($elements, 'grob', 0, null, null),
            'begin index'    => array($elements, 'bar',  2, null, 3),
            'range match'    => array($elements, 'bar',  1, 3,    1),
            'range no match' => array($elements, 'bar',  2, 3,    null),
        );
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

    public function testInsertRange()
    {
        $this->collection->append(array(1, 2, 3));

        $elements = $this->createCollection(array('a', 'b', 'c', 'd', 'e'));

        $this->collection->insertRange(1, $elements, 2, 4);

        $this->assertSame(array(1, 'c', 'd', 2, 3), $this->collection->elements());

        $this->verifyLinkIntegrity($elements);
    }

    public function testInsertRangeEmpty()
    {
        $this->collection->append(array(1, 2, 3));

        $elements = $this->createCollection(array('a', 'b', 'c', 'd', 'e'));

        $this->collection->insertRange(1, $elements, 2, 2);

        $this->assertSame(array(1, 2, 3), $this->collection->elements());

        $this->verifyLinkIntegrity($elements);
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

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/60
     */
    public function testNestedIterator()
    {
        $input = array(1, 2, 3);
        $output = array();

        $this->collection->append($input);

        foreach ($this->collection as $e) {
            foreach ($this->collection as $element) {
                $output[] = $element;
            }
        }

        $this->assertSame(array(1, 2, 3, 1, 2, 3, 1, 2, 3), $output);
    }

    ////////////////////////////////////////////////////////////////
    // Implementation of [Restricted|Extended]ComparableInterface //
    ////////////////////////////////////////////////////////////////

    /**
     * @dataProvider getCompareData
     */
    public function testCompare($lhs, $rhs, $expectedResult)
    {
        $lhs = $this->createCollection($lhs);
        $rhs = $this->createCollection($rhs);

        $cmp = $lhs->compare($rhs);

        if ($expectedResult < 0) {
            $this->assertLessThan(0, $cmp);
        } elseif ($expectedResult > 0) {
            $this->assertGreaterThan(0, $cmp);
        } else {
            $this->assertSame(0, $cmp);
        }

        $this->assertSame($expectedResult === 0, $lhs->isEqualTo($rhs));
        $this->assertSame($expectedResult === 0, $rhs->isEqualTo($lhs));

        $this->assertSame($expectedResult !== 0, $lhs->isNotEqualTo($rhs));
        $this->assertSame($expectedResult !== 0, $rhs->isNotEqualTo($lhs));

        $this->assertSame($expectedResult < 0, $lhs->isLessThan($rhs));
        $this->assertSame($expectedResult > 0, $rhs->isLessThan($lhs));

        $this->assertSame($expectedResult > 0, $lhs->isGreaterThan($rhs));
        $this->assertSame($expectedResult < 0, $rhs->isGreaterThan($lhs));

        $this->assertSame($expectedResult <= 0, $lhs->isLessThanOrEqualTo($rhs));
        $this->assertSame($expectedResult >= 0, $rhs->isLessThanOrEqualTo($lhs));

        $this->assertSame($expectedResult >= 0, $lhs->isGreaterThanOrEqualTo($rhs));
        $this->assertSame($expectedResult <= 0, $rhs->isGreaterThanOrEqualTo($lhs));
    }

    public function testCompareFailure()
    {
        $this->setExpectedException('Icecave\Parity\Exception\NotComparableException');
        $this->collection->compare(array());
    }

    public function testCanCompare()
    {
        $this->assertTrue($this->collection->canCompare($this->createCollection()));
        $this->assertFalse($this->collection->canCompare(array()));
    }

    public function getCompareData()
    {
        return array(
            'empty'         => array(array(),     array(),      0),
            'smaller'       => array(array(1),    array(1, 2), -1),
            'larger'        => array(array(1, 2), array(1),    +1),
            'same'          => array(array(1, 2), array(1, 2),  0),
            'lesser'        => array(array(1, 0), array(1, 1), -1),
            'greater'       => array(array(1, 1), array(1, 0), +1),
        );
    }
}
