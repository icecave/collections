<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use Icecave\Collections\Iterator\Traits;
use PHPUnit_Framework_TestCase;

class HashSetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->collection = new HashSet;
        $this->incompatibleCollection = new HashSet(null, 'sha1');
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->collection->size());
    }

    public function testConstructorWithArray()
    {
        $collection = new HashSet(array(1, 2, 3, 3, 4, 5));
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
        $collection = new HashSet(null, 'sha1');

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
        $this->assertSame('<HashSet 0>', $this->collection->__toString());

        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $this->assertSame('<HashSet 3 ["a", "b", "c"]>', $this->collection->__toString());

        $this->collection->add('d');

        $this->assertSame('<HashSet 4 ["a", "b", "c", ...]>', $this->collection->__toString());
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

        $this->assertInstanceOf(__NAMESPACE__ . '\HashSet', $result);
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

        $this->assertInstanceOf(__NAMESPACE__ . '\HashSet', $result);
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

        $this->assertInstanceOf(__NAMESPACE__ . '\HashSet', $result);
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

        $this->assertInstanceOf(__NAMESPACE__ . '\HashSet', $left);
        $this->assertSame(array(1, 2), $left->elements());

        $this->assertInstanceOf(__NAMESPACE__ . '\HashSet', $right);
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

    public function testIsEqualSet()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new HashSet(array('a', 'b', 'c'));
        $this->assertTrue($this->collection->isEqualSet($collection));

        $collection = new HashSet(array('a', 'b'));
        $this->assertFalse($this->collection->isEqualSet($collection));

        $collection = new HashSet(array('c', 'b', 'x'));
        $this->assertFalse($this->collection->isEqualSet($collection));
    }

    public function testIsEqualSetIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->isEqualSet($this->incompatibleCollection);
    }

    public function testIsSuperSet()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new HashSet(array('a', 'b', 'c'));
        $this->assertTrue($this->collection->isSuperSet($collection));

        $collection = new HashSet(array('a', 'b'));
        $this->assertTrue($this->collection->isSuperSet($collection));

        $collection = new HashSet(array('a', 'b', 'x'));
        $this->assertFalse($this->collection->isSuperSet($collection));

        $collection = new HashSet(array('a', 'b', 'c', 'd'));
        $this->assertFalse($this->collection->isSuperSet($collection));
    }

    public function testIsSuperSetIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->isSuperSet($this->incompatibleCollection);
    }

    public function testIsSubSet()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new HashSet(array('a', 'b', 'c'));
        $this->assertTrue($this->collection->isSubSet($collection));

        $collection = new HashSet(array('a', 'b'));
        $this->assertFalse($this->collection->isSubSet($collection));

        $collection = new HashSet(array('a', 'b', 'x'));
        $this->assertFalse($this->collection->isSubSet($collection));

        $collection = new HashSet(array('a', 'b', 'c', 'd'));
        $this->assertTrue($this->collection->isSubSet($collection));
    }

    public function testIsSubSetIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->isSubSet($this->incompatibleCollection);
    }

    public function testIsProperSuperSet()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new HashSet(array('a', 'b', 'c'));
        $this->assertFalse($this->collection->isProperSuperSet($collection));

        $collection = new HashSet(array('a', 'b'));
        $this->assertTrue($this->collection->isProperSuperSet($collection));

        $collection = new HashSet(array('a', 'x'));
        $this->assertFalse($this->collection->isProperSuperSet($collection));

        $collection = new HashSet(array('a', 'b', 'c', 'd'));
        $this->assertFalse($this->collection->isProperSuperSet($collection));
    }

    public function testIsProperSuperSetIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->isProperSuperSet($this->incompatibleCollection);
    }

    public function testIsProperSubSet()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new HashSet(array('a', 'b', 'c'));
        $this->assertFalse($this->collection->isProperSubSet($collection));

        $collection = new HashSet(array('a', 'b'));
        $this->assertFalse($this->collection->isProperSubSet($collection));

        $collection = new HashSet(array('a', 'x'));
        $this->assertFalse($this->collection->isProperSubSet($collection));

        $collection = new HashSet(array('a', 'b', 'c', 'd'));
        $this->assertTrue($this->collection->isProperSubSet($collection));
    }

    public function testIsProperSubSetIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->isProperSubSet($this->incompatibleCollection);
    }

    public function testIsIntersectingWithSubSet()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new HashSet(array('b', 'c', 'd'));

        $this->assertTrue($this->collection->isIntersecting($collection));
        $this->assertTrue($collection->isIntersecting($this->collection));
    }

    public function testIsIntersectingWithSuperSet()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new HashSet(array('a', 'b', 'c', 'd'));

        $this->assertTrue($this->collection->isIntersecting($collection));
        $this->assertTrue($collection->isIntersecting($this->collection));
    }

    public function testIsIntersectingWithExclusiveSets()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $collection = new HashSet(array('d', 'e', 'f'));

        $this->assertFalse($this->collection->isIntersecting($collection));
        $this->assertFalse($collection->isIntersecting($this->collection));
    }

    public function testIsIsIntersectingIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->isIntersecting($this->incompatibleCollection);
    }

    public function testUnion()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new HashSet;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->collection->union($set);
        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $result->elements());
    }

    public function testIsUnionIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->union($this->incompatibleCollection);
    }

    public function testUnionInPlace()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new HashSet;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $this->collection->unionInPlace($set);

        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $this->collection->elements());
    }

    public function testIsUnionInPlaceIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->unionInPlace($this->incompatibleCollection);
    }

    public function testIntersect()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new HashSet;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->collection->intersect($set);

        $this->assertSame(array('c'), $result->elements());
    }

    public function testIsIntersectIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->intersect($this->incompatibleCollection);
    }

    public function testIntersectInPlace()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new HashSet;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $this->collection->intersectInPlace($set);

        $this->assertSame(array('c'), $this->collection->elements());
    }

    public function testIsIntersectInPlaceIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->intersectInPlace($this->incompatibleCollection);
    }

    public function testDiff()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new HashSet;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->collection->diff($set);

        $this->assertSame(array('a', 'b'), $result->elements());
    }

    public function testDiffIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->diff($this->incompatibleCollection);
    }

    public function testDiffInPlace()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new HashSet;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $this->collection->diffInPlace($set);

        $this->assertSame(array('a', 'b'), $this->collection->elements());
    }

    public function testDiffInPlaceIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->diffInPlace($this->incompatibleCollection);
    }

    public function testSymmetricDiff()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new HashSet;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->collection->symmetricDiff($set);

        $this->assertSame(array('a', 'b', 'd', 'e'), $result->elements());
    }

    public function testSymmetricDiffIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->symmetricDiff($this->incompatibleCollection);
    }

    public function testSymmetricDiffInPlace()
    {
        $this->collection->add('a');
        $this->collection->add('b');
        $this->collection->add('c');

        $set = new HashSet;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $this->collection->symmetricDiffInPlace($set);

        $this->assertSame(array('a', 'b', 'd', 'e'), $this->collection->elements());
    }

    public function testSymmetricDiffInPlaceIncompatible()
    {
        $this->setExpectedException('InvalidArgumentException', 'The given set does not use the same hashing algorithm.');
        $this->collection->symmetricDiffInPlace($this->incompatibleCollection);
    }
}
