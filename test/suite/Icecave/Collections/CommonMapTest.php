<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use Exception;
use Ezzatron\PHPUnit\ParameterizedTestCase;
use Icecave\Collections\Exception\DuplicateKeyException;
use Icecave\Collections\Exception\UnknownKeyException;
use Icecave\Collections\Iterator\Traits;
use PHPUnit_Framework_TestCase;
use stdClass;

/**
 * @covers Icecave\Collections\Map
 * @covers Icecave\Collections\HashMap
 */
class CommonMapTest extends ParameterizedTestCase
{
    public function getTestCaseParameters()
    {
        return array(
            array(new Map,     'verifyElementsInMap'),
            array(new HashMap, 'verifyElementsInHashMap'),
        );
    }

    public function setUpParameterized($collection, $verifyElementsFunction)
    {
        $this->className = get_class($collection);
        $this->shortClassName = substr($this->className, strrpos($this->className, '\\') + 1);
        $this->collection = $collection;
        $this->liberatedCollection = Liberator::liberate($this->collection);
        $this->verifyElementsFunction = $verifyElementsFunction;
    }

    private function createMap($elements = null)
    {
        $class = $this->className;

        return new $class($elements);
    }

    private function verifyElements()
    {
        $arguments = func_get_args();

        if (end($arguments) instanceof $this->className) {
            $collection = array_pop($arguments);
        } else {
            $collection = $this->collection;
        }

        if (1 === count($arguments) && is_array($arguments[0]) && is_array($arguments[0][0])) {
            $arguments = $arguments[0];
        }

        call_user_func(
            array($this, $this->verifyElementsFunction),
            $collection,
            $arguments
        );
    }

    public function verifyElementsInMap(Map $collection, array $elements)
    {
        $this->assertSame(
            $elements,
            $collection->elements()
        );
    }

