<?php
namespace Icecave\Collections\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class EmptyCollectionExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new EmptyCollectionException($previous);

        $this->assertSame('Collection is empty.', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
