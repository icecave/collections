<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections;


class LinkedListTyphoon extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function validateConstruct(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        if (($argumentCount > 0))
        {
            ($value = $arguments[0]);
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
            if ((!$check($arguments[0])))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'collection',
                    0,
                    $arguments[0],
                    'mixed<mixed>|null'
                ));
            }
        }
    }
    public function size(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function isEmpty(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
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
    public function elements(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function contains(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 0, 'mixed'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function filtered(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        if (($argumentCount > 0))
        {
            ($value = $arguments[0]);
            if ((!(\is_callable($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'predicate',
                    0,
                    $arguments[0],
                    'callable|null'
                ));
            }
        }
    }
    public function map(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('transform', 0, 'callable'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        if ((!\is_callable($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'transform',
                0,
                $arguments[0],
                'callable'
            ));
        }
    }
    public function filter(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        if (($argumentCount > 0))
        {
            ($value = $arguments[0]);
            if ((!(\is_callable($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'predicate',
                    0,
                    $arguments[0],
                    'callable|null'
                ));
            }
        }
    }
    public function apply(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('transform', 0, 'callable'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        if ((!\is_callable($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'transform',
                0,
                $arguments[0],
                'callable'
            ));
        }
    }
    public function front(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function tryFront(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 0, 'mixed'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function back(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function tryBack(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 0, 'mixed'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function sorted(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        if (($argumentCount > 0))
        {
            ($value = $arguments[0]);
            if ((!(\is_callable($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'comparator',
                    0,
                    $arguments[0],
                    'callable|null'
                ));
            }
        }
    }
    public function reversed(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function join(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('sequence', 0, 'mixed<mixed>'));
        }
        ($value = $arguments[0]);
        ($check =         function ($value)
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
        if ((!$check($arguments[0])))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'sequence',
                0,
                $arguments[0],
                'mixed<mixed>'
            ));
        }
        if (($argumentCount > 1))
        {
            ($check =             function ($argument, $index)
                        {
                            ($value = $argument);
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
                            if ((!$check($argument)))
                            {
                                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                                    'additional',
                                    $index,
                                    $argument,
                                    'mixed<mixed>'
                                ));
                            }
                        }
            );
            for (($index = 1); ($index < $argumentCount); ($index++))
            {
                $check($arguments[$index], $index);
            }
        }
    }
    public function sort(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        if (($argumentCount > 0))
        {
            ($value = $arguments[0]);
            if ((!(\is_callable($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'comparator',
                    0,
                    $arguments[0],
                    'callable|null'
                ));
            }
        }
    }
    public function reverse(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function append(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('sequence', 0, 'mixed<mixed>'));
        }
        ($value = $arguments[0]);
        ($check =         function ($value)
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
        if ((!$check($arguments[0])))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'sequence',
                0,
                $arguments[0],
                'mixed<mixed>'
            ));
        }
        if (($argumentCount > 1))
        {
            ($check =             function ($argument, $index)
                        {
                            ($value = $argument);
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
                            if ((!$check($argument)))
                            {
                                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                                    'additional',
                                    $index,
                                    $argument,
                                    'mixed<mixed>'
                                ));
                            }
                        }
            );
            for (($index = 1); ($index < $argumentCount); ($index++))
            {
                $check($arguments[$index], $index);
            }
        }
    }
    public function pushFront(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 0, 'mixed'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function popFront(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function tryPopFront(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function pushBack(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 0, 'mixed'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function popBack(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function tryPopBack(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function resize(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('size', 0, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'size',
                0,
                $arguments[0],
                'integer'
            ));
        }
    }
    public function get(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'mixed'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function slice(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
        if (($argumentCount > 1))
        {
            ($value = $arguments[1]);
            if ((!(\is_int($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'count',
                    1,
                    $arguments[1],
                    'integer|null'
                ));
            }
        }
    }
    public function range(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('begin', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('end', 1, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'begin',
                0,
                $arguments[0],
                'integer'
            ));
        }
        ($value = $arguments[1]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'end',
                1,
                $arguments[1],
                'integer'
            ));
        }
    }
    public function indexOf(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 0, 'mixed'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function set(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 1, 'mixed'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
    }
    public function insert(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 1, 'mixed'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
    }
    public function insertMany(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('elements', 1, 'mixed<mixed>'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
        ($value = $arguments[1]);
        ($check =         function ($value)
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
        if ((!$check($arguments[1])))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'elements',
                1,
                $arguments[1],
                'mixed<mixed>'
            ));
        }
    }
    public function remove(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
    }
    public function removeMany(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
        if (($argumentCount > 1))
        {
            ($value = $arguments[1]);
            if ((!(\is_int($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'count',
                    1,
                    $arguments[1],
                    'integer|null'
                ));
            }
        }
    }
    public function removeRange(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('begin', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('end', 1, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'begin',
                0,
                $arguments[0],
                'integer'
            ));
        }
        ($value = $arguments[1]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'end',
                1,
                $arguments[1],
                'integer'
            ));
        }
    }
    public function replace(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('elements', 1, 'mixed<mixed>'));
        }
        elseif (($argumentCount > 3))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
        ($value = $arguments[1]);
        ($check =         function ($value)
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
        if ((!$check($arguments[1])))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'elements',
                1,
                $arguments[1],
                'mixed<mixed>'
            ));
        }
        if (($argumentCount > 2))
        {
            ($value = $arguments[2]);
            if ((!(\is_int($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'count',
                    2,
                    $arguments[2],
                    'integer|null'
                ));
            }
        }
    }
    public function replaceRange(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 3))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('begin', 0, 'integer'));
            }
            if (($argumentCount < 2))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('end', 1, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('elements', 2, 'mixed<mixed>'));
        }
        elseif (($argumentCount > 3))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'begin',
                0,
                $arguments[0],
                'integer'
            ));
        }
        ($value = $arguments[1]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'end',
                1,
                $arguments[1],
                'integer'
            ));
        }
        ($value = $arguments[2]);
        ($check =         function ($value)
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
        if ((!$check($arguments[2])))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'elements',
                2,
                $arguments[2],
                'mixed<mixed>'
            ));
        }
    }
    public function swap(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index1', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index2', 1, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index1',
                0,
                $arguments[0],
                'integer'
            ));
        }
        ($value = $arguments[1]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index2',
                1,
                $arguments[1],
                'integer'
            ));
        }
    }
    public function trySwap(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index1', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index2', 1, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index1',
                0,
                $arguments[0],
                'integer'
            ));
        }
        ($value = $arguments[1]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index2',
                1,
                $arguments[1],
                'integer'
            ));
        }
    }
    public function count(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function current(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function key(array $arguments)
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
    public function rewind(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function valid(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function doSwap(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index1', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index2', 1, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index1',
                0,
                $arguments[0],
                'integer'
            ));
        }
        ($value = $arguments[1]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index2',
                1,
                $arguments[1],
                'integer'
            ));
        }
    }
    public function validateIndex(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
        if (($argumentCount > 1))
        {
            ($value = $arguments[1]);
            if ((!(\is_int($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'max',
                    1,
                    $arguments[1],
                    'integer|null'
                ));
            }
        }
    }
    public function createNode(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
    }
    public function nodeAt(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'index',
                0,
                $arguments[0],
                'integer'
            ));
        }
    }
    public function nodeFrom(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('node', 0, 'stdClass'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('count', 1, 'integer'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[1]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'count',
                1,
                $arguments[1],
                'integer'
            ));
        }
    }
    public function cloneNodes(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('start', 0, 'stdClass'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
    }
    public function createNodes(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('elements', 0, 'mixed<mixed>'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        ($check =         function ($value)
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
        if ((!$check($arguments[0])))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'elements',
                0,
                $arguments[0],
                'mixed<mixed>'
            ));
        }
    }
}
