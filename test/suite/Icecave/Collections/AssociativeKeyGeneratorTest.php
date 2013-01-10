<?php
namespace Icecave\Collections;

use PHPUnit_Framework_TestCase;

class AssociativeKeyGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_generator = new AssociativeKeyGenerator;
    }

    public function testBoolean()
    {
        $this->assertSame('bt', $this->_generator->generate(true));
        $this->assertSame('bf', $this->_generator->generate(false));
    }

    public function testInteger()
    {
        $this->assertSame(12345, $this->_generator->generate(12345));
    }

    public function testDouble()
    {
        $this->assertSame('d12345.67', $this->_generator->generate(12345.67));
    }

    public function testString()
    {
        $this->assertSame('sfoo', $this->_generator->generate('foo'));
    }

    public function testResource()
    {
        $resource = fopen(__FILE__, 'r');
        $this->assertRegExp('/^r[0-9]+$/', $this->_generator->generate($resource));
    }

    public function testNull()
    {
        $this->assertSame('n', $this->_generator->generate(null));
    }

    public function testObject()
    {
        $object = new \stdClass;
        $hash = spl_object_hash($object);
        $this->assertSame('o' . $hash, $this->_generator->generate($object));
    }

    public function testArray()
    {
        $this->assertSame('v5755510604b6be7f9f748461c6c8d1f6', $this->_generator->generate(array(1, 2, 3)));
    }

    public function testEmptyArray()
    {
        $this->assertSame('a', $this->_generator->generate(array()));
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
        $this->assertSame($this->_generator->generate('foo'), call_user_func($this->_generator, 'foo'));
    }

    public function testAlternativeImplementations()
    {
        $generator = new AssociativeKeyGenerator('sha1', function($o) { return 'foo'; });

        $this->assertSame('ve90bd8e7dd9616850b36bb6d15ab5b6cb113abbb', $generator->generate(array(1, 2, 3)));
        $this->assertSame('ofoo', $generator->generate(new \stdClass));
    }
}
