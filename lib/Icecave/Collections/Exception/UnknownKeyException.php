<?php
namespace Icecave\Collections\Exception;

use Exception;
use Icecave\Collections\Support\Stringify;
use OutOfBoundsException;

/**
 * The key of an associative collection was not found in the set of existing keys.
 */
class UnknownKeyException extends OutOfBoundsException implements CollectionExceptionInterface
{
    /**
     * @param mixed $key The unknown key.
     * @param Exception|null The previous exception, if any.
     */
    public function __construct($key, Exception $previous = null)
    {
        parent::__construct('Key ' . Stringify::stringify($key) . ' does not exist.', 0, $previous);
    }
}
