<?php
namespace Icecave\Collections;

use ArrayIterator;
use LimitIterator;
use Icecave\Collections\Iterator\Traits;
use PHPUnit_Framework_TestCase;
use Phake;
use SplDoublyLinkedList;
use SplStack;
use SplQueue;
use SplMaxHeap;
use SplMinHeap;
use SplPriorityQueue;
use SplFixedArray;
use SplObjectStorage;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (isset($this->_array)) {
            return;
        }

        $this->_array = array(1, 2, 3);
        $this->_vector = new Vector($this->_array);
        $this->_map = new Map($this->_array);
        $this->_countable = new ArrayIterator($this->_array);
        $this->_traversable = Phake::partialMock('LimitIterator', $this->_countable);
    }

    public function tearDown()
    {
        // Iteration should only be performed once ...
        Phake::verify($this->_traversable, Phake::atMost(1))->rewind();
    }

    public function getCollections()
    {
        $this->setUp();

        return array(
            array($this->_array),
            array($this->_vector),
            array($this->_map),
            array($this->_countable),
            array($this->_traversable),
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

    public function testSizeNoIterationOfNonCountable()
    {
        $this->assertNull(Collection::size($this->_traversable, false));
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
    public function testFiltered($collection)
    {
        $result = Collection::filtered(
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
    public function testFilteredWithArrayAccess($collection)
    {
        $map = new Map;

        $result = Collection::filtered(
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
    public function testFilteredWithExplicitArray($collection)
    {
        $array = array();

        $result = Collection::filtered(
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
            array(new Traits(true,  false), $this->_countable),
            array(new Traits(false, false), $this->_traversable),
        );
    }
}
