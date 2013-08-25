<?php
namespace Icecave\Collections\Iterator;

use Icecave\Collections\HashMap;
use PHPUnit_Framework_TestCase;

class AssociativeIteratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->collection = new HashMap(array('a' => 1, 'b' => 2, 'c' => 3));
        $this->iterator = new AssociativeIterator($this->collection);
    }

    public function testIteration()
    {
        $result = array();

        foreach ($this->iterator as $index => $value) {
            $result[$index] = $value;
        }

        $this->assertSame(array('a' => 1, 'b' => 2, 'c' => 3), $result);
    }

    public function testCollection()
    {
        $this->assertSame($this->collection, $this->iterator->collection());
    }
}
