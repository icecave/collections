<?php
namespace Icecave\Collections;

use PHPUnit_Framework_TestCase;

class StackTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_collection = new Stack;
    }

    public function testConstructor()
    {
        $this->assertSame(0, $this->_collection->size());
    }

    public function testConstructorWithArray()
    {
        $collection = new Stack(array(1, 2, 3));
        $this->assertSame(3, $collection->size());
        $this->assertSame(3, $collection->pop());
        $this->assertSame(2, $collection->pop());
        $this->assertSame(1, $collection->pop());
        $this->assertSame(0, $collection->size());
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
        $this->assertSame('<Stack 0>', $this->_collection->__toString());

        $this->_collection->push('foo');
        $this->_collection->push('bar');
        $this->_collection->push('spam');

        $this->assertSame('<Stack 3 [next: "spam"]>', $this->_collection->__toString());
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

        $this->assertSame('bar', $this->_collection->next());
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
        $this->assertSame('bar', $element);
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

        $this->assertSame('bar', $this->_collection->pop());
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
        $this->assertSame('bar', $element);
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
