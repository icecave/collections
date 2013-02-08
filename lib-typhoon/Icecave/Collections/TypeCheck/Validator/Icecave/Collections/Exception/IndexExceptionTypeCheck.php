<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections\Exception;

class IndexExceptionTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!\is_int($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            );
        }
    }

}
