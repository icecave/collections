<?php
namespace Icecave\Collections\Detail;

use PHPUnit_Framework_TestCase;

class AssociativeKeyGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->generator = new AssociativeKeyGenerator;
    }

    public function testBoolean()
    {
        $this->assertSame('bt', $this->generator->generate(true));
        $this->assertSame('bf', $this->generator->generate(false));
    }

    public function testInteger()
    {
        $this->assertSame(12345, $this->generator->generate(12345));
    }

    public function testDouble()
    {
        $this->assertSame('d12345.67', $this->generator->generate(12345.67));
    }

    public function testString()
    {
        $this->assertSame('sfoo', $this->generator->generate('foo'));
    }

    public function testResource()
    {
        $resource = fopen(__FILE__, 'r');
        $this->assertRegExp('/^r[0-9]+$/', $this->generator->generate($resource));
    }

    public function testNull()
    {
        $this->assertSame('n', $this->generator->generate(null));
    }

    public function testObject()
    {
        $object = new \stdClass;
        $hash = spl_object_hash($object);
        $this->assertSame('o' . $hash, $this->generator->generate($object));
    }

    public function testArray()
    {
        $this->assertSame('v5755510604b6be7f9f748461c6c8d1f6', $this->generator->generate(array(1, 2, 3)));
    }

    public function testEmptyArray()
    {
        $this->assertSame('a', $this->generator->generate(array()));
    }

    public function testArrayInternalImplementation()
    {
        $value = array(1, 2, 3);
        $identity = function($value) { return $value; };
        $generator = new AssociativeKeyGenerator($identity);
        $this->assertSame('v1,2,3,', $generator->generate($value));
    }

    public function testArrayInternalImplementationWithAssociativeArray()
    {
        $value = array(
            'a' => 1,
            'b' => 2,
            'c' => 3
        );
        $identity = function($value) { return $value; };
        $generator = new AssociativeKeyGenerator($identity);
        $this->assertSame('asa,sb,sc,1,2,3,', $generator->generate($value));
    }

    public function testInvoke()
    {
        $this->assertSame($this->generator->generate('foo'), call_user_func($this->generator, 'foo'));
    }

    public function testAlternativeImplementations()
    {
        $generator = new AssociativeKeyGenerator('sha1', function($o) { return 'foo'; });

        $this->assertSame('ve90bd8e7dd9616850b36bb6d15ab5b6cb113abbb', $generator->generate(array(1, 2, 3)));
        $this->assertSame('ofoo', $generator->generate(new \stdClass));
    }
}
