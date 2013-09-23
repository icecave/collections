<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections\Iterator;

class UnpackIteratorTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('iterator', 0, 'Iterator');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
    }

    public function getInnerIterator(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function current(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function key(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function next(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function rewind(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

    public function valid(array $arguments)
    {
        if (\count($arguments) > 0) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]);
        }
    }

}
