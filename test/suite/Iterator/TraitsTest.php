<?php
namespace Icecave\Collections\Iterator;

use PHPUnit_Framework_TestCase;

class TraitsTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $traits = new Traits(true, false);

        $this->assertTrue($traits->isCountable);
        $this->assertFalse($traits->isRewindable);
    }
}
