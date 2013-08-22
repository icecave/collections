<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use Icecave\Collections\Iterator\Traits;
use PHPUnit_Framework_TestCase;
use stdClass;

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
}
