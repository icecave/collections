<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\Vector;
use PHPUnit_Framework_TestCase;

class RandomAccessIteratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->collection = new Vector(array(1, 2, 3));
        $this->iterator = new RandomAccessIterator($this->collection);
    }

    public function testIteration()
    {
        $result = array();

        foreach ($this->iterator as $index => $value) {
            $result[$index] = $value;
        }

        $this->assertSame(array(1, 2, 3), $result);
    }

    public function testCollection()
    {
        $this->assertSame($this->collection, $this->iterator->collection());
    }
}
