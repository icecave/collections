<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;
use PHPUnit_Framework_TestCase;

class HashSetTest extends PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $collection = new HashSet(array(1, 2, 3));

        $packet = serialize($collection);
        $unserializedCollection = unserialize($packet);

        $this->assertSame(
            Liberator::liberate($unserializedCollection)->elements,
            Liberator::liberate($collection)->elements
        );
    }

    /**
     * @group regression
     * @link https://github.com/IcecaveStudios/collections/issues/23
     */
    public function testSerializationOfHashFunction()
    {
        $collection = new HashSet(null, 'sha1');

        $packet = serialize($collection);
        $collection = unserialize($packet);

        $this->assertSame('sha1', Liberator::liberate($collection)->hashFunction);
    }

    public function testCanCompare()
    {
        $collection = new HashSet;

        $this->assertTrue($collection->canCompare(new HashSet));
        $this->assertFalse($collection->canCompare(new HashSet(null, function() {})));
        $this->assertFalse($collection->canCompare(array()));
    }
}
