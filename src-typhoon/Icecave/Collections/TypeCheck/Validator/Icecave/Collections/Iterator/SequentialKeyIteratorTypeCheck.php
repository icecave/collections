<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections\Iterator;

class SequentialKeyIteratorTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('iterator', 0, 'Traversable');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
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

}
