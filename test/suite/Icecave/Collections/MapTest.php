<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use Icecave\Collections\Iterator\Traits;
use PHPUnit_Framework_TestCase;
use stdClass;

class MapTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_collection = new Map;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->_collection->size());
    }

    public function testConstructorWithArray()
    {
        $array = array(
            'a' => 1,
            'b' => 2,
            'c' => 3,
        );

        $collection = new Map($array);
        $this->assertSame(array(array('a', 1), array('b', 2), array('c', 3)), $collection->elements());
    }

    public function testClone()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $collection = clone $this->_collection;

        $collection->remove('a');

        $this->assertSame(array(array('b', 2), array('c', 3)), $collection->elements());
        $this->assertSame(array(array('a', 1), array('b', 2), array('c', 3)), $this->_collection->elements());
    }

    public function testSerialization()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $packet = serialize($this->_collection);
        $collection = unserialize($packet);

        $this->assertSame($this->_collection->elements(), $collection->elements());
    }

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/23
     */
    public function testSerializationOfHashFunction()
    {
        $collection = new Map(null, 'sha1');

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

        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->assertSame(3, $this->_collection->size());

        $this->_collection->clear();

        $this->assertSame(0, $this->_collection->size());
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->_collection->isEmpty());

        $this->_collection->set('a', 1);

        $this->assertFalse($this->_collection->isEmpty());

        $this->_collection->clear();

        $this->assertTrue($this->_collection->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('<Map 0>', $this->_collection->__toString());

        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->assertSame('<Map 3 ["a" => 1, "b" => 2, "c" => 3]>', $this->_collection->__toString());

        $this->_collection->set('d', 4);

        $this->assertSame('<Map 4 ["a" => 1, "b" => 2, "c" => 3, ...]>', $this->_collection->__toString());
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    public function testClear()
    {
        $this->_collection->set('a', 1);

        $this->_collection->clear();

        $this->assertTrue($this->_collection->isEmpty());
    }

    //////////////////////////////////////////////
    // Implementation of IteratorTraitsProvider //
    //////////////////////////////////////////////

    public function testIteratorTraits()
    {
        $this->assertEquals(new Traits(true, true), $this->_collection->iteratorTraits());
    }

    /////////////////////////////////////////
    // Implementation of IterableInterface //
    /////////////////////////////////////////

    public function testElements()
    {
        $this->assertSame(array(), $this->_collection->elements());

        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->assertSame(array(array('a', 1), array('b', 2), array('c', 3)), $this->_collection->elements());
    }

    public function testContains()
    {
        $this->assertFalse($this->_collection->contains(1));

        $this->_collection->set('a', 1);

        $this->assertTrue($this->_collection->contains(1));
    }

    public function testFiltered()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', null);
        $this->_collection->set('c', 3);

        $result = $this->_collection->filtered();

        $this->assertInstanceOf(__NAMESPACE__ . '\Map', $result);
        $this->assertSame(array(array('a', 1), array('c', 3)), $result->elements());
    }

    public function testFilteredWithPredicate()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);
        $this->_collection->set('d', 4);
        $this->_collection->set('e', 5);

        $result = $this->_collection->filtered(
            function ($key, $value) {
                return $value & 0x1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Map', $result);
        $this->assertSame(array(array('a', 1), array('c', 3), array('e', 5)), $result->elements());
    }

    public function testMap()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $result = $this->_collection->map(
            function ($key, $value) {
                return array($key, $value + 1);
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Map', $result);
        $this->assertSame(array(array('a', 2), array('b', 3), array('c', 4)), $result->elements());
    }

    public function testPartition()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $result = $this->_collection->partition(
            function ($key, $value) {
                return $value < 3;
            }
        );

        $this->assertTrue(is_array($result));
        $this->assertSame(2, count($result));

        list($left, $right) = $result;

        $this->assertInstanceOf(__NAMESPACE__ . '\Map', $left);
        $this->assertSame(array(array('a', 1), array('b', 2)), $left->elements());

        $this->assertInstanceOf(__NAMESPACE__ . '\Map', $right);
        $this->assertSame(array(array('c', 3)), $right->elements());
    }

    public function testEach()
    {
        $calls = array();
        $callback = function ($key, $value) use (&$calls) {
            $calls[] = func_get_args();
        };

        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->_collection->each($callback);

        $expected = array(
            array('a', 1),
            array('b', 2),
            array('c', 3),
        );

        $this->assertSame($expected, $calls);
    }

    public function testAll()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->assertTrue(
            $this->_collection->all(
                function ($key, $value) {
                    return is_int($value);
                }
            )
        );

        $this->assertFalse(
            $this->_collection->all(
                function ($key, $value) {
                    return $value > 2;
                }
            )
        );
    }

    public function testAny()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->assertTrue(
            $this->_collection->any(
                function ($key, $value) {
                    return $value > 2;
                }
            )
        );

        $this->assertFalse(
            $this->_collection->any(
                function ($key, $value) {
                    return is_float($value);
                }
            )
        );
    }

    ////////////////////////////////////////////////
    // Implementation of MutableIterableInterface //
    ////////////////////////////////////////////////

    public function testFilter()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', null);
        $this->_collection->set('c', 3);

        $this->_collection->filter();

        $this->assertSame(array(array('a', 1), array('c', 3)), $this->_collection->elements());
    }

    public function testFilterWithPredicate()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);
        $this->_collection->set('d', 4);
        $this->_collection->set('e', 5);

        $this->_collection->filter(
            function ($key, $value) {
                return $value & 0x1;
            }
        );

        $this->assertSame(array(array('a', 1), array('c', 3), array('e', 5)), $this->_collection->elements());
    }

    public function testApply()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->_collection->apply(
            function ($key, $value) {
                return $value + 1;
            }
        );

        $this->assertSame(array(array('a', 2), array('b', 3), array('c', 4)), $this->_collection->elements());
    }

    ////////////////////////////////////////////
    // Implementation of AssociativeInterface //
    ////////////////////////////////////////////

    public function testHasKey()
    {
        $this->assertFalse($this->_collection->hasKey('a'));

        $this->_collection->set('a', 1);

        $this->assertTrue($this->_collection->hasKey('a'));
    }

    public function testGet()
    {
        $this->_collection->set('a', 1);

        $this->assertSame(1, $this->_collection->get('a'));
    }

    public function testGetFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');

        $this->_collection->get('a');
    }

    public function testTryGet()
    {
        $value = '<not null>';

        $this->assertFalse($this->_collection->tryGet('a', $value));
        $this->assertSame('<not null>', $value); // element should not be changed on failure

        $this->_collection->set('a', 1);

        $this->assertTrue($this->_collection->tryGet('a', $value));
        $this->assertSame(1, $value);
    }

    public function testGetWithDefault()
    {
        $this->assertNull($this->_collection->getWithDefault('a'));
        $this->assertSame('<default>', $this->_collection->getWithDefault('a', '<default>'));

        $this->_collection->set('a', 1);

        $this->assertSame(1, $this->_collection->getWithDefault('a'));
    }

    public function testCascade()
    {
        $this->_collection->set('b', 2);

        $this->assertSame(2, $this->_collection->cascade('a', 'b', 'c'));
    }

    public function testCascadeFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "c" does not exist.');

        $this->_collection->cascade('a', 'b', 'c');
    }

    public function testCascadeWithDefault()
    {
        $this->assertSame('<default>', $this->_collection->cascadeWithDefault('<default>', 'a', 'b', 'c'));

        $this->_collection->set('b', 2);

        $this->assertSame(2, $this->_collection->cascadeWithDefault('<default>', 'a', 'b', 'c'));
    }

    public function testCascadeIterable()
    {
        $this->_collection->set('b', 2);

        $this->assertSame(2, $this->_collection->cascadeIterable(array('a', 'b', 'c')));
    }

    public function testCascadeIterableWithDefault()
    {
        $this->assertSame('<default>', $this->_collection->cascadeIterableWithDefault('<default>', array('a', 'b', 'c')));

        $this->_collection->set('b', 2);

        $this->assertSame(2, $this->_collection->cascadeIterableWithDefault('<default>', array('a', 'b', 'c')));
    }

    public function testKeys()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->assertSame(array('a', 'b', 'c'), $this->_collection->keys());
    }

    public function testValues()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->assertSame(array(1, 2, 3), $this->_collection->values());
    }

    public function testCombine()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('c', 3);

        $collection = new Map;
        $collection->set('a', 10);
        $collection->set('b', 20);

        $result = $this->_collection->combine($collection);

        $this->assertSame(array(array('a', 10), array('c', 3), array('b', 20)), $result->elements());
    }

    public function testProject()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $result = $this->_collection->project('b', 'd');

        $this->assertSame(array(array('b', 2)), $result->elements());
    }

    public function testProjectIterable()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $result = $this->_collection->projectIterable(array('b', 'd'));

        $this->assertSame(array(array('b', 2)), $result->elements());
    }

    ///////////////////////////////////////////////////
    // Implementation of MutableAssociativeInterface //
    ///////////////////////////////////////////////////

    public function testSet()
    {
        $this->assertFalse($this->_collection->hasKey('a'));

        $this->_collection->set('a', 1);

        $this->assertSame(1, $this->_collection->get('a'));

        $this->_collection->set('a', 2);

        $this->assertSame(2, $this->_collection->get('a'));
    }

    public function testAdd()
    {
        $this->_collection->add('a', 1);

        $this->assertSame(1, $this->_collection->get('a'));
    }

    public function testAddFailure()
    {
        $this->_collection->set('a', 1);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\DuplicateKeyException', 'Key "a" already exists.');
        $this->_collection->add('a', 1);
    }

    public function testTryAdd()
    {
        $this->assertTrue($this->_collection->tryAdd('a', 1));
        $this->assertFalse($this->_collection->tryAdd('a', 2));
        $this->assertSame(1, $this->_collection->get('a'));
    }

    public function testReplace()
    {
        $this->_collection->set('a', 1);
        $previous = $this->_collection->replace('a', 2);

        $this->assertSame(1, $previous);
        $this->assertSame(2, $this->_collection->get('a'));
    }

    public function testReplaceFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->_collection->replace('a', 2);
    }

    public function testTryReplace()
    {
        $previous = null;
        $this->_collection->set('a', 1);
        $this->assertTrue($this->_collection->tryReplace('a', 2, $previous));

        $this->assertSame(1, $previous);
        $this->assertSame(2, $this->_collection->get('a'));
    }

    public function testTryReplaceFailure()
    {
        $this->assertFalse($this->_collection->tryReplace('b', 2));
        $this->assertFalse($this->_collection->hasKey('b'));
    }

    public function testRemove()
    {
        $this->_collection->set('a', 1);
        $value = $this->_collection->remove('a');

        $this->assertSame(1, $value);
        $this->assertFalse($this->_collection->hasKey('a'));
    }

    public function testRemoveFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->_collection->remove('a');
    }

    public function testTryRemove()
    {
        $value = '<not null>';

        $this->assertFalse($this->_collection->tryRemove('a', $value));
        $this->assertSame('<not null>', $value); // value should not be changed on failure

        $this->_collection->set('a', 1);

        $this->assertTrue($this->_collection->tryRemove('a', $value));
        $this->assertSame(1, $value);
        $this->assertFalse($this->_collection->hasKey('a'));
    }

    public function testMerge()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('c', 3);

        $collection = new Map;
        $collection->set('a', 10);
        $collection->set('b', 20);

        $this->_collection->merge($collection);

        $this->assertSame(array(array('a', 10), array('c', 3), array('b', 20)), $this->_collection->elements());
    }

    public function testSwap()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);

        $this->_collection->swap('a', 'b');

        $this->assertSame(array(array('a', 2), array('b', 1)), $this->_collection->elements());
    }

    public function testSwapFailureWithUnknownSource()
    {
        $this->_collection->set('b', 2);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->_collection->swap('a', 'b');
    }

    public function testSwapFailureWithUnknownTarget()
    {
        $this->_collection->set('a', 1);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "b" does not exist.');
        $this->_collection->swap('a', 'b');
    }

    public function testTrySwap()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);

        $this->assertTrue($this->_collection->trySwap('a', 'b'));

        $this->assertSame(array(array('a', 2), array('b', 1)), $this->_collection->elements());
    }

    public function testTrySwapFailureWithUnknownSource()
    {
        $this->_collection->set('b', 2);

        $this->assertFalse($this->_collection->trySwap('a', 'b'));

        $this->assertSame(array(array('b', 2)), $this->_collection->elements());
    }

    public function testTrySwapFailureWithUnknownTarget()
    {
        $this->_collection->set('a', 1);

        $this->assertFalse($this->_collection->trySwap('a', 'b'));

        $this->assertSame(array(array('a', 1)), $this->_collection->elements());
    }

    public function testMove()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);

        $this->_collection->move('a', 'b');

        $this->assertSame(array(array('b', 1)), $this->_collection->elements());
    }

    public function testMoveFailure()
    {
        $this->_collection->set('b', 2);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->_collection->move('a', 'b');
    }

    public function testTryMove()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);

        $this->assertTrue($this->_collection->tryMove('a', 'b'));

        $this->assertSame(array(array('b', 1)), $this->_collection->elements());
    }

    public function testTryMoveFailure()
    {
        $this->_collection->set('b', 2);

        $this->assertFalse($this->_collection->tryMove('a', 'b'));

        $this->assertSame(array(array('b', 2)), $this->_collection->elements());
    }

    public function testRename()
    {
        $this->_collection->set('a', 1);

        $this->_collection->rename('a', 'b');

        $this->assertSame(array(array('b', 1)), $this->_collection->elements());
    }

    public function testRenameFailureWithUnknownSource()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->_collection->rename('a', 'b');
    }

    public function testRenameFailureWithDuplicateTarget()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\DuplicateKeyException', 'Key "b" already exists.');
        $this->_collection->rename('a', 'b');
    }

    public function testTryRename()
    {
        $this->_collection->set('a', 1);

        $this->assertTrue($this->_collection->tryRename('a', 'b'));

        $this->assertSame(array(array('b', 1)), $this->_collection->elements());
    }

    public function testTryRenameFailureWithUnknownSource()
    {
        $this->_collection->set('b', 2);

        $this->assertFalse($this->_collection->tryRename('a', 'b'));

        $this->assertSame(array(array('b', 2)), $this->_collection->elements());
    }

    public function testTryRenameFailureWithDuplicateTarget()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);

        $this->assertFalse($this->_collection->tryRename('a', 'b'));

        $this->assertSame(array(array('a', 1), array('b', 2)), $this->_collection->elements());
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function testCount()
    {
        $this->assertSame(0, count($this->_collection));

        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $this->assertSame(3, count($this->_collection));

        $this->_collection->clear();

        $this->assertSame(0, count($this->_collection));
    }

    ////////////////////////////////
    // Implementation of Iterator //
    ////////////////////////////////

    public function testIteration()
    {
        $this->_collection->set('a', 1);
        $this->_collection->set('b', 2);
        $this->_collection->set('c', 3);

        $result = iterator_to_array($this->_collection);

        $this->assertSame(array('a' => 1, 'b' => 2, 'c' => 3), $result);
    }

    /**
     * @link https://github.com/IcecaveStudios/collections/issues/34
     */
    public function testIteratorKeyLimitationWorkaround()
    {
        $key1 = new stdClass;
        $this->_collection->set($key1, 'a');

        $key2 = new stdClass;
        $this->_collection->set($key2, 'b');

        $keys = array();
        $values = array();

        foreach ($this->_collection as $value) {
            $keys[] = $this->_collection->key();
            $values[] = $value;
        }

        $this->assertSame(array($key1, $key2), $keys);
        $this->assertSame(array('a', 'b'), $values);
    }

    ///////////////////////////////////
    // Implementation of ArrayAccess //
    ///////////////////////////////////

    public function testOffsetExists()
    {
        $this->assertFalse(isset($this->_collection['a']));

        $this->_collection->set('a', 1);

        $this->assertTrue(isset($this->_collection['a']));
    }

    public function testOffsetGet()
    {
        $this->_collection->set('a', 1);

        $this->assertSame(1, $this->_collection['a']);
    }

    public function testOffsetGetFailure()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');

        $this->_collection['a'];
    }

    public function testOffsetSet()
    {
        $this->_collection['a'] = 1;

        $this->assertSame(array(array('a', 1)), $this->_collection->elements());
    }

    public function testOffsetUnset()
    {
        unset($this->_collection['a']);

        $this->assertTrue($this->_collection->isEmpty());
    }
}
