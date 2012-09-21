<?php
namespace Icecave\Collections\Exception;

use Exception;
use Icecave\Collections\Support\Stringify;
use RuntimeException;

/**
 * The key of an associative collection already exists in the set of existing keys.
 */
class DuplicateKeyException extends RuntimeException implements ICollectionException
{
    /**
     * @param mixed $key The unknown key.
     * @param Exception|null The previous exception, if any.
     */
    public function __construct($key, Exception $previous = null)
    {
        parent::__construct('Key ' . Stringify::stringify($key) . ' already exists.', 0, $previous);
    }
}
