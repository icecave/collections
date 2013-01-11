<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections;


class PriorityQueueTyphoon extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('prioritizer', 0, 'callable'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_callable($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'prioritizer',
                0,
                $arguments[0],
                'callable'
            ));
        }
        if (($argumentCount > 1))
        {
            ($value = $arguments[1]);
            ($check =             function ($value)
                        {
                            ($check =                 function ($value)
                                            {
                                                if (((!\is_array($value)) && (!($value instanceof \Traversable))))
                                                {
                                                    return false;
                                                }
                                                foreach ($value as $key => $subValue)
                                                {
                                                }
                                                return true;
                                            }
                            );
                            if (                function ($value)
                                            {
                                                if (((!\is_array($value)) && (!($value instanceof \Traversable))))
                                                {
                                                    return false;
                                                }
                                                foreach ($value as $key => $subValue)
                                                {
                                                }
                                                return true;
                                            }
                            )
                            {
                                return true;
                            }
                            return ($value === null);
                        }
            );
            if ((!$check($arguments[1])))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'collection',
                    1,
                    $arguments[1],
                    'mixed<mixed>|null'
                ));
            }
        }
    }
    public function validateToString(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function clear(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function next(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function push(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 0, 'mixed'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        if (($argumentCount > 1))
        {
            ($value = $arguments[1]);
            if ((!(\is_int($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'priority',
                    1,
                    $arguments[1],
                    'integer|null'
                ));
            }
        }
    }
    public function pop(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
}
