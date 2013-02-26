<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\Map;
use PHPUnit_Framework_TestCase;

class AssociativeIteratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_collection = new Map(array('a' => 1, 'b' => 2, 'c' => 3));
        $this->_iterator = new AssociativeIterator($this->_collection);
    }

    public function testIteration()
    {
        $result = array();

        foreach ($this->_iterator as $index => $value) {
            $result[$index] = $value;
        }

        $this->assertSame(array('a' => 1, 'b' => 2, 'c' => 3), $result);
    }

    public function testCollection()
    {
        $this->assertSame($this->_collection, $this->_iterator->collection());
    }
}
