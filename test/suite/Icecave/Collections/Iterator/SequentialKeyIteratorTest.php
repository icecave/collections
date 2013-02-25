<?php
namespace Icecave\Collections\Iterator;

use ArrayIterator;
use PHPUnit_Framework_TestCase;

class SequentialKeyIteratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_internal = new ArrayIterator(array('a' => 'one', 'b' => 'two', 'c' => 'three'));
        $this->_iterator = new SequentialKeyIterator($this->_internal);
    }

    public function testIteration()
    {
        $result = array();

        foreach ($this->_iterator as $index => $value) {
            $result[$index] = $value;
        }

        $this->assertSame(array(0 => 'one', 1 => 'two', 2 => 'three'), $result);
    }
}
