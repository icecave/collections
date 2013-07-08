<?php
namespace Icecave\Collections\Iterator;

use ArrayIterator;
use PHPUnit_Framework_TestCase;

class SequentialKeyIteratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->internal = new ArrayIterator(array('a' => 'one', 'b' => 'two', 'c' => 'three'));
        $this->iterator = new SequentialKeyIterator($this->internal);
    }

    public function testIteration()
    {
        $result = array();

        foreach ($this->iterator as $index => $value) {
            $result[$index] = $value;
        }

        $this->assertSame(array(0 => 'one', 1 => 'two', 2 => 'three'), $result);
    }
}
