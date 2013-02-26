<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class SetTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_validatedElements = array();
        $this->_remainingPassingValidations = PHP_INT_MAX;

        $self = $this;
        $this->_validator = function ($element) use ($self) {
            $self->_validatedElements[] = $element;

            return $self->_remainingPassingValidations-- > 0;
        };

        $this->_collection = new Set(null, $this->_validator);
    }

    /**
     * A helper method to ensure that the collection's element state is not change after an operation
     * that produces an element validation exception.
     */
    public function checkValidationFailureAtomicity($expectedExceptionMessage, $operation, $allowedValidationCount = 0)
    {
        $before = $this->_collection->elements();

        $this->_remainingPassingValidations = $allowedValidationCount;

        try {
            call_user_func($operation, $this->_collection);
            $this->fail('Expected exception was not thrown.');
        } catch (Exception\InvalidElementException $e) {
            $this->assertSame($expectedExceptionMessage, $e->getMessage());
        }

        $this->assertSame($before, $this->_collection->elements());
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

    public function testSerialization()
    {
        $this->_collection = new Set;
        $this->_collection->add(1);
        $this->_collection->add(2);
        $this->_collection->add(3);

        $packet = serialize($this->_collection);
        $collection = unserialize($packet);

        $this->assertSame($this->_collection->elements(), $collection->elements());
    }

    public function testSerializationOfElementValidator()
    {
        $collection = new Set(null, 'is_int');

        $packet = serialize($collection);
        $collection = unserialize($packet);

        $this->assertSame('is_int', $collection->elementValidator());
    }

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/23
     */
    public function testSerializationOfHashFunction()
    {
        $collection = new Set(null, null, 'sha1');

        $packet = serialize($collection);
        $collection = unserialize($packet);

        $this->assertSame('sha1', Liberator::liberate($collection)->hashFunction);
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
        $this->assertSame($this->_validator, $result->elementValidator());
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
        $this->assertSame($this->_validator, $result->elementValidator());
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
        $this->assertSame($this->_validator, $result->elementValidator());
        $this->assertSame(array(2, 3, 4), $result->elements());
        $this->assertSame(array(1, 2, 3, 2, 3, 4), $this->_validatedElements);
    }

    public function testMapValidationFailure()
    {
        $this->_collection->add(1);
        $this->_collection->add(2);
        $this->_collection->add(3);

        $this->checkValidationFailureAtomicity(
            'Invalid element: 2.',
            function ($set) {
                $set->map(
                    function ($element) {
                        return $element + 1;
                    }
                );
            }
        );
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

    /////////////////////////////////////////
    // Implementation of IteratorAggregate //
    /////////////////////////////////////////

    public function testIteration()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $result = iterator_to_array($this->_collection);

        $this->assertSame(array(0 => 'a', 1 => 'b', 2 => 'c'), $result);
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

        $this->assertSame(array('a'), $this->_validatedElements);
    }

    public function testAddValidationFailure()
    {
        $this->checkValidationFailureAtomicity(
            'Invalid element: 0.',
            function ($set) {
                $set->add(0);
            }
        );
    }

    public function testRemove()
    {
        $this->assertFalse($this->_collection->remove('a'));

        $this->_collection->add('a');

        $this->assertTrue($this->_collection->remove('a'));

        $this->assertFalse($this->_collection->contains('a'));
    }

    public function testIsEqual()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertTrue($this->_collection->isEqual($collection));

        $collection->remove('b');

        $this->assertFalse($this->_collection->isEqual($collection));

        $collection->add('b');
        $this->_collection->remove('b');

        $this->assertFalse($this->_collection->isEqual($collection));
    }

    public function testIsSuperset()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertTrue($this->_collection->isSuperset($collection));

        $this->_collection->add('d');

        $this->assertTrue($this->_collection->isSuperset($collection));

        $this->_collection->remove('a');

        $this->assertFalse($this->_collection->isSuperset($collection));
    }

    public function testIsSubset()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertTrue($this->_collection->isSubset($collection));

        $collection->add('d');

        $this->assertTrue($this->_collection->isSubset($collection));

        $collection->remove('a');

        $this->assertFalse($this->_collection->isSubset($collection));
    }

    public function testIsStrictSuperset()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertFalse($this->_collection->isStrictSuperset($collection));

        $this->_collection->add('d');

        $this->assertTrue($this->_collection->isStrictSuperset($collection));

        $this->_collection->remove('a');

        $this->assertFalse($this->_collection->isStrictSuperset($collection));
    }

    public function testIsStrictSubset()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $collection = new Set;
        $collection->add('c');
        $collection->add('b');
        $collection->add('a');

        $this->assertFalse($this->_collection->isStrictSubset($collection));

        $collection->add('d');

        $this->assertTrue($this->_collection->isStrictSubset($collection));

        $collection->remove('a');

        $this->assertFalse($this->_collection->isStrictSubset($collection));
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
        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $this->_validatedElements);
    }

    public function testUnionNoCheckOptimization()
    {
        $this->_collection->add('a');
        $this->_collection->add('b');
        $this->_collection->add('c');

        $set = new Set;
        $set->add('c');
        $set->add('d');
        $set->add('e');

        $result = $set->union($this->_collection);

        $this->assertSame(array('c', 'd', 'e', 'a', 'b'), $result->elements());
        $this->assertSame(array('a', 'b', 'c'), $this->_validatedElements);
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
        $this->assertSame(array('a', 'b', 'c', 'd', 'e'), $this->_validatedElements);
    }

    public function testUnionNoCheckOptimisationWithArray()
    {
        $this->_collection = new Set;
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

    public function testUnionInPlaceValidationFailure()
    {
        $this->_collection->add('a');

        $this->checkValidationFailureAtomicity(
            'Invalid element: "c".',
            function ($set) {
                $set->unionInPlace(
                    array(
                        'b',
                        'c',
                    )
                );
            },
            1
        );
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
