<?php
namespace Icecave\Collections;

use PHPUnit_Framework_TestCase;

class PriorityQueueTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->prioritizer = __CLASS__ . '::identityPrioritizer';
        $this->collection = new PriorityQueue($this->prioritizer);
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
        $collection = new PriorityQueue($this->prioritizer, array(1, 2, 3));
        $this->assertSame(3, $collection->size());
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

}
