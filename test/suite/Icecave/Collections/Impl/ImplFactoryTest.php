<?php
namespace Icecave\Collections\Impl;

use PHPUnit_Framework_TestCase;

class ImplFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_factory = new ImplFactory('Native');
    }

    public function testClassName()
    {
        $this->assertSame('Icecave\Collections\Impl\Native\VectorImpl', $this->_factory->className('Vector'));
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Icecave\Collections\Impl\Native\VectorImpl', $this->_factory->create('Vector'));
    }
}
