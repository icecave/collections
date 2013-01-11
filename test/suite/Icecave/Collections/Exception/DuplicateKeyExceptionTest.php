<?php
namespace Icecave\Collections\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class DuplicateKeyExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new DuplicateKeyException('foo', $previous);

        $this->assertSame('Key "foo" already exists.', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