    public function verifyElementsInHashMap(HashMap $collection, array $elements)
    {
        if (count($elements)) {
            $e = array();

            foreach ($elements as $element) {
                list($key, $value) = $element;
                $e[$key] = $element;
            }

            $elements = $e;
        }

        $actualElements = Liberator::liberate($collection)->elements;

        ksort($elements);
        ksort($actualElements);

        $this->assertSame(
            $elements,
            $actualElements
        );
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->collection->size());
    }

    public function testConstructorWithArray()
    {
        $array = array(
            10 => 1,
            20 => 2,
            30 => 3,
        );

        $collection = $this->createMap($array);
        $this->verifyElements(array(10, 1), array(20, 2), array(30, 3), $collection);
    }

    public function testClone()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);

        $collection = clone $this->collection;

        $collection->remove(10);

        $this->verifyElements(array(20, 2), array(30, 3), $collection);
        $this->verifyElements(array(10, 1), array(20, 2), array(30, 3));
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    public function testSize()
    {
        $this->assertSame(0, $this->collection->size());

        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertSame(3, $this->collection->size());

        $this->collection->clear();

        $this->assertSame(0, $this->collection->size());
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->collection->isEmpty());

        $this->collection->set('a', 1);

        $this->assertFalse($this->collection->isEmpty());

        $this->collection->clear();

        $this->assertTrue($this->collection->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('<' . $this->shortClassName . ' 0>', $this->collection->__toString());

        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertSame('<' . $this->shortClassName . ' 3 ["a" => 1, "b" => 2, "c" => 3]>', $this->collection->__toString());

        $this->collection->set('d', 4);

        $this->assertSame('<' . $this->shortClassName . ' 4 ["a" => 1, "b" => 2, "c" => 3, ...]>', $this->collection->__toString());
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    public function testClear()
    {
        $this->collection->set('a', 1);

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

        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertSame(array(array('a', 1), array('b', 2), array('c', 3)), $this->collection->elements());
    }

    public function testContains()
    {
        $this->assertFalse($this->collection->contains(1));

        $this->collection->set('a', 1);

        $this->assertTrue($this->collection->contains(1));
    }

    public function testFilter()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, null);
        $this->collection->set(30, 3);

        $result = $this->collection->filter();

        $this->assertInstanceOf($this->className, $result);
        $this->verifyElements(array(10, 1), array(30, 3), $result);
    }

    public function testFilterWithPredicate()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);
        $this->collection->set(40, 4);
        $this->collection->set(50, 5);

        $result = $this->collection->filter(
            function ($key, $value) {
                return $value & 0x1;
            }
        );

        $this->assertInstanceOf($this->className, $result);
        $this->verifyElements(array(10, 1), array(30, 3), array(50, 5), $result);
    }

    public function testMap()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);

        $result = $this->collection->map(
            function ($key, $value) {
                return array($key + 5, $value + 1);
            }
        );

        $this->assertInstanceOf($this->className, $result);
        $this->verifyElements(array(15, 2), array(25, 3), array(35, 4), $result);
    }

    public function testPartition()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);

        $result = $this->collection->partition(
            function ($key, $value) {
                return $value < 3;
            }
        );

        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));

        list($left, $right) = $result;

        $this->assertInstanceOf($this->className, $left);
        $this->verifyElements(array(10, 1), array(20, 2), $left);

        $this->assertInstanceOf($this->className, $right);
        $this->verifyElements(array(30, 3), $right);
    }

    public function testEach()
    {
        $calls = array();
        $callback = function ($key, $value) use (&$calls) {
            $calls[] = func_get_args();
        };

        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->collection->each($callback);

        $expected = array(
            array('a', 1),
            array('b', 2),
            array('c', 3),
        );

        $this->assertSame($expected, $calls);
    }

    public function testAll()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertTrue(
            $this->collection->all(
                function ($key, $value) {
                    return is_int($value);
                }
            )
        );

        $this->assertFalse(
            $this->collection->all(
                function ($key, $value) {
                    return $value > 2;
                }
            )
        );
    }

    public function testAny()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertTrue(
            $this->collection->any(
                function ($key, $value) {
                    return $value > 2;
                }
            )
        );

        $this->assertFalse(
            $this->collection->any(
                function ($key, $value) {
                    return is_float($value);
                }
            )
        );
    }

    ////////////////////////////////////////////////
    // Implementation of MutableIterableInterface //
    ////////////////////////////////////////////////

    public function testFilterInPlace()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, null);
        $this->collection->set(30, 3);

        $this->collection->filterInPlace();

        $this->verifyElements(array(10, 1), array(30, 3));
    }

    public function testFilterInPlaceWithPredicate()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);
        $this->collection->set(40, 4);
        $this->collection->set(50, 5);

        $this->collection->filterInPlace(
            function ($key, $value) {
                return $value & 0x1;
            }
        );

        $this->verifyElements(array(10, 1), array(30, 3), array(50, 5));
    }

    public function testMapInPlace()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);

        $this->collection->mapInPlace(
            function ($key, $value) {
                return $value + 1;
            }
        );

        $this->verifyElements(array(10, 2), array(20, 3), array(30, 4));
    }

    ////////////////////////////////////////////
    // Implementation of AssociativeInterface //
    ////////////////////////////////////////////

    public function testHasKey()
    {
        $this->assertFalse($this->collection->hasKey('a'));

        $this->collection->set('a', 1);

        $this->assertTrue($this->collection->hasKey('a'));
    }

    public function testGet()
    {
        $this->collection->set('a', 1);

        $this->assertSame(1, $this->collection->get('a'));
    }

    public function testGetFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');

        $this->collection->get('a');
    }

    public function testTryGet()
    {
        $value = '<not null>';

        $this->assertFalse($this->collection->tryGet('a', $value));
        $this->assertSame('<not null>', $value); // element should not be changed on failure

        $this->collection->set('a', 1);

        $this->assertTrue($this->collection->tryGet('a', $value));
        $this->assertSame(1, $value);
    }

    public function testGetWithDefault()
    {
        $this->assertNull($this->collection->getWithDefault('a'));
        $this->assertSame('<default>', $this->collection->getWithDefault('a', '<default>'));

        $this->collection->set('a', 1);

        $this->assertSame(1, $this->collection->getWithDefault('a'));
    }

    public function testCascade()
    {
        $this->collection->set('b', 2);

        $this->assertSame(2, $this->collection->cascade('a', 'b', 'c'));
    }

    public function testCascadeFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "c" does not exist.');

        $this->collection->cascade('a', 'b', 'c');
    }

    public function testCascadeWithDefault()
    {
        $this->assertSame('<default>', $this->collection->cascadeWithDefault('<default>', 'a', 'b', 'c'));

        $this->collection->set('b', 2);

        $this->assertSame(2, $this->collection->cascadeWithDefault('<default>', 'a', 'b', 'c'));
    }

    public function testCascadeIterable()
    {
        $this->collection->set('b', 2);

        $this->assertSame(2, $this->collection->cascadeIterable(array('a', 'b', 'c')));
    }

    public function testCascadeIterableWithDefault()
    {
        $this->assertSame('<default>', $this->collection->cascadeIterableWithDefault('<default>', array('a', 'b', 'c')));

        $this->collection->set('b', 2);

        $this->assertSame(2, $this->collection->cascadeIterableWithDefault('<default>', array('a', 'b', 'c')));
    }

    public function testKeys()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertSame(array('a', 'b', 'c'), $this->collection->keys());
    }

    public function testValues()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertSame(array(1, 2, 3), $this->collection->values());
    }

    public function testMerge()
    {
        $this->collection->set(10, 1);
        $this->collection->set(30, 3);

        $collection = $this->createMap();
        $collection->set(10, 10);
        $collection->set(20, 20);

        $result = $this->collection->merge($collection);

        $this->verifyElements(array(10, 10), array(20, 20), array(30, 3), $result);
    }

    public function testProject()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);

        $result = $this->collection->project(20, 40);

        $this->verifyElements(array(20, 2), $result);
    }

    public function testProjectIterable()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);

        $result = $this->collection->projectIterable(array(20, 40));

        $this->verifyElements(array(20, 2), $result);
    }

    ///////////////////////////////////////////////////
    // Implementation of MutableAssociativeInterface //
    ///////////////////////////////////////////////////

    public function testSet()
    {
        $this->assertFalse($this->collection->hasKey('a'));

        $this->collection->set('a', 1);

        $this->assertSame(1, $this->collection->get('a'));

        $this->collection->set('a', 2);

        $this->assertSame(2, $this->collection->get('a'));
    }

    public function testAdd()
    {
        $this->collection->add('a', 1);

        $this->assertSame(1, $this->collection->get('a'));
    }

    public function testAddFailure()
    {
        $this->collection->set('a', 1);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\DuplicateKeyException', 'Key "a" already exists.');
        $this->collection->add('a', 1);
    }

    public function testTryAdd()
    {
        $this->assertTrue($this->collection->tryAdd('a', 1));
        $this->assertFalse($this->collection->tryAdd('a', 2));
        $this->assertSame(1, $this->collection->get('a'));
    }

    public function testReplace()
    {
        $this->collection->set('a', 1);
        $previous = $this->collection->replace('a', 2);

        $this->assertSame(1, $previous);
        $this->assertSame(2, $this->collection->get('a'));
    }

    public function testReplaceFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->collection->replace('a', 2);
    }

    public function testTryReplace()
    {
        $previous = null;
        $this->collection->set('a', 1);
        $this->assertTrue($this->collection->tryReplace('a', 2, $previous));

        $this->assertSame(1, $previous);
        $this->assertSame(2, $this->collection->get('a'));
    }

    public function testTryReplaceFailure()
    {
        $this->assertFalse($this->collection->tryReplace('b', 2));
        $this->assertFalse($this->collection->hasKey('b'));
    }

    public function testRemove()
    {
        $this->collection->set('a', 1);
        $value = $this->collection->remove('a');

        $this->assertSame(1, $value);
        $this->assertFalse($this->collection->hasKey('a'));
    }

    public function testRemoveFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->collection->remove('a');
    }

    public function testTryRemove()
    {
        $value = '<not null>';

        $this->assertFalse($this->collection->tryRemove('a', $value));
        $this->assertSame('<not null>', $value); // value should not be changed on failure

        $this->collection->set('a', 1);

        $this->assertTrue($this->collection->tryRemove('a', $value));
        $this->assertSame(1, $value);
        $this->assertFalse($this->collection->hasKey('a'));
    }

    public function testMergeInPlace()
    {
        $this->collection->set(10, 1);
        $this->collection->set(30, 3);

        $collection = $this->createMap();
        $collection->set(10, 10);
        $collection->set(20, 20);

        $this->collection->mergeInPlace($collection);

        $this->verifyElements(array(10, 10), array(20, 20), array(30, 3));
    }

    public function testSwap()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);
        $this->collection->set(40, 4);
        $this->collection->set(50, 5);

        $this->collection->swap(20, 40);

        $this->verifyElements(array(10, 1), array(20, 4), array(30, 3), array(40, 2), array(50, 5));
    }

    public function testSwapFailureWithUnknownSource()
    {
        $this->collection->set('b', 2);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->collection->swap('a', 'b');
    }

    public function testSwapFailureWithUnknownTarget()
    {
        $this->collection->set('a', 1);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "b" does not exist.');
        $this->collection->swap('a', 'b');
    }

    public function testTrySwap()
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);
        $this->collection->set(40, 4);
        $this->collection->set(50, 5);

        $this->assertTrue($this->collection->trySwap(20, 40));

        $this->verifyElements(array(10, 1), array(20, 4), array(30, 3), array(40, 2), array(50, 5));
    }

    public function testTrySwapFailureWithUnknownSource()
    {
        $this->collection->set(20, 2);

        $this->assertFalse($this->collection->trySwap(10, 20));

        $this->verifyElements(array(20, 2));
    }

    public function testTrySwapFailureWithUnknownTarget()
    {
        $this->collection->set(10, 1);

        $this->assertFalse($this->collection->trySwap(10, 20));

        $this->verifyElements(array(10, 1));
    }

    /**
     * @dataProvider getMoveData
     */
    public function testMove($sourceKey, $targetKey, $expectedResult)
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);
        $this->collection->set(40, 4);
        $this->collection->set(50, 5);

        if ($expectedResult instanceof Exception) {
            $this->setExpectedException(get_class($expectedResult), $expectedResult->getMessage());
            $this->collection->move($sourceKey, $targetKey);
        } else {
            $this->collection->move($sourceKey, $targetKey);
            $this->verifyElements($expectedResult);
        }
    }

    /**
     * @dataProvider getMoveData
     */
    public function testTryMove($sourceKey, $targetKey, $expectedResult)
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);
        $this->collection->set(40, 4);
        $this->collection->set(50, 5);

        $result = $this->collection->tryMove($sourceKey, $targetKey);

        if ($expectedResult instanceof Exception) {
            $this->assertFalse($result);
        } else {
            $this->assertTrue($result);
            $this->verifyElements($expectedResult);
        }
    }

    public function getMoveData()
    {
        return array(
            'lo-hi'             => array(20, 40,   array(array(10, 1), array(30, 3), array(40, 2), array(50, 5))),
            'hi-lo'             => array(40, 20,   array(array(10, 1), array(20, 4), array(30, 3), array(50, 5))),
            'new key lo-hi'     => array(20, 60,   array(array(10, 1), array(30, 3), array(40, 4), array(50, 5), array(60, 2))),
            'new key hi-lo'     => array(20, 5,    array(array(5, 2),  array(10, 1), array(30, 3), array(40, 4), array(50, 5))),
            'same key'          => array(20, 20,   array(array(10, 1), array(20, 2), array(30, 3), array(40, 4), array(50, 5))),
            'failure'           => array(5,  20,   new UnknownKeyException(5)),
        );
    }

    /**
     * @dataProvider getRenameData
     */
    public function testRename($sourceKey, $targetKey, $expectedResult)
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);
        $this->collection->set(40, 4);
        $this->collection->set(50, 5);

        if ($expectedResult instanceof Exception) {
            $this->setExpectedException(get_class($expectedResult), $expectedResult->getMessage());
            $this->collection->rename($sourceKey, $targetKey);
        } else {
            $this->collection->rename($sourceKey, $targetKey);
            $this->verifyElements($expectedResult);
        }
    }

    /**
     * @dataProvider getRenameData
     */
    public function testTryRename($sourceKey, $targetKey, $expectedResult)
    {
        $this->collection->set(10, 1);
        $this->collection->set(20, 2);
        $this->collection->set(30, 3);
        $this->collection->set(40, 4);
        $this->collection->set(50, 5);

        $result = $this->collection->tryRename($sourceKey, $targetKey);

        if ($expectedResult instanceof Exception) {
            $this->assertFalse($result);
        } else {
            $this->assertTrue($result);
            $this->verifyElements($expectedResult);
        }
    }

    public function getRenameData()
    {
        return array(
            'lo-hi'                 => array(20, 60,   array(array(10, 1), array(30, 3), array(40, 4), array(50, 5), array(60, 2))),
            'hi-lo'                 => array(20, 5,    array(array(5, 2),  array(10, 1), array(30, 3), array(40, 4), array(50, 5))),
            'failure - source key'  => array(5,  20,   new UnknownKeyException(5)),
            'failure - target key'  => array(10, 20,   new DuplicateKeyException(20)),
        );
    }



    // public function testRename()
    // {
    //     $this->collection->set(10, 1);

    //     $this->collection->rename(10, 20);

    //     $this->verifyElements(array(20, 1));
    // }

    // public function testRenameFailureWithUnknownSource()
    // {
    //     $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
    //     $this->collection->rename('a', 'b');
    // }

    // public function testRenameFailureWithDuplicateTarget()
    // {
    //     $this->collection->set('a', 1);
    //     $this->collection->set('b', 2);

    //     $this->setExpectedException(__NAMESPACE__ . '\Exception\DuplicateKeyException', 'Key "b" already exists.');
    //     $this->collection->rename('a', 'b');
    // }

    // public function testTryRename()
    // {
    //     $this->collection->set(10, 1);

    //     $this->assertTrue($this->collection->tryRename(10, 20));

    //     $this->verifyElements(array(20, 1));
    // }

    // public function testTryRenameFailureWithUnknownSource()
    // {
    //     $this->collection->set(20, 2);

    //     $this->assertFalse($this->collection->tryRename(10, 20));

    //     $this->verifyElements(array(20, 2));
    // }

    // public function testTryRenameFailureWithDuplicateTarget()
    // {
    //     $this->collection->set(10, 1);
    //     $this->collection->set(20, 2);

    //     $this->assertFalse($this->collection->tryRename(10, 20));

    //     $this->verifyElements(array(10, 1), array(20, 2));
    // }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function testCount()
    {
        $this->assertSame(0, count($this->collection));

        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertSame(3, count($this->collection));

        $this->collection->clear();

        $this->assertSame(0, count($this->collection));
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function testIteration()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $result = iterator_to_array($this->collection);

        $this->assertSame(array('a' => 1, 'b' => 2, 'c' => 3), $result);
    }

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/34
     */
    public function testIteratorKeyLimitationWorkaround()
    {
        $key1 = new stdClass;
        $key1->expectedValue = 'a';
        $this->collection->set($key1, 'a');

        $key2 = new stdClass;
        $key2->expectedValue = 'b';
        $this->collection->set($key2, 'b');

        $keys = array();
        $values = array();

        foreach ($this->collection as $value) {
            $keys[] = $this->collection->key();
            $values[] = $value;
        }

        $this->assertSame(2, count($keys));
        $this->assertTrue(in_array($key1, $keys));
        $this->assertTrue(in_array($key2, $keys));

        $this->assertSame(2, count($values));
        $this->assertSame($keys[0]->expectedValue, $values[0]);
        $this->assertSame($keys[1]->expectedValue, $values[1]);
    }

    ///////////////////////////////////
    // Implementation of ArrayAccess //
    ///////////////////////////////////

    public function testOffsetExists()
    {
        $this->assertFalse(isset($this->collection['a']));

        $this->collection->set('a', 1);

        $this->assertTrue(isset($this->collection['a']));
    }

    public function testOffsetGet()
    {
        $this->collection->set('a', 1);

        $this->assertSame(1, $this->collection['a']);
    }

    public function testOffsetGetFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');

        $this->collection['a'];
    }

    public function testOffsetSet()
    {
        $this->collection['a'] = 1;

        $this->assertSame(array(array('a', 1)), $this->collection->elements());
    }

    public function testOffsetUnset()
    {
        unset($this->collection['a']);

        $this->assertTrue($this->collection->isEmpty());
    }
}
