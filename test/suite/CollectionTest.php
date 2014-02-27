<?php
namespace Icecave\Collections;

use ArrayIterator;
use Icecave\Collections\Iterator\Traits;
use Icecave\Collections\TestFixtures\UncountableIterator;
use LimitIterator;
use Phake;
use PHPUnit_Framework_TestCase;
use SplDoublyLinkedList;
use SplFixedArray;
use SplMaxHeap;
use SplMinHeap;
use SplObjectStorage;
use SplPriorityQueue;
use SplQueue;
use SplStack;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->array)) {
            return;
        }

        $this->array = array(1, 2, 3);
        $this->vector = new Vector($this->array);
        $this->map = new Map($this->array);
        $this->countable = new ArrayIterator($this->array);
        $this->traversable = Phake::partialMock('LimitIterator', $this->countable);
    }

    public function tearDown()
    {
        // Iteration should only be performed once ...
        Phake::verify($this->traversable, Phake::atMost(1))->rewind();
    }

    public function getCollections()
    {
        $this->setUp();

        return array(
            array($this->array),
            array($this->vector),
            array($this->map),
            array($this->countable),
            array($this->traversable),
        );
    }

    public function getCountableCollections()
    {
        $this->setUp();

        return array(
            array($this->array),
            array($this->vector),
            array($this->map),
            array($this->countable),
        );
    }

    /**
     * @dataProvider getCollections
     */
    public function testIsEmpty($collection)
    {
        $this->assertFalse(Collection::isEmpty($collection));
    }

    /**
     * @dataProvider getCollections
     */
    public function testSize($collection)
    {
        $this->assertSame(3, Collection::size($collection));
    }

    /**
     * @dataProvider getCountableCollections
     */
    public function testTrySize($collection)
    {
        $this->assertSame(3, Collection::trySize($collection));
    }

    public function testTrySizeFailure()
    {
        $this->assertNull(Collection::trySize($this->traversable));
    }

    /**
     * @dataProvider getCollections
     */
    public function testGet($collection)
    {
        $this->assertSame(2, Collection::get($collection, 1));
    }

    /**
     * @dataProvider getCollections
     */
    public function testGetFailure($collection)
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key 10 does not exist.');
        Collection::get($collection, 10);
    }

    /**
     * @dataProvider getCollections
     */
    public function testTryGet($collection)
    {
        $value = null;
        $this->assertTrue(Collection::tryGet($collection, 1, $value));
        $this->assertSame(2, $value);
    }

    /**
     * @dataProvider getCollections
     */
    public function testTryGetFailure($collection)
    {
        $value = null;
        $this->assertFalse(Collection::tryGet($collection, 10, $value));
        $this->assertNull($value);
    }

    /**
     * @dataProvider getCollections
     */
    public function testGetWithDefault($collection)
    {
        $this->assertSame(2, Collection::getWithDefault($collection, 1, 100));
    }

    /**
     * @dataProvider getCollections
     */
    public function testGetWithDefaultFailure($collection)
    {
        $this->assertSame(100, Collection::getWithDefault($collection, 10, 100));
    }

    /**
     * @dataProvider getCollections
     */
    public function testHasKey($collection)
    {
        $this->assertTrue(Collection::hasKey($collection, 2));
        $this->assertFalse(Collection::hasKey($collection, 100));
        $this->assertFalse(Collection::hasKey($collection, -10));
    }

    /**
     * @dataProvider getCollections
     */
    public function testContains($collection)
    {
        $this->assertTrue(Collection::contains($collection, 2));
        $this->assertFalse(Collection::contains($collection, 100));
    }

    /**
     * @dataProvider getCollections
     */
    public function testKeys($collection)
    {
        $this->assertSame(array(0, 1, 2), Collection::keys($collection));
    }

    /**
     * @dataProvider getCollections
     */
    public function testValues($collection)
    {
        $this->assertSame(array(1, 2, 3), Collection::values($collection));
    }

    /**
     * @dataProvider getCollections
     */
    public function testElements($collection)
    {
        $this->assertSame(array(array(0, 1), array(1, 2), array(2, 3)), Collection::elements($collection));
    }

    /**
     * @dataProvider getCollections
     */
    public function testMap($collection)
    {
        $result = Collection::map(
            $collection,
            function ($key, $value) {
                return array($key * 10, $value * 10);
            }
        );

        $expected = array(
            0 => 10,
            10 => 20,
            20 => 30,
        );

        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider getCollections
     */
    public function testMapWithArrayAccess($collection)
    {
        $map = new Map;

        $result = Collection::map(
            $collection,
            function ($key, $value) {
                return array($key * 10, $value * 10);
            },
            $map
        );

        $expected = new Map(
            array(
                0 => 10,
                10 => 20,
                20 => 30,
            )
        );

        $this->assertEquals($expected, $result);
        $this->assertSame($map, $result);
    }

    /**
     * @dataProvider getCollections
     */
    public function testMapWithExplicitArray($collection)
    {
        $array = array();

        $result = Collection::map(
            $collection,
            function ($key, $value) {
                return array($key * 10, $value * 10);
            },
            $array
        );

        $expected = array(
            0 => 10,
            10 => 20,
            20 => 30,
        );

        $this->assertSame($expected, $result);
        $this->assertSame($array, $result);
    }

    /**
     * @dataProvider getCollections
     */
    public function testFilter($collection)
    {
        $result = Collection::filter(
            $collection,
            function ($key, $value) {
                return $key % 2 === 0;
            }
        );

        $expected = array(
            0 => 1,
            2 => 3,
        );

        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider getCollections
     */
    public function testFilterWithArrayAccess($collection)
    {
        $map = new Map;

        $result = Collection::filter(
            $collection,
            function ($key, $value) {
                return $key % 2 === 0;
            },
            $map
        );

        $expected = new Map(
            array(
                0 => 1,
                2 => 3,
            )
        );

        $this->assertEquals($expected, $result);
        $this->assertSame($map, $result);
    }

    /**
     * @dataProvider getCollections
     */
    public function testFilterWithExplicitArray($collection)
    {
        $array = array();

        $result = Collection::filter(
            $collection,
            function ($key, $value) {
                return $key % 2 === 0;
            },
            $array
        );

        $expected = array(
            0 => 1,
            2 => 3,
        );

        $this->assertSame($expected, $result);
        $this->assertSame($array, $result);
    }

    /**
     * @dataProvider getCollections
     */
    public function testEach($collection)
    {
        $calls = array();

        Collection::each(
            $collection,
            function ($key, $value) use (&$calls) {
                $calls[] = func_get_args();
            }
        );

        $this->assertSame(array(array(0, 1), array(1, 2), array(2, 3)), $calls);
    }

    /**
     * @dataProvider getCollections
     */
    public function testAll($collection)
    {
        $calls = array();

        $this->assertTrue(
            Collection::all(
                $collection,
                function ($key, $value) {
                    return is_int($key);
                }
            )
        );

        $this->assertFalse(
            Collection::all(
                $collection,
                function ($key, $value) {
                    return $key > 1;
                }
            )
        );
    }

    /**
     * @dataProvider getCollections
     */
    public function testAny($collection)
    {
        $calls = array();

        $this->assertTrue(
            Collection::any(
                $collection,
                function ($key, $value) {
                    return $key > 1;
                }
            )
        );

        $this->assertFalse(
            Collection::all(
                $collection,
                function ($key, $value) {
                    return is_float($key);
                }
            )
        );
    }

    /**
     * @dataProvider getCollections
     */
    public function testIsSequential($collection)
    {
        $result   = Collection::isSequential($collection);
        $expected = !$collection instanceof Map;
        $this->assertSame($expected, $result);
    }

    public function testGetIterator()
    {
        $iterator = Collection::getIterator(array(1, 2, 3));
        $this->assertSame(array(1, 2, 3), iterator_to_array($iterator));

        $vector = new ArrayIterator(array(1, 2, 3));
        $this->assertInstanceOf('Iterator', $vector);
        $iterator = Collection::getIterator($vector);
        $this->assertSame(array(1, 2, 3), iterator_to_array($iterator));

        $set = new Set(array(1, 2, 3));
        $this->assertInstanceOf('IteratorAggregate', $set);
        $iterator = Collection::getIterator($set);
        $this->assertSame(array(1, 2, 3), iterator_to_array($iterator));
    }

    public function testGetIteratorFailure()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Could not produce an iterator for 123.'
        );

        Collection::getIterator(123);
    }

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/66
     */
    public function testGetIteratorWithNestedIteratorAggregate()
    {
        $level1 = Phake::mock('Iterator');
        $level2 = Phake::mock('IteratorAggregate');
        $level3 = Phake::mock('IteratorAggregate');

        Phake::when($level3)
            ->getIterator()
            ->thenReturn($level2);

        Phake::when($level2)
            ->getIterator()
            ->thenReturn($level1);

        $this->assertSame($level1, Collection::getIterator($level3));
    }

    public function testAddElementArray()
    {
        $collection = array('a');
        Collection::addElement($collection, 'b');
        Collection::addElements($collection, array('c', 'd'));

        $this->assertSame(array('a', 'b', 'c', 'd'), $collection);
    }

    public function testAddElementSequence()
    {
        $collection = Phake::mock(__NAMESPACE__ . '\MutableSequenceInterface');
        Collection::addElement($collection, 'a');
        Collection::addElements($collection, array('b', 'c'));

        Phake::inOrder(
            Phake::verify($collection)->pushBack('a'),
            Phake::verify($collection)->pushBack('b'),
            Phake::verify($collection)->pushBack('c')
        );
    }

    public function testAddElementSet()
    {
        $collection = Phake::mock(__NAMESPACE__ . '\SetInterface');
        Collection::addElement($collection, 'a');
        Collection::addElements($collection, array('b', 'c'));

        Phake::verify($collection)->add('a');
        Phake::verify($collection)->add('b');
        Phake::verify($collection)->add('c');
    }

    public function testAddElementQueuedAccess()
    {
        $collection = Phake::mock(__NAMESPACE__ . '\QueuedAccessInterface');
        Collection::addElement($collection, 'a');
        Collection::addElements($collection, array('b', 'c'));

        Phake::inOrder(
            Phake::verify($collection)->push('a'),
            Phake::verify($collection)->push('b'),
            Phake::verify($collection)->push('c')
        );
    }

    public function testAddElementArrayAccess()
    {
        $collection = Phake::mock('ArrayAccess');
        Collection::addElement($collection, 'a');
        Collection::addElements($collection, array('b', 'c'));

        Phake::inOrder(
            Phake::verify($collection)->offsetSet(null, 'a'),
            Phake::verify($collection)->offsetSet(null, 'b'),
            Phake::verify($collection)->offsetSet(null, 'c')
        );
    }

    /**
     * @dataProvider getImplodeData
     */
    public function testImplode($separator, $collection, $emptyResult, $transform, $expectedResult)
    {
        $result = Collection::implode($separator, $collection, $emptyResult, $transform);
        $this->assertSame($expectedResult, $result);
    }

    public function testImplodeDefaults()
    {
        $result = Collection::implode('.', array());
        $this->assertSame('', $result);

        $result = Collection::implode('.', array(1, 2, 3));
        $this->assertSame('1.2.3', $result);
    }

    public function getImplodeData()
    {
        return array(
            'empty'             => array(', ', array(),                                        '',        null,     ''),
            'custom fallback'   => array(', ', array(),                                        'foo',     null,     'foo'),
            'single element'    => array(', ', array('foo'),                                   '<empty>', null,     'foo'),
            'multiple elements' => array(', ', array('foo', 'bar', 'spam'),                    '<empty>', null,     'foo, bar, spam'),
            'transform'         => array(', ', array('foo', 'bar', 'spam'),                    '<empty>', 'strrev', 'oof, rab, maps'),
            'iterator'          => array(', ', new ArrayIterator(array('foo', 'bar', 'spam')), '<empty>', null,     'foo, bar, spam'),
        );
    }

    /**
     * @dataProvider getExplodeData
     */
    public function testExplode($separator, $string, $limit, $collection, $transform, $encoding, $expectedResult)
    {
        $result = Collection::explode($separator, $string, $limit, $collection, $transform, $encoding);
        $this->assertSame($expectedResult, $result);
    }

    public function testExplodeDefaults()
    {
        $result = Collection::explode(', ', 'foo, bar, spam');
        $this->assertSame(array('foo', 'bar', 'spam'), $result);
    }

    public function testExplodeNonArray()
    {
        $collection = new Vector;
        $result = Collection::explode(', ', 'foo, bar, spam', null, $collection);
        $this->assertSame($collection, $result);
        $this->assertSame(array('foo', 'bar', 'spam'), $collection->elements());
    }

    public function testExplodeInternalEncoding()
    {
        $previous = mb_internal_encoding();
        mb_internal_encoding('UTF-8');

        $result = Collection::explode(', ', "\xc3\xb6, \xc3\xb6, \xc3\xb6");
        $this->assertSame(array("\xc3\xb6", "\xc3\xb6", "\xc3\xb6"), $result);

        mb_internal_encoding($previous);
    }

    public function getExplodeData()
    {
        return array(
            'empty'              => array(', ', '',                 null, array(), null,     'ascii', array()),
            'single element'     => array(', ', 'foo',              null, array(), null,     'ascii', array('foo')),
            'multiple elements'  => array(', ', 'foo, bar, spam',   null, array(), null,     'ascii', array('foo', 'bar', 'spam')),
            'transform'          => array(', ', 'foo, bar, spam',   null, array(), 'strrev', 'ascii', array('oof', 'rab', 'maps')),
            'separator at start' => array(', ', ', foo, bar, spam', null, array(), null,     'ascii', array('', 'foo', 'bar', 'spam')),
            'separator at end'   => array(', ', 'foo, bar, spam, ', null, array(), null,     'ascii', array('foo', 'bar', 'spam', '')),
            'limit'              => array(', ', 'foo, bar, spam',   2,    array(), null,     'ascii', array('foo', 'bar, spam')),
        );
    }

    /**
     * @dataProvider getIterators
     */
    public function testIteratorTraits($expected, $iterator)
    {
        $this->assertEquals($expected, Collection::iteratorTraits($iterator));
    }

    public function testIteratorTraitsWithProvider()
    {
        $provider = Phake::mock(__NAMESPACE__ . '\Iterator\TraitsProviderInterface');

        $traits = new Traits(true, true);

        Phake::when($provider)
            ->iteratorTraits(Phake::anyParameters())
            ->thenReturn($traits);

        $this->assertSame($traits, Collection::iteratorTraits($provider));

        Phake::verify($provider)->iteratorTraits();
    }

    public function getIterators()
    {
        $this->setUp();

        return array(
            array(new Traits(true,  true),  array()),
            array(new Traits(true,  true),  new SplDoublyLinkedList),
            array(new Traits(true,  true),  new SplStack),
            array(new Traits(true,  true),  new SplQueue),
            array(new Traits(true,  true),  new SplMaxHeap),
            array(new Traits(true,  true),  new SplMinHeap),
            array(new Traits(true,  true),  new SplPriorityQueue),
            array(new Traits(true,  true),  new SplFixedArray),
            array(new Traits(true,  true),  new SplObjectStorage),
            array(new Traits(true,  false), $this->countable),
            array(new Traits(false, false), $this->traversable),
        );
    }

    /**
     * @dataProvider getCompareData
     */
    public function testCompare($lhs, $rhs, $expectedResult)
    {
        $cmp = Collection::compare($lhs, $rhs);

        if ($expectedResult < 0) {
            $this->assertLessThan(0, $cmp);
        } elseif ($expectedResult > 0) {
            $this->assertGreaterThan(0, $cmp);
        } else {
            $this->assertSame(0, $cmp);
        }
    }

    /**
     * @dataProvider getCompareData
     */
    public function testCompareWithCustomComparator($lhs, $rhs, $expectedResult)
    {
        $inverseComparator = function ($lhs, $rhs) {
            return $rhs - $lhs;
        };

        $cmp = Collection::compare($lhs, $rhs, $inverseComparator);

        if ($expectedResult < 0) {
            $this->assertGreaterThan(0, $cmp);
        } elseif ($expectedResult > 0) {
            $this->assertLessThan(0, $cmp);
        } else {
            $this->assertSame(0, $cmp);
        }
    }

    public function getCompareData()
    {
        $arrayData = array(
            'empty'         => array(array(),     array(),      0),
            'smaller'       => array(array(1),    array(1, 2), -1),
            'larger'        => array(array(1, 2), array(1),    +1),
            'same'          => array(array(1, 2), array(1, 2),  0),
            'lesser'        => array(array(1, 0), array(1, 1), -1),
            'greater'       => array(array(1, 1), array(1, 0), +1),
        );

        $data = array();

        foreach ($arrayData as $name => $parameters) {
            list($lhs, $rhs, $result) = $parameters;
            $data[$name . ', both uncountable'] = array(
                new UncountableIterator($lhs),
                new UncountableIterator($rhs),
                $result
            );

            $data[$name . ', lhs uncountable'] = array(
                new UncountableIterator($lhs),
                $rhs,
                $result
            );

            $data[$name . ', rhs uncountable'] = array(
                $lhs,
                new UncountableIterator($rhs),
                $result
            );
        }

        return array_merge($arrayData, $data);
    }

    /**
     * @dataProvider getLowerBoundData
     */
    public function testLowerBound($element, $begin, $end, $expectedIndex)
    {
        $comparator = function ($lhs, $rhs) {
            return $lhs - $rhs;
        };

        $index = Collection::lowerBound(array(10, 10, 20, 20, 30, 30, 40, 40, 50, 50, 60), $element, $comparator, $begin, $end);
        $this->assertSame($expectedIndex, $index);
    }

    public function getLowerBoundData()
    {
        return array(
            'not found, start'              => array(5,  0, null, 0),
            'found, start'                  => array(10, 0, null, 0),
            'not found, midway'             => array(15, 0, null, 2),
            'found, midway'                 => array(20, 0, null, 2),
            'not found, end'                => array(65, 0, null, 11),
            'found, end'                    => array(60, 0, null, 10),
            'found, sub-range'              => array(30, 2, 5,    4),
            'not found, sub-range'          => array(25, 2, 5,    4),
            'found, start of sub-range'     => array(20, 2, 5,    2),
            'not found, start of sub-range' => array(10, 2, 5,    2),
            'found, end of sub-range'       => array(30, 2, 5,    4),
            'not found, end of sub-range'   => array(60, 2, 5,    5),
        );
    }

    /**
     * @dataProvider getUpperBoundData
     */
    public function testUpperBound($element, $begin, $end, $expectedIndex)
    {
        $comparator = function ($lhs, $rhs) {
            return $lhs - $rhs;
        };

        $index = Collection::upperBound(array(10, 10, 20, 20, 30, 30, 40, 40, 50, 50, 60), $element, $comparator, $begin, $end);
        $this->assertSame($expectedIndex, $index);
    }

    public function getUpperBoundData()
    {
        return array(
            'not found, start'              => array(5,  0, null, 0),
            'found, start'                  => array(10, 0, null, 2),
            'not found, midway'             => array(15, 0, null, 2),
            'found, midway'                 => array(20, 0, null, 4),
            'not found, end'                => array(65, 0, null, 11),
            'found, end'                    => array(60, 0, null, 11),
            'found, sub-range'              => array(30, 2, 5,    5),
            'not found, sub-range'          => array(25, 2, 5,    4),
            'found, start of sub-range'     => array(20, 2, 5,    4),
            'not found, start of sub-range' => array(10, 2, 5,    2),
            'found, end of sub-range'       => array(30, 2, 5,    5),
            'not found, end of sub-range'   => array(60, 2, 5,    5),
        );
    }

    /**
     * @dataProvider getBinarySearchTerms
     */
    public function testBinarySearch($element, $begin, $end, $expectedIndex, $expectedInsertIndex)
    {
        $comparator = function ($lhs, $rhs) {
            return $lhs - $rhs;
        };

        $insertIndex = null;
        $index = Collection::binarySearch(array(10, 20, 30, 40, 50, 60, 70, 80, 90, 100), $element, $comparator, $begin, $end, $insertIndex);

        $this->assertSame($expectedIndex, $index);
        $this->assertSame($expectedInsertIndex, $insertIndex);
    }

    public function getBinarySearchTerms()
    {
        return array(
            'not found, start'              => array(5,   0, null, null, 0),
            'found, start'                  => array(10,  0, null, 0,    0),
            'not found, midway'             => array(15,  0, null, null, 1),
            'found, end'                    => array(100, 0, null, 9,    9),
            'not found, end'                => array(105, 0, null, null, 10),
            'found, sub-range'              => array(30,  1, 5,    2,    2),
            'not found, sub-range'          => array(80,  1, 5,    null, 5),
            'found, start of sub-range'     => array(20,  1, 5,    1,    1),
            'not found, start of sub-range' => array(10,  1, 5,    null, 1),
            'found, end of sub-range'       => array(50,  1, 5,    4,    4),
            'not found, end of sub-range'   => array(60,  1, 5,    null, 5),
        );
    }
}
