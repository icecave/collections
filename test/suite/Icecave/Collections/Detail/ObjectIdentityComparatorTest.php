<?php
namespace Icecave\Collections\Detail;

use PHPUnit_Framework_TestCase;
use stdClass;
use DateTime;

class ObjectIdentityComparatorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->comparator = new ObjectIdentityComparator;
    }

    public function testCompareSameObject()
    {
        $object = new stdClass;

        $this->assertSame(0, $this->comparator->__invoke($object, $object));
    }

    public function testCompareDifferentClass()
    {
        $this->assertLessThan(0, $this->comparator->__invoke(new DateTime, new stdClass));
    }

    public function testCompare()
    {
        $object1 = new stdClass;
        $object2 = new stdClass;

        $this->assertSame(
            strcmp(spl_object_hash($object1), spl_object_hash($object2)),
            $this->comparator->__invoke($object1, $object2)
        );
    }
}
