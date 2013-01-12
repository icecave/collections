<?php
namespace Icecave\Collections;

use PHPUnit_Framework_TestCase;

class QueueTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_collection = new Queue;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->_collection->size());
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
        $this->_collection->push(1);
        $this->_collection->push(2);
        $this->_collection->push(3);

        $collection = clone $this->_collection;

        $collection->pop();

        $this->assertSame(2, $collection->next());
        $this->assertSame(1, $this->_collection->next());
    }

    public function testSerialization()
    {
        $this->_collection->push(1);
        $this->_collection->push(2);
        $this->_collection->push(3);

        $packet = serialize($this->_collection);
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
        $this->assertSame(0, $this->_collection->size());

        $this->_collection->push('foo');
        $this->_collection->push('bar');
        $this->_collection->push('spam');

        $this->assertSame(3, $this->_collection->size());

        $this->_collection->clear();

        $this->assertSame(0, $this->_collection->size());
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->_collection->isEmpty());

        $this->_collection->push('foo');

        $this->assertFalse($this->_collection->isEmpty());

        $this->_collection->clear();

        $this->assertTrue($this->_collection->isEmpty());
    }

    public function testToString()
    {
        $this->assertSame('<Queue 0>', $this->_collection->__toString());

        $this->_collection->push('foo');
        $this->_collection->push('bar');
        $this->_collection->push('spam');

        $this->assertSame('<Queue 3 [next: "foo"]>', $this->_collection->__toString());
    }

    //////////////////////////////////////////////////
    // Implementation of MutableCollectionInterface //
    //////////////////////////////////////////////////

    public function testClear()
    {
        $this->_collection->push('foo');

        $this->_collection->clear();

        $this->assertTrue($this->_collection->isEmpty());
    }

    /////////////////////////////////////////////
    // Implementation of QueuedAccessInterface //
    /////////////////////////////////////////////

    public function testNext()
    {
        $this->_collection->push('foo');
        $this->_collection->push('bar');

        $this->assertSame('foo', $this->_collection->next());
    }

    public function testNextWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->_collection->next();
    }

    public function testTryNext()
    {
        $this->_collection->push('foo');
        $this->_collection->push('bar');

        $element = null;
        $this->assertTrue($this->_collection->tryNext($element));
        $this->assertSame('foo', $element);
    }

    public function testTryNextWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->_collection->tryNext($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    public function testPop()
    {
        $this->_collection->push('foo');
        $this->_collection->push('bar');

        $this->assertSame('foo', $this->_collection->pop());
        $this->assertSame(1, $this->_collection->size());
    }

    public function testPopWithEmptyCollection()
    {
        $this->setExpectedException(__NAMESPACE__ . '\Exception\EmptyCollectionException');
        $this->_collection->pop();
    }

    public function testTryPop()
    {
        $this->_collection->push('foo');
        $this->_collection->push('bar');

        $element = null;
        $this->assertTrue($this->_collection->tryPop($element));
        $this->assertSame('foo', $element);
        $this->assertSame(1, $this->_collection->size());
    }

    public function testTryPopWithEmptyCollection()
    {
        $element = '<not null>';
        $this->assertFalse($this->_collection->tryPop($element));
        $this->assertSame('<not null>', $element); // Reference should not be changed on failure.
    }

    /////////////////////////////////
    // Implementation of Countable //
    /////////////////////////////////

    public function testCount()
    {
        $this->assertSame(0, count($this->_collection));

        $this->_collection->push('foo');
        $this->_collection->push('bar');
        $this->_collection->push('spam');

        $this->assertSame(3, count($this->_collection));

        $this->_collection->clear();

        $this->assertSame(0, count($this->_collection));
    }
}
