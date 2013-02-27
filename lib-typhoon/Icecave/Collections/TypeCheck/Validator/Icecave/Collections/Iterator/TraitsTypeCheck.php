<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections\Iterator;

class TraitsTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('isCountable', 0, 'boolean');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('isRewindable', 1, 'boolean');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!\is_bool($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'isCountable',
                0,
                $arguments[0],
                'boolean'
            );
        }
        $value = $arguments[1];
        if (!\is_bool($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'isRewindable',
                1,
                $arguments[1],
                'boolean'
            );
        }
    }

}
