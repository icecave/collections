<?php
namespace Icecave\Collections;

use Exception;
use PHPUnit_Framework_TestCase;

class PriorityQueueTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->prioritizer = __CLASS__ . '::identityPrioritizer';
        $this->collection = new PriorityQueue(null, $this->prioritizer);
    }

    public static function identityPrioritizer($value)
    {
        return $value;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->collection->size());
    }

    public function testConstructorWithArray()
    {
        $collection = new PriorityQueue(array(1, 2, 3), $this->prioritizer);
        $this->assertSame(3, $collection->size());
        $this->assertSame(3, $collection->pop());
        $this->assertSame(2, $collection->pop());
        $this->assertSame(1, $collection->pop());
        $this->assertSame(0, $collection->size());
    }

    public function testCreate()
    {
        $collection = PriorityQueue::create(1, 2, 3);

        $this->assertInstanceOf(__NAMESPACE__ . '\PriorityQueue', $collection);
        $this->assertSame(3, $collection->size());
        $this->assertSame(3, $collection->pop());
        $this->assertSame(2, $collection->pop());
        $this->assertSame(1, $collection->pop());
        $this->assertSame(0, $collection->size());
    }

    public function testSerialization()
    {
        $this->collection->push(1);
        $this->collection->push(2);
        $this->collection->push(3);

        $packet = serialize($this->collection);
        $collection = unserialize($packet);

        $this->assertSame(3, $collection->pop());
        $this->assertSame(2, $collection->pop());
        $this->assertSame(1, $collection->pop());
        $this->assertTrue($collection->isEmpty());
    }

    ///////////////////////////////////////////
    // Implementation of CollectionInterface //
    ///////////////////////////////////////////

    public function testSize()
    {
        $this->assertSame(0, $this->collection->size());

        $this->collection->push('foo');
        $this->collection->push('bar');
        $this->collection->push('spam');

        $this->assertSame(3, $this->collection->size());

        $this->collection->clear();

        $this->assertSame(0, $this->collection->size());
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->collection->isEmpty());

        $this->collection->push('foo');

        $this->assertFalse($this->collection->isEmpty());

        $this->collection->clear();

        $this->assertTrue($this->collection->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('<PriorityQueue 0>', $this->collection->__toString());

        $this->collection->push(1);
        $this->collection->push(2);
        $this->collection->push(3);

        $this->assertSame("<PriorityQueue 3 [next: 3]>", $this->collection->__toString());
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    public function testClear()
    {
        $this->collection->push('foo');

        $this->collection->clear();

        $this->assertTrue($this->collection->isEmpty());
    }

    /////////////////////////////////////////////
    // Implementation of QueuedAccessInterface //
    /////////////////////////////////////////////

    public function testNext()
    {
        $this->collection->push(1);
        $this->collection->push(2);

        $this->assertSame(2, $this->collection->next());
    }

    public function testNextWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->collection->next();
    }

    public function testTryNext()
    {
        $this->collection->push(1);
        $this->collection->push(2);

        $element = null;
        $this->assertTrue($this->collection->tryNext($element));
        $this->assertSame(2, $element);
    }

    public function testTryNextWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->collection->tryNext($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testPop()
    {
        $this->collection->push(1);
        $this->collection->push(2);

        $this->assertSame(2, $this->collection->pop());
        $this->assertSame(1, $this->collection->size());
    }

    public function testPopWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->collection->pop();
    }

    public function testTryPop()
    {
        $this->collection->push(1);
        $this->collection->push(2);

        $element = null;
        $this->assertTrue($this->collection->tryPop($element));
        $this->assertSame(2, $element);
        $this->assertSame(1, $this->collection->size());
    }

    public function testTryPopWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->collection->tryPop($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testPush()
    {
        $this->collection->push(2);

        $this->assertSame(2, $this->collection->next());

        $this->collection->push(1);

        $this->assertSame(2, $this->collection->next());

        $this->collection->push(3);

        $this->assertSame(3, $this->collection->next());
    }

    public function testPushWithExplicitPriority()
    {
        $this->collection->push(1);
        $this->collection->push(0, 3);

        $this->assertSame(0, $this->collection->next());
    }

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/56
     */
    public function testPushWithFalsyPriority()
    {
        $prioritizer = function () {
            throw new Exception('Prioritizer called unexpectedly.');
        };

        $collection = new PriorityQueue(null, $prioritizer);
        $collection->push('value', 0);

        $this->assertSame('value', $collection->next());
    }

    ////////////////////////////////////////////////////////////////
    // Implementation of [Restricted|Extended]ComparableInterface //
    ////////////////////////////////////////////////////////////////

    public function testCanCompare()
    {
        $this->assertTrue($this->collection->canCompare(new PriorityQueue(null, $this->prioritizer)));
        $this->assertFalse($this->collection->canCompare(new PriorityQueue(null, function () {})));
        $this->assertFalse($this->collection->canCompare(new Queue));
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
