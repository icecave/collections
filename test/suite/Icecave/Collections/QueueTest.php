<?php
namespace Icecave\Collections;

use PHPUnit_Framework_TestCase;

class QueueTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->collection = new Queue;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->collection->size());
    }

    public function testConstructorWithArray()
    {
        $collection = new Queue(array(1, 2, 3));
        $this->assertSame(3, $collection->size());
        $this->assertSame(1, $collection->pop());
        $this->assertSame(2, $collection->pop());
        $this->assertSame(3, $collection->pop());
        $this->assertSame(0, $collection->size());
    }

    public function testClone()
    {
        $this->collection->push(1);
        $this->collection->push(2);
        $this->collection->push(3);

        $collection = clone $this->collection;

        $collection->pop();

        $this->assertSame(2, $collection->next());
        $this->assertSame(1, $this->collection->next());
    }

    public function testSerialization()
    {
        $this->collection->push(1);
        $this->collection->push(2);
        $this->collection->push(3);

        $packet = serialize($this->collection);
        $collection = unserialize($packet);

        $this->assertSame(1, $collection->pop());
        $this->assertSame(2, $collection->pop());
        $this->assertSame(3, $collection->pop());
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
        $this->assertSame('<Queue 0>', $this->collection->__toString());

        $this->collection->push('foo');
        $this->collection->push('bar');
        $this->collection->push('spam');

        $this->assertSame('<Queue 3 [next: "foo"]>', $this->collection->__toString());
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
        $this->collection->push('foo');
        $this->collection->push('bar');

        $this->assertSame('foo', $this->collection->next());
    }

    public function testNextWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->collection->next();
    }

    public function testTryNext()
    {
        $this->collection->push('foo');
        $this->collection->push('bar');

        $element = null;
        $this->assertTrue($this->collection->tryNext($element));
        $this->assertSame('foo', $element);
    }

    public function testTryNextWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->collection->tryNext($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testPop()
    {
        $this->collection->push('foo');
        $this->collection->push('bar');

        $this->assertSame('foo', $this->collection->pop());
        $this->assertSame(1, $this->collection->size());
    }

    public function testPopWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->collection->pop();
    }

    public function testTryPop()
    {
        $this->collection->push('foo');
        $this->collection->push('bar');

        $element = null;
        $this->assertTrue($this->collection->tryPop($element));
        $this->assertSame('foo', $element);
        $this->assertSame(1, $this->collection->size());
    }

    public function testTryPopWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->collection->tryPop($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function testCount()
    {
        $this->assertSame(0, count($this->collection));

        $this->collection->push('foo');
        $this->collection->push('bar');
        $this->collection->push('spam');

        $this->assertSame(3, count($this->collection));

        $this->collection->clear();

        $this->assertSame(0, count($this->collection));
    }
}
