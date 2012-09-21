<?php
namespace Icecave\Collections\Exception;

use Exception;
use UnderflowException;

/**
 * An operation requiring an element was performed on an empty collection.
 */
class EmptyCollectionException extends UnderflowException implements ICollectionException
{
    /**
     * @param Exception|null The previous exception, if any.
     */
    public function __construct(Exception $previous = null)
    {
        parent::__construct('Collection is empty.', 0, $previous);
    }
}
