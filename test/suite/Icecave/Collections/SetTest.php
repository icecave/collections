<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class SetTest extends PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $collection = new Set(array(1, 2, 3));

        $packet = serialize($collection);
        $unserializedCollection = unserialize($packet);

        $this->assertSame(
            Liberator::liberate($unserializedCollection)->elements->elements(),
            Liberator::liberate($collection)->elements->elements()
        );
    }

    public function testSerializationOfComparator()
    {
        $collection = new Set(null, 'strcmp');

        $packet = serialize($collection);
        $collection = unserialize($packet);

        $this->assertSame('strcmp', Liberator::liberate($collection)->comparator);
    }

    public function testCanCompare()
    {
        $collection = new Set;

        $this->assertTrue($collection->canCompare(new Set));
        $this->assertFalse($collection->canCompare(new Set(null, function () {})));
        $this->assertFalse($collection->canCompare(array()));
    }
}
