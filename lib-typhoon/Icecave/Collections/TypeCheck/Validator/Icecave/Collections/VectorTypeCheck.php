<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections;


class VectorTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
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
    public function validateClone(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
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
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        if (($argumentCount > 1))
        {
            ($value = $arguments[1]);
            if ((!\is_int($value)))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'startIndex',
                    1,
                    $arguments[1],
                    'integer'
                ));
            }
        }
    }
    public function indexOfLast(array $arguments)
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
                    'startIndex',
                    1,
                    $arguments[1],
                    'integer|null'
                ));
            }
        }
    }
    public function find(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('predicate', 0, 'callable'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_callable($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'predicate',
                0,
                $arguments[0],
                'callable'
            ));
        }
        if (($argumentCount > 1))
        {
            ($value = $arguments[1]);
            if ((!\is_int($value)))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'startIndex',
                    1,
                    $arguments[1],
                    'integer'
                ));
            }
        }
    }
    public function findLast(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('predicate', 0, 'callable'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!\is_callable($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'predicate',
                0,
                $arguments[0],
                'callable'
            ));
        }
        if (($argumentCount > 1))
        {
            ($value = $arguments[1]);
            if ((!(\is_int($value) || ($value === null))))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'startIndex',
                    1,
                    $arguments[1],
                    'integer|null'
                ));
            }
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
    public function offsetExists(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('offset', 0, 'mixed'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
    }
    public function offsetGet(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('offset', 0, 'integer'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'offset',
                0,
                $arguments[0],
                'integer'
            ));
        }
    }
    public function offsetSet(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('offset', 0, 'integer|null'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('value', 1, 'mixed'));
        }
        elseif (($argumentCount > 2))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]));
        }
        ($value = $arguments[0]);
        if ((!(\is_int($value) || ($value === null))))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'offset',
                0,
                $arguments[0],
                'integer|null'
            ));
        }
    }
    public function offsetUnset(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('offset', 0, 'integer'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'offset',
                0,
                $arguments[0],
                'integer'
            ));
        }
    }
    public function serialize(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function unserialize(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('packet', 0, 'string'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        if ((!\is_string($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'packet',
                0,
                $arguments[0],
                'string'
            ));
        }
    }
    public function capacity(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
        }
    }
    public function reserve(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('size', 0, 'integer'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
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
    public function shrink(array $arguments)
    {
        if ((\count($arguments) > 0))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(0, $arguments[0]));
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
    public function shiftLeft(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('count', 1, 'integer'));
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
    public function shiftRight(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 2))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('index', 0, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('count', 1, 'integer'));
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
    public function clamp(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 3))
        {
            if (($argumentCount < 1))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('value', 0, 'integer|null'));
            }
            if (($argumentCount < 2))
            {
                throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('min', 1, 'integer'));
            }
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('max', 2, 'integer'));
        }
        elseif (($argumentCount > 3))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]));
        }
        ($value = $arguments[0]);
        if ((!(\is_int($value) || ($value === null))))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'value',
                0,
                $arguments[0],
                'integer|null'
            ));
        }
        ($value = $arguments[1]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'min',
                1,
                $arguments[1],
                'integer'
            ));
        }
        ($value = $arguments[2]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'max',
                2,
                $arguments[2],
                'integer'
            ));
        }
    }
    public function expand(array $arguments)
    {
        ($argumentCount = \count($arguments));
        if (($argumentCount < 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('count', 0, 'integer'));
        }
        elseif (($argumentCount > 1))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]));
        }
        ($value = $arguments[0]);
        if ((!\is_int($value)))
        {
            throw (new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'count',
                0,
                $arguments[0],
                'integer'
            ));
        }
    }
}
