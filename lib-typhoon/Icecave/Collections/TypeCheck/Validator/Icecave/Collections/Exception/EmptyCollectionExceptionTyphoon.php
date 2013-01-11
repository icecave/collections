<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections\Exception;


class EmptyCollectionExceptionTyphoon extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
}
