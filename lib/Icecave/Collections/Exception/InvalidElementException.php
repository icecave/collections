<?php
namespace Icecave\Collections\Exception;

use Exception;
use Icecave\Collections\TypeCheck\TypeCheck;
use Icecave\Repr\Repr;
use InvalidArgumentException;

/**
 * An element is unable to be stored in a collection.
 */
class InvalidElementException extends InvalidArgumentException implements CollectionExceptionInterface
{
    /**
     * @param mixed          $element  The invalid element.
     * @param Exception|null $previous The previous exception, if any.
     */
    public function __construct($element, Exception $previous = null)
    {
        TypeCheck::get(__CLASS__, func_get_args());

        parent::__construct('Invalid element: ' . Repr::repr($element) . '.', 0, $previous);
    }
}
