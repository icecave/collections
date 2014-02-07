<?php
namespace Icecave\Collections\Iterator;

use ArrayIterator;
use PHPUnit_Framework_TestCase;

class UnpackIteratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->pairs = array(
            array(10, 1),
            array(20, 2),
            array(30, 3),
        );
        $this->internalIterator = new ArrayIterator($this->pairs);
        $this->iterator = new UnpackIterator($this->internalIterator);
    }

    public function testGetInnerIterator()
    {
        $this->assertSame($this->internalIterator, $this->iterator->getInnerIterator());
    }

    public function testIteration()
    {
        $expected = array(
            10 => 1,
            20 => 2,
            30 => 3,
        );

        $this->assertSame($expected, iterator_to_array($this->iterator));
    }
}
