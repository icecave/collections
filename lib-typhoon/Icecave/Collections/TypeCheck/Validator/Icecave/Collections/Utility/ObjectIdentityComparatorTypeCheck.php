<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections\Utility;

class ObjectIdentityComparatorTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function compareObject(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('lhs', 0, 'object');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('rhs', 1, 'object');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!\is_object($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'lhs',
                0,
                $arguments[0],
                'object'
            );
        }
        $value = $arguments[1];
        if (!\is_object($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'rhs',
                1,
                $arguments[1],
                'object'
            );
        }
    }

    public function validateInvoke(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('lhs', 0, 'mixed');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('rhs', 1, 'mixed');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
    }

}
