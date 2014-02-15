<?php
namespace Icecave\Collections\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class IndexExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new IndexException(25, $previous);

        $this->assertSame("Index 25 is out of range.", $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
