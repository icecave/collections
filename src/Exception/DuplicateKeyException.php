<?php
namespace Icecave\Collections\Exception;

use Exception;
use Icecave\Repr\Repr;
use RuntimeException;

/**
 * The key of an associative collection already exists in the set of existing keys.
 */
class DuplicateKeyException extends RuntimeException implements CollectionExceptionInterface
{
    /**
     * @param mixed          $key      The unknown key.
     * @param Exception|null $previous The previous exception, if any.
     */
    public function __construct($key, Exception $previous = null)
    {
        parent::__construct('Key ' . Repr::repr($key) . ' already exists.', 0, $previous);
    }
}
