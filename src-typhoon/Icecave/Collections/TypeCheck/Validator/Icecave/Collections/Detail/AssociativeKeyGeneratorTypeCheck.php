<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections\Detail;

class AssociativeKeyGeneratorTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        if ($argumentCount > 0) {
            $value = $arguments[0];
            if (!\is_callable($value)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'arrayHashFunction',
                    0,
                    $arguments[0],
                    'callable'
                );
            }
        }
        if ($argumentCount > 1) {
            $value = $arguments[1];
            if (!\is_callable($value)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'objectHashFunction',
                    1,
                    $arguments[1],
                    'callable'
                );
            }
        }
    }

    public function validateInvoke(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('value', 0, 'mixed');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
    }

    public function generate(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('value', 0, 'mixed');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
    }

    public function generateForArray(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('value', 0, 'array');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
    }

}
