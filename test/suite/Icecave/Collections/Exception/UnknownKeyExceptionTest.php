<?php
namespace Icecave\Collections\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class UnknownKeyExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new UnknownKeyException('foo', $previous);

        $this->assertSame('Key "foo" does not exist.', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
