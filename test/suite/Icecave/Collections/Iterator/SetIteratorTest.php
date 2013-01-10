<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\Set;
use PHPUnit_Framework_TestCase;

class SetIteratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_collection = new Set(array('a', 'b', 'c'));
        $this->_iterator = new SetIterator($this->_collection);
    }

    public function testIteration()
    {
        $result = array();

        foreach ($this->_iterator as $index => $value) {
            $result[$index] = $value;
        }

        $this->assertsame(array('a', 'b', 'c'), $result);
    }

    public function testCollection()
    {
        $this->assertSame($this->_collection, $this->_iterator->collection());
    }
}
