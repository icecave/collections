<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections\Exception;

class InvalidElementExceptionTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 0, 'mixed');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
    }

}
