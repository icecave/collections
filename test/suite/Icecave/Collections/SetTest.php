<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use Icecave\Collections\Iterator\Traits;
use PHPUnit_Framework_TestCase;

class SetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->collection = new Set;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->collection->size());
    }

    public function testConstructorWithArray()
    {
        $collection = new Set(array(1, 2, 3, 3, 4, 5));
        $this->assertSame(array(1, 2, 3, 4, 5), $collection->elements());
    }

    public function testClone()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);

        $collection = clone $this->collection;

        $collection->remove(2);

        $this->assertSame(array(1, 3), $collection->elements());
        $this->assertSame(array(1, 2, 3), $this->collection->elements());
    }

    public function testSerialization()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);

        $packet = serialize($this->collection);
        $collection = unserialize($packet);

        $this->assertSame($this->collection->elements(), $collection->elements());
    }

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/23
     */
    public function testSerializationOfHashFunction()
    {
        $collection = new Set(null, 'sha1');

        $packet = serialize($collection);
        $collection = unserialize($packet);

        $this->assertSame('sha1', Liberator::liberate($collection)->hashFunction);
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    public function testSize()
    {
        $this->assertSame(0, $this->collection->size());

        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $this->assertSame(3, $this->collection->size());

        $this->collection->clear();

        $this->assertSame(0, $this->collection->size());
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->collection->isEmpty());

        $this->collection->add('a');

        $this->assertFalse($this->collection->isEmpty());

        $this->collection->clear();

        $this->assertTrue($this->collection->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('<Set 0>', $this->collection->__toString());

        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $this->assertSame('<Set 3 ["a", "b", "c"]>', $this->collection->__toString());

        $this->collection->add('d');

        $this->assertSame('<Set 4 ["a", "b", "c", ...]>', $this->collection->__toString());
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    public function testClear()
    {
        $this->collection->add('a');

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

        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $this->assertSame(array('a', 'b', 'c'), $this->collection->elements());
    }

    public function testContains()
    {
        $this->assertFalse($this->collection->contains('a'));

        $this->collection->add('a');

        $this->assertTrue($this->collection->contains('a'));
    }

    public function testFilter()
    {
        $this->collection->add('a');
        $this->collection->add(null);
        $this->collection->add('c');

        $result = $this->collection->filter();

        $this->assertInstanceOf(__NAMESPACE__ . '\Set', $result);
        $this->assertSame(array('a', 'c'), $result->elements());
    }

    public function testFilterWithPredicate()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);
        $this->collection->add(4);
        $this->collection->add(5);

        $result = $this->collection->filter(
            function ($value) {
                return $value & 0x1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Set', $result);
        $this->assertSame(array(1, 3, 5), $result->elements());
    }

    public function testMap()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);

        $result = $this->collection->map(
            function ($value) {
                return $value + 1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Set', $result);
        $this->assertSame(array(2, 3, 4), $result->elements());
    }

    public function testPartition()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);

        $result = $this->collection->partition(
            function ($element) {
                return $element < 3;
            }
        );

        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));

        list($left, $right) = $result;

        $this->assertInstanceOf(__NAMESPACE__ . '\Set', $left);
        $this->assertSame(array(1, 2), $left->elements());

        $this->assertInstanceOf(__NAMESPACE__ . '\Set', $right);
        $this->assertSame(array(3), $right->elements());
    }

    public function testEach()
    {
        $calls = array();
        $callback = function ($element) use (&$calls) {
            $calls[] = func_get_args();
        };

        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);

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
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);

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
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);

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
        $this->collection->add('a');
        $this->collection->add(null);
        $this->collection->add('c');

        $this->collection->filterInPlace();

        $this->assertSame(array('a', 'c'), $this->collection->elements());
    }

    public function testFilterInPlaceWithPredicate()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);
        $this->collection->add(4);
        $this->collection->add(5);

        $this->collection->filterInPlace(
            function ($value) {
                return $value & 0x1;
            }
        );

        $this->assertSame(array(1, 3, 5), $this->collection->elements());
    }

    public function testMapInPlace()
    {
        $this->collection->add(1);
        $this->collection->add(2);
        $this->collection->add(3);

        $this->collection->mapInPlace(
            function ($value) {
                return $value + 1;
            }
        );

        $this->assertSame(array(2, 3, 4), $this->collection->elements());
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function testCount()
    {
        $this->assertSame(0, count($this->collection));

        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $this->assertSame(3, count($this->collection));

        $this->collection->clear();

        $this->assertSame(0, count($this->collection));
    }

    /////////////////////////////////////////
    // Implementation of IteratorAggregate //
    /////////////////////////////////////////

    public function testIteration()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $result = iterator_to_array($this->collection);

        $this->assertSame(array(0 => 'a', 1 => 'b', 2 => 'c'), $result);
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    public function testCascade()
    {
        $this->collection->add('b');

        $this->assertSame('b', $this->collection->cascade('a', 'b', 'c'));
    }

    public function testCascadeFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "c" does not exist.');

        $this->collection->cascade('a', 'b', 'c');
    }

    public function testCascadeWithDefault()
    {
        $this->assertSame('<default>', $this->collection->cascadeWithDefault('<default>', 'a', 'b', 'c'));

        $this->collection->add('b');

        $this->assertSame('b', $this->collection->cascadeWithDefault('<default>', 'a', 'b', 'c'));
    }

    public function testCascadeIterable()
    {
        $this->collection->add('b');

        $this->assertSame('b', $this->collection->cascadeIterable(array('a', 'b', 'c')));
    }

    public function testCascadeIterableWithDefault()
    {
        $this->assertSame('<default>', $this->collection->cascadeIterableWithDefault('<default>', array('a', 'b', 'c')));

        $this->collection->add('b');

        $this->assertSame('b', $this->collection->cascadeIterableWithDefault('<default>', array('a', 'b', 'c')));
    }

    public function testAdd()
    {
        $this->assertFalse($this->collection->contains('a'));

        $this->assertTrue($this->collection->add('a'));

        $this->assertTrue($this->collection->contains('a'));

        $this->assertFalse($this->collection->add('a'));

        $this->assertTrue($this->collection->contains('a'));
    }

    public function testRemove()
    {
        $this->assertFalse($this->collection->remove('a'));

        $this->collection->add('a');

        $this->assertTrue($this->collection->remove('a'));

        $this->assertFalse($this->collection->contains('a'));
    }

    public function testIsEqual()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertTrue($this->collection->isEqual($collection));

        $collection->remove('b');

        $this->assertFalse($this->collection->isEqual($collection));

        $collection->add('b');
        $this->collection->remove('b');

        $this->assertFalse($this->collection->isEqual($collection));
    }

    public function testIsSuperset()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertTrue($this->collection->isSuperset($collection));

        $this->collection->add('d');

        $this->assertTrue($this->collection->isSuperset($collection));

        $this->collection->remove('a');

        $this->assertFalse($this->collection->isSuperset($collection));
    }

    public function testIsSubset()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertTrue($this->collection->isSubset($collection));

        $collection->add('d');

        $this->assertTrue($this->collection->isSubset($collection));

        $collection->remove('a');

        $this->assertFalse($this->collection->isSubset($collection));
    }

    public function testIsProperSuperset()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertFalse($this->collection->IsProperSuperset($collection));

        $this->collection->add('d');

        $this->assertTrue($this->collection->IsProperSuperset($collection));

        $this->collection->remove('a');

        $this->assertFalse($this->collection->IsProperSuperset($collection));
    }

    public function testIsProperSubset()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertFalse($this->collection->IsProperSubset($collection));

        $collection->add('d');

        $this->assertTrue($this->collection->IsProperSubset($collection));

        $collection->remove('a');

        $this->assertFalse($this->collection->IsProperSubset($collection));
    }

    public function testIsIntersectingWithSubset()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new Set;
        $collection->add('b');
        $collection->add('c');
        $collection->add('d');

        $this->assertTrue($this->collection->isIntersecting($collection));
        $this->assertTrue($collection->isIntersecting($this->collection));
    }

    public function testIsIntersectingWithSuperset()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new Set;
        $collection->add('a');
        $collection->add('b');
        $collection->add('c');
        $collection->add('d');

        $this->assertTrue($this->collection->isIntersecting($collection));
        $this->assertTrue($collection->isIntersecting($this->collection));
    }

    public function testIsIntersectingWithExclusiveSets()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new Set;
        $collection->add('d');
        $collection->add('e');
        $collection->add('f');

        $this->assertFalse($this->collection->isIntersecting($collection));
        $this->assertFalse($collection->isIntersecting($this->collection));
    }

    public function testUnion()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->collection->union($set);
        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $result->elements());
    }

    public function testUnionWithArray()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $result = $this->collection->union($array);

        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $result->elements());
    }

    public function testUnionInPlace()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $this->collection->unionInPlace($set);

        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $this->collection->elements());
    }

    public function testUnionInPlaceWithArray()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $this->collection->unionInPlace($array);

        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $this->collection->elements());
    }

    public function testIntersect()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->collection->intersect($set);

        $this->assertSame(array('c'), $result->elements());
    }

    public function testIntersectWithArray()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $result = $this->collection->intersect($array);

        $this->assertSame(array('c'), $result->elements());
    }

    public function testIntersectInPlace()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $this->collection->intersectInPlace($set);

        $this->assertSame(array('c'), $this->collection->elements());
    }

    public function testIntersectInPlaceWithArray()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $this->collection->intersectInPlace($array);

        $this->assertSame(array('c'), $this->collection->elements());
    }

    public function testDiff()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->collection->diff($set);

        $this->assertSame(array('a', 'b'), $result->elements());
    }

    public function testDiffWithArray()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $result = $this->collection->diff($array);

        $this->assertSame(array('a', 'b'), $result->elements());
    }

    public function testDiffInPlace()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $this->collection->diffInPlace($set);

        $this->assertSame(array('a', 'b'), $this->collection->elements());
    }

    public function testDiffInPlaceWithArray()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $this->collection->diffInPlace($array);

        $this->assertSame(array('a', 'b'), $this->collection->elements());
    }

    public function testSymmetricDiff()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->collection->symmetricDiff($set);

        $this->assertSame(array('a', 'b', 'd', 'e'), $result->elements());
    }

    public function testSymmetricDiffWithArray()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $result = $this->collection->symmetricDiff($array);

        $this->assertSame(array('a', 'b', 'd', 'e'), $result->elements());
    }

    public function testSymmetricDiffInPlace()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $this->collection->symmetricDiffInPlace($set);

        $this->assertSame(array('a', 'b', 'd', 'e'), $this->collection->elements());
    }

    public function testSymmetricDiffInPlaceWithArray()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $this->collection->symmetricDiffInPlace($array);

        $this->assertSame(array('a', 'b', 'd', 'e'), $this->collection->elements());
    }
}
