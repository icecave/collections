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

}
