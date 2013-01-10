<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\Vector;
use PHPUnit_Framework_TestCase;

class RandomAccessIteratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_collection = new Vector(array(1, 2, 3));
        $this->_iterator = new RandomAccessIterator($this->_collection);
    }

    public function testIteration()
    {
        foreach ($this->_iterator as $index => $value) {
            $result[$index] = $value;
        }

        $this->assertsame(array(1, 2, 3), $result);
    }

    public function testCollection()
    {
        $this->assertSame($this->_collection, $this->_iterator->collection());
    }
}
