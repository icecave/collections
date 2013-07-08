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
        $this->collection = new Map;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->collection->size());
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
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $collection = clone $this->collection;

        $collection->remove('a');

        $this->assertSame(array(array('b', 2), array('c', 3)), $collection->elements());
        $this->assertSame(array(array('a', 1), array('b', 2), array('c', 3)), $this->collection->elements());
    }

    public function testSerialization()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

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
        $this->assertSame('<Map 0>', $this->collection->__toString());

        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->assertSame('<Map 3 ["a" => 1, "b" => 2, "c" => 3]>', $this->collection->__toString());

        $this->collection->set('d', 4);

        $this->assertSame('<Map 4 ["a" => 1, "b" => 2, "c" => 3, ...]>', $this->collection->__toString());
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

    public function testFiltered()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', null);
        $this->collection->set('c', 3);

        $result = $this->collection->filtered();

        $this->assertInstanceOf(__NAMESPACE__ . '\Map', $result);
        $this->assertSame(array(array('a', 1), array('c', 3)), $result->elements());
    }

    public function testFilteredWithPredicate()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);
        $this->collection->set('d', 4);
        $this->collection->set('e', 5);

        $result = $this->collection->filtered(
            function ($key, $value) {
                return $value & 0x1;
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Map', $result);
        $this->assertSame(array(array('a', 1), array('c', 3), array('e', 5)), $result->elements());
    }

    public function testMap()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $result = $this->collection->map(
            function ($key, $value) {
                return array($key, $value + 1);
            }
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\Map', $result);
        $this->assertSame(array(array('a', 2), array('b', 3), array('c', 4)), $result->elements());
    }

    public function testPartition()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $result = $this->collection->partition(
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

    public function testFilter()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', null);
        $this->collection->set('c', 3);

        $this->collection->filter();

        $this->assertSame(array(array('a', 1), array('c', 3)), $this->collection->elements());
    }

    public function testFilterWithPredicate()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);
        $this->collection->set('d', 4);
        $this->collection->set('e', 5);

        $this->collection->filter(
            function ($key, $value) {
                return $value & 0x1;
            }
        );

        $this->assertSame(array(array('a', 1), array('c', 3), array('e', 5)), $this->collection->elements());
    }

    public function testApply()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $this->collection->apply(
            function ($key, $value) {
                return $value + 1;
            }
        );

        $this->assertSame(array(array('a', 2), array('b', 3), array('c', 4)), $this->collection->elements());
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

    public function testCombine()
    {
        $this->collection->set('a', 1);
        $this->collection->set('c', 3);

        $collection = new Map;
        $collection->set('a', 10);
        $collection->set('b', 20);

        $result = $this->collection->combine($collection);

        $this->assertSame(array(array('a', 10), array('c', 3), array('b', 20)), $result->elements());
    }

    public function testProject()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $result = $this->collection->project('b', 'd');

        $this->assertSame(array(array('b', 2)), $result->elements());
    }

    public function testProjectIterable()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);
        $this->collection->set('c', 3);

        $result = $this->collection->projectIterable(array('b', 'd'));

        $this->assertSame(array(array('b', 2)), $result->elements());
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

    public function testMerge()
    {
        $this->collection->set('a', 1);
        $this->collection->set('c', 3);

        $collection = new Map;
        $collection->set('a', 10);
        $collection->set('b', 20);

        $this->collection->merge($collection);

        $this->assertSame(array(array('a', 10), array('c', 3), array('b', 20)), $this->collection->elements());
    }

    public function testSwap()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);

        $this->collection->swap('a', 'b');

        $this->assertSame(array(array('a', 2), array('b', 1)), $this->collection->elements());
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
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);

        $this->assertTrue($this->collection->trySwap('a', 'b'));

        $this->assertSame(array(array('a', 2), array('b', 1)), $this->collection->elements());
    }

    public function testTrySwapFailureWithUnknownSource()
    {
        $this->collection->set('b', 2);

        $this->assertFalse($this->collection->trySwap('a', 'b'));

        $this->assertSame(array(array('b', 2)), $this->collection->elements());
    }

    public function testTrySwapFailureWithUnknownTarget()
    {
        $this->collection->set('a', 1);

        $this->assertFalse($this->collection->trySwap('a', 'b'));

        $this->assertSame(array(array('a', 1)), $this->collection->elements());
    }

    public function testMove()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);

        $this->collection->move('a', 'b');

        $this->assertSame(array(array('b', 1)), $this->collection->elements());
    }

    public function testMoveFailure()
    {
        $this->collection->set('b', 2);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->collection->move('a', 'b');
    }

    public function testTryMove()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);

        $this->assertTrue($this->collection->tryMove('a', 'b'));

        $this->assertSame(array(array('b', 1)), $this->collection->elements());
    }

    public function testTryMoveFailure()
    {
        $this->collection->set('b', 2);

        $this->assertFalse($this->collection->tryMove('a', 'b'));

        $this->assertSame(array(array('b', 2)), $this->collection->elements());
    }

    public function testRename()
    {
        $this->collection->set('a', 1);

        $this->collection->rename('a', 'b');

        $this->assertSame(array(array('b', 1)), $this->collection->elements());
    }

    public function testRenameFailureWithUnknownSource()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\UnknownKeyException', 'Key "a" does not exist.');
        $this->collection->rename('a', 'b');
    }

    public function testRenameFailureWithDuplicateTarget()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);

        $this->setExpectedException(__NAMESPACE__ . '\Exception\DuplicateKeyException', 'Key "b" already exists.');
        $this->collection->rename('a', 'b');
    }

    public function testTryRename()
    {
        $this->collection->set('a', 1);

        $this->assertTrue($this->collection->tryRename('a', 'b'));

        $this->assertSame(array(array('b', 1)), $this->collection->elements());
    }

    public function testTryRenameFailureWithUnknownSource()
    {
        $this->collection->set('b', 2);

        $this->assertFalse($this->collection->tryRename('a', 'b'));

        $this->assertSame(array(array('b', 2)), $this->collection->elements());
    }

    public function testTryRenameFailureWithDuplicateTarget()
    {
        $this->collection->set('a', 1);
        $this->collection->set('b', 2);

        $this->assertFalse($this->collection->tryRename('a', 'b'));

        $this->assertSame(array(array('a', 1), array('b', 2)), $this->collection->elements());
    }

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
     * @link https://github.com/IcecaveStudios/collections/issues/34
     */
    public function testIteratorKeyLimitationWorkaround()
    {
        $key1 = new stdClass;
        $this->collection->set($key1, 'a');

        $key2 = new stdClass;
        $this->collection->set($key2, 'b');

        $keys = array();
        $values = array();

        foreach ($this->collection as $value) {
            $keys[] = $this->collection->key();
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
