<?php
namespace Icecave\Collections\Support;

use PHPUnit_Framework_TestCase;
use stdClass;

class StringifyTest extends PHPUnit_Framework_TestCase
{
    public function testNull()
    {
        $this->assertSame('<null>', Stringify::stringify(null));
    }

    public function testBoolean()
    {
        $this->assertSame('<true>', Stringify::stringify(true));
        $this->assertSame('<false>', Stringify::stringify(false));
    }

    public function testScalar()
    {
        $this->assertSame("'foo'", Stringify::stringify('foo'));
        $this->assertSame('25', Stringify::stringify(25));
        $this->assertSame('99.5', Stringify::stringify(99.5));
    }

    public function testObject()
    {
        $object = new stdClass;
        $this->assertSame('<stdClass @ ' . spl_object_hash($object) . '>', Stringify::stringify($object));
    }

    public function testResource()
    {
        $resource = fopen('php://memory', 'w');
        $this->assertSame('<' . strval($resource) . '>', Stringify::stringify($resource));
        fclose($resource);
    }

    public function testOther()
    {
        $this->assertSame('<array>', Stringify::stringify(array()));
    }
}
