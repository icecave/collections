<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class MapTest extends PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $collection = new Map;

        $collection->set('a', 1);
        $collection->set('b', 2);
        $collection->set('c', 3);

        $packet = serialize($collection);
        $unserializedCollection = unserialize($packet);

        $this->assertSame(
            Liberator::liberate($unserializedCollection)->elements->elements(),
            Liberator::liberate($collection)->elements->elements()
        );
    }

    public function testSerializationOfComparator()
    {
        $collection = new Map(null, 'strcmp');

        $packet = serialize($collection);
        $collection = unserialize($packet);

        $this->assertSame('strcmp', Liberator::liberate($collection)->comparator);
    }

    public function testCanCompare()
    {
        $collection = new Map;

        $this->assertTrue($collection->canCompare(new Map));
        $this->assertFalse($collection->canCompare(new Map(null, function() {})));
        $this->assertFalse($collection->canCompare(array()));
    }
}
