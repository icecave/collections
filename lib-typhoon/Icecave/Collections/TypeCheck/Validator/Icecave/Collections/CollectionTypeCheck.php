<?php
namespace Icecave\Collections\TypeCheck\Validator\Icecave\Collections;

class CollectionTypeCheck extends \Icecave\Collections\TypeCheck\AbstractValidator
{
    public function isEmpty(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\CollectionInterface');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\CollectionInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\CollectionInterface'
            );
        }
    }

    public function size(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Countable|Icecave\\Collections\\CollectionInterface');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Countable || $value instanceof \Icecave\Collections\CollectionInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Countable|Icecave\\Collections\\CollectionInterface'
            );
        }
    }

    public function get(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\AssociativeInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('key', 1, 'mixed');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\AssociativeInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\AssociativeInterface'
            );
        }
    }

    public function tryGet(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 3) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\AssociativeInterface');
            }
            if ($argumentCount < 2) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('key', 1, 'mixed');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('value', 2, 'mixed');
        } elseif ($argumentCount > 3) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\AssociativeInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\AssociativeInterface'
            );
        }
    }

    public function getWithDefault(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\IterableInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('key', 1, 'mixed');
        } elseif ($argumentCount > 3) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\IterableInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\IterableInterface'
            );
        }
    }

    public function hasKey(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\IterableInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('key', 1, 'mixed');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\IterableInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\IterableInterface'
            );
        }
    }

    public function contains(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\IterableInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('value', 1, 'mixed');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\IterableInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\IterableInterface'
            );
        }
    }

    public function keys(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\AssociativeInterface|Icecave\\Collections\\SequenceInterface');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\AssociativeInterface || $value instanceof \Icecave\Collections\SequenceInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\AssociativeInterface|Icecave\\Collections\\SequenceInterface'
            );
        }
    }

    public function values(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\AssociativeInterface|Icecave\\Collections\\SequenceInterface');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\AssociativeInterface || $value instanceof \Icecave\Collections\SequenceInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\AssociativeInterface|Icecave\\Collections\\SequenceInterface'
            );
        }
    }

    public function elements(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\CollectionInterface');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\CollectionInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\CollectionInterface'
            );
        }
    }

    public function map(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\IterableInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('transform', 1, 'callable');
        } elseif ($argumentCount > 3) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\IterableInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\IterableInterface'
            );
        }
        $value = $arguments[1];
        if (!\is_callable($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'transform',
                1,
                $arguments[1],
                'callable'
            );
        }
        if ($argumentCount > 2) {
            $value = $arguments[2];
            if (!(\is_array($value) || $value instanceof \ArrayAccess)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'result',
                    2,
                    $arguments[2],
                    'array|ArrayAccess'
                );
            }
        }
    }

    public function filter(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\IterableInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('predicate', 1, 'callable|null');
        } elseif ($argumentCount > 3) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(3, $arguments[3]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\IterableInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\IterableInterface'
            );
        }
        $value = $arguments[1];
        if (!(\is_callable($value) || $value === null)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'predicate',
                1,
                $arguments[1],
                'callable|null'
            );
        }
        if ($argumentCount > 2) {
            $value = $arguments[2];
            if (!(\is_array($value) || $value instanceof \ArrayAccess)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'result',
                    2,
                    $arguments[2],
                    'array|ArrayAccess'
                );
            }
        }
    }

    public function each(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\IterableInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('callback', 1, 'callable');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\IterableInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\IterableInterface'
            );
        }
        $value = $arguments[1];
        if (!\is_callable($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'callback',
                1,
                $arguments[1],
                'callable'
            );
        }
    }

    public function all(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\IterableInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('predicate', 1, 'callable');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\IterableInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\IterableInterface'
            );
        }
        $value = $arguments[1];
        if (!\is_callable($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'predicate',
                1,
                $arguments[1],
                'callable'
            );
        }
    }

    public function any(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\IterableInterface');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('predicate', 1, 'callable');
        } elseif ($argumentCount > 2) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(2, $arguments[2]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\IterableInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\IterableInterface'
            );
        }
        $value = $arguments[1];
        if (!\is_callable($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'predicate',
                1,
                $arguments[1],
                'callable'
            );
        }
    }

    public function isSequential(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|Traversable|Icecave\\Collections\\CollectionInterface');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Icecave\Collections\CollectionInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|Traversable|Icecave\\Collections\\CollectionInterface'
            );
        }
    }

    public function iteratorTraits(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('iterator', 0, 'array|Traversable|Countable|Icecave\\Collections\\Iterator\\TraitsProviderInterface');
        } elseif ($argumentCount > 1) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(1, $arguments[1]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \Traversable || $value instanceof \Countable || $value instanceof \Icecave\Collections\Iterator\TraitsProviderInterface)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'iterator',
                0,
                $arguments[0],
                'array|Traversable|Countable|Icecave\\Collections\\Iterator\\TraitsProviderInterface'
            );
        }
    }

    public function lowerBound(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 3) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|ArrayAccess');
            }
            if ($argumentCount < 2) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 1, 'mixed');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('comparator', 2, 'callable');
        } elseif ($argumentCount > 5) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(5, $arguments[5]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \ArrayAccess)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|ArrayAccess'
            );
        }
        $value = $arguments[2];
        if (!\is_callable($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'comparator',
                2,
                $arguments[2],
                'callable'
            );
        }
        if ($argumentCount > 3) {
            $value = $arguments[3];
            if (!\is_int($value)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'startIndex',
                    3,
                    $arguments[3],
                    'integer'
                );
            }
        }
        if ($argumentCount > 4) {
            $value = $arguments[4];
            if (!(\is_int($value) || $value === null)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'endIndex',
                    4,
                    $arguments[4],
                    'integer|null'
                );
            }
        }
    }

    public function upperBound(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 3) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|ArrayAccess');
            }
            if ($argumentCount < 2) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 1, 'mixed');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('comparator', 2, 'callable');
        } elseif ($argumentCount > 5) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(5, $arguments[5]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \ArrayAccess)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|ArrayAccess'
            );
        }
        $value = $arguments[2];
        if (!\is_callable($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'comparator',
                2,
                $arguments[2],
                'callable'
            );
        }
        if ($argumentCount > 3) {
            $value = $arguments[3];
            if (!\is_int($value)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'startIndex',
                    3,
                    $arguments[3],
                    'integer'
                );
            }
        }
        if ($argumentCount > 4) {
            $value = $arguments[4];
            if (!(\is_int($value) || $value === null)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'endIndex',
                    4,
                    $arguments[4],
                    'integer|null'
                );
            }
        }
    }

    public function binarySearch(array $arguments)
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 3) {
            if ($argumentCount < 1) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('collection', 0, 'array|ArrayAccess');
            }
            if ($argumentCount < 2) {
                throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('element', 1, 'mixed');
            }
            throw new \Icecave\Collections\TypeCheck\Exception\MissingArgumentException('comparator', 2, 'callable');
        } elseif ($argumentCount > 6) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentException(6, $arguments[6]);
        }
        $value = $arguments[0];
        if (!(\is_array($value) || $value instanceof \ArrayAccess)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'collection',
                0,
                $arguments[0],
                'array|ArrayAccess'
            );
        }
        $value = $arguments[2];
        if (!\is_callable($value)) {
            throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                'comparator',
                2,
                $arguments[2],
                'callable'
            );
        }
        if ($argumentCount > 3) {
            $value = $arguments[3];
            if (!\is_int($value)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'startIndex',
                    3,
                    $arguments[3],
                    'integer'
                );
            }
        }
        if ($argumentCount > 4) {
            $value = $arguments[4];
            if (!(\is_int($value) || $value === null)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'endIndex',
                    4,
                    $arguments[4],
                    'integer|null'
                );
            }
        }
        if ($argumentCount > 5) {
            $value = $arguments[5];
            if (!(\is_int($value) || $value === null)) {
                throw new \Icecave\Collections\TypeCheck\Exception\UnexpectedArgumentValueException(
                    'insertIndex',
                    5,
                    $arguments[5],
                    'integer|null'
                );
            }
        }
    }

}
