<?php
namespace Icecave\Collections;

use PHPUnit_Framework_TestCase;

class SetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_collection = new Set;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->_collection->size());
    }

    public function testConstructorWithArray()
    {
        $collection = new Set(array(1, 2, 3, 3, 4, 5));
        $this->assertSame(array(1, 2, 3, 4, 5), $collection->elements());
    }

    public function testClone()
    {
        $this->_collection->add(1);
        $this->_collection->add(2);
        $this->_collection->add(3);

        $collection = clone $this->_collection;

        $collection->remove(2);

        $this->assertSame(array(1, 3), $collection->elements());
        $this->assertSame(array(1, 2, 3), $this->_collection->elements());
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    public function testSize()
    {
        $this->assertSame(0, $this->_collection->size());

        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $this->assertSame(3, $this->_collection->size());

        $this->_collection->clear();

        $this->assertSame(0, $this->_collection->size());
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->_collection->isEmpty());

        $this->_collection->add('a');

        $this->assertFalse($this->_collection->isEmpty());

        $this->_collection->clear();

        $this->assertTrue($this->_collection->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('<Set 0>', $this->_collection->__toString());

        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $this->assertSame('<Set 3 ["a", "b", "c"]>', $this->_collection->__toString());

        $this->_collection->add('d');

        $this->assertSame('<Set 4 ["a", "b", "c", ...]>', $this->_collection->__toString());
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    public function testClear()
    {
        $this->_collection->add('a');

        $this->_collection->clear();

        $this->assertTrue($this->_collection->isEmpty());
    }

    /////////////////////////////////////////
    // Implementation of IterableInterface //
    /////////////////////////////////////////

    public function testElements()
    {
        $this->assertSame(array(), $this->_collection->elements());

        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $this->assertSame(array('a', 'b', 'c'), $this->_collection->elements());
    }

    public function testContains()
    {
        $this->assertFalse($this->_collection->contains('a'));

        $this->_collection->add('a');

        $this->assertTrue($this->_collection->contains('a'));
    }

    public function testFiltered()
    {
        $this->_collection->add('a');
        $this->_collection->add(null);
        $this->_collection->add('c');

        $result = $this->_collection->filtered();

        $this->assertInstanceOf(__NAMESPACE__ . '\Set', $result);
        $this->assertSame(array('a', 'c'), $result->elements());
    }

    public function testFilteredWithPredicate()
    {
        $this->_collection->add(1);
        $this->_collection->add(2);
        $this->_collection->add(3);
        $this->_collection->add(4);
        $this->_collection->add(5);

        $result = $this->_collection->filtered(
            function ($value) {
                return $value & 0x1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Set', $result);
        $this->assertSame(array(1, 3, 5), $result->elements());
    }

    public function testMap()
    {
        $this->_collection->add(1);
        $this->_collection->add(2);
        $this->_collection->add(3);

        $result = $this->_collection->map(
            function ($value) {
                return $value + 1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Set', $result);
        $this->assertSame(array(2, 3, 4), $result->elements());
    }

    ////////////////////////////////////////////////
    // Implementation of MutableIterableInterface //
    ////////////////////////////////////////////////

    public function testFilter()
    {
        $this->_collection->add('a');
        $this->_collection->add(null);
        $this->_collection->add('c');

        $this->_collection->filter();

        $this->assertSame(array('a', 'c'), $this->_collection->elements());
    }

    public function testFilterWithPredicate()
    {
        $this->_collection->add(1);
        $this->_collection->add(2);
        $this->_collection->add(3);
        $this->_collection->add(4);
        $this->_collection->add(5);

        $this->_collection->filter(
            function ($value) {
                return $value & 0x1;
            }
        );

        $this->assertSame(array(1, 3, 5), $this->_collection->elements());
    }

    public function testApply()
    {
        $this->_collection->add(1);
        $this->_collection->add(2);
        $this->_collection->add(3);

        $this->_collection->apply(
            function ($value) {
                return $value + 1;
            }
        );

        $this->assertSame(array(2, 3, 4), $this->_collection->elements());
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function testCount()
    {
        $this->assertSame(0, count($this->_collection));

        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $this->assertSame(3, count($this->_collection));

        $this->_collection->clear();

        $this->assertSame(0, count($this->_collection));
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function testIteration()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $result = iterator_to_array($this->_collection);

        $this->assertSame(array('a' => 'a', 'b' => 'b', 'c' => 'c'), $result);
    }

    ////////////////////////////
    // Model specific methods //
    ////////////////////////////

    public function testCascade()
    {
        $this->_collection->add('b');

        $this->assertSame('b', $this->_collection->cascade('a', 'b', 'c'));
    }

    public function testCascadeFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "c" does not exist.');

        $this->_collection->cascade('a', 'b', 'c');
    }

    public function testCascadeWithDefault()
    {
        $this->assertSame('<default>', $this->_collection->cascadeWithDefault('<default>', 'a', 'b', 'c'));

        $this->_collection->add('b');

        $this->assertSame('b', $this->_collection->cascadeWithDefault('<default>', 'a', 'b', 'c'));
    }

    public function testCascadeIterable()
    {
        $this->_collection->add('b');

        $this->assertSame('b', $this->_collection->cascadeIterable(array('a', 'b', 'c')));
    }

    public function testCascadeIterableWithDefault()
    {
        $this->assertSame('<default>', $this->_collection->cascadeIterableWithDefault('<default>', array('a', 'b', 'c')));

        $this->_collection->add('b');

        $this->assertSame('b', $this->_collection->cascadeIterableWithDefault('<default>', array('a', 'b', 'c')));
    }

    public function testAdd()
    {
        $this->assertFalse($this->_collection->contains('a'));

        $this->assertTrue($this->_collection->add('a'));

        $this->assertTrue($this->_collection->contains('a'));

        $this->assertFalse($this->_collection->add('a'));

        $this->assertTrue($this->_collection->contains('a'));
    }

    public function testRemove()
    {
        $this->assertFalse($this->_collection->remove('a'));

        $this->_collection->add('a');

        $this->assertTrue($this->_collection->remove('a'));

        $this->assertFalse($this->_collection->contains('a'));
    }

    public function testUnion()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->_collection->union($set);

        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $result->elements());
    }

    public function testUnionWithArray()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $result = $this->_collection->union($array);

        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $result->elements());
    }

    public function testIntersect()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->_collection->intersect($set);

        $this->assertSame(array('c'), $result->elements());
    }

    public function testIntersectWithArray()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $result = $this->_collection->intersect($array);

        $this->assertSame(array('c'), $result->elements());
    }

    public function testComplement()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $this->_collection->complement($set);

        $this->assertSame(array('a', 'b'), $result->elements());
    }

    public function testComplementWithArray()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $array = array(
            'c',
            'd',
            'e'
        );

        $result = $this->_collection->complement($array);

        $this->assertSame(array('a', 'b'), $result->elements());
    }
}
