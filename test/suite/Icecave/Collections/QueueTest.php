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

    public function testCreate()
    {
        $collection = Queue::create(1, 2, 3);

        $this->assertInstanceOf(__NAMESPACE__ . '\Queue', $collection);
        $this->assertSame(3, $collection->size());
        $this->assertSame(1, $collection->pop());
        $this->assertSame(2, $collection->pop());
        $this->assertSame(3, $collection->pop());
        $this->assertSame(0, $collection->size());
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

    ////////////////////////////////////////////////////////////////
    // Implementation of [Restricted|Extended]ComparableInterface //
    ////////////////////////////////////////////////////////////////

    /**
     * @dataProvider getCompareData
     */
    public function testCompare($lhs, $rhs, $expectedResult)
    {
        $lhs = new Queue($lhs);
        $rhs = new Queue($rhs);

        $cmp = $lhs->compare($rhs);

        if ($expectedResult < 0) {
            $this->assertLessThan(0, $cmp);
        } elseif ($expectedResult > 0) {
            $this->assertGreaterThan(0, $cmp);
        } else {
            $this->assertSame(0, $cmp);
        }

        $this->assertSame($expectedResult === 0, $lhs->isEqualTo($rhs));
        $this->assertSame($expectedResult === 0, $rhs->isEqualTo($lhs));

        $this->assertSame($expectedResult !== 0, $lhs->isNotEqualTo($rhs));
        $this->assertSame($expectedResult !== 0, $rhs->isNotEqualTo($lhs));

        $this->assertSame($expectedResult < 0, $lhs->isLessThan($rhs));
        $this->assertSame($expectedResult > 0, $rhs->isLessThan($lhs));

        $this->assertSame($expectedResult > 0, $lhs->isGreaterThan($rhs));
        $this->assertSame($expectedResult < 0, $rhs->isGreaterThan($lhs));

        $this->assertSame($expectedResult <= 0, $lhs->isLessThanOrEqualTo($rhs));
        $this->assertSame($expectedResult >= 0, $rhs->isLessThanOrEqualTo($lhs));

        $this->assertSame($expectedResult >= 0, $lhs->isGreaterThanOrEqualTo($rhs));
        $this->assertSame($expectedResult <= 0, $rhs->isGreaterThanOrEqualTo($lhs));
    }

    public function testCompareFailure()
    {
        $this->setExpectedException('Icecave\Parity\Exception\NotComparableException');
        $this->collection->compare(array());
    }

    public function testCanCompare()
    {
        $this->assertTrue($this->collection->canCompare(new Queue));
        $this->assertFalse($this->collection->canCompare(new PriorityQueue(null, function () {})));
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
