<?php
namespace Icecave\Collections;

use Icecave\Collections\TypeCheck\TypeCheck;

/**
 * A basic associative key generator that allows for keys of any type.
 *
 * Given any value, generates a value suitable for use as an identifying key in a PHP array.
 */
class AssociativeKeyGenerator
{
    /**
     * @param callable $arrayHashFunction  The function to use for generating a hash of array keys and values.
     * @param callable $objectHashFunction The function to use for generating a hash of objects.
     */
    public function __construct($arrayHashFunction = 'md5', $objectHashFunction = 'spl_object_hash')
    {
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

        $this->arrayHashFunction = $arrayHashFunction;
        $this->objectHashFunction = $objectHashFunction;
    }

    /**
     * Generate a suitable key value for use in a PHP array.
     *
     * @param mixed $value The value for which a key is required.
     *
     * @return int|string The key to use.
     */
    public function __invoke($value)
    {
        $this->typeCheck->validateInvoke(func_get_args());

        return $this->generate($value);
    }

    /**
     * Generate a suitable key value for use in a PHP array.
     *
     * @param mixed $value The value for which a key is required.
     *
     * @return integer|string The key to use.
     */
    public function generate($value)
    {
        $this->typeCheck->generate(func_get_args());

        switch (gettype($value)) {
            case 'boolean':
                return 'b' . ($value ? 't' : 'f');
            case 'integer':
                return $value;
            case 'double':
                return 'd' . $value;
            case 'string':
                return 's' . $value;
            case 'resource':
                return 'r' . intval($value);
            case 'NULL':
                return 'n';
            case 'object':
                return 'o' . call_user_func($this->objectHashFunction, $value);
        }

        return $this->generateForArray($value);
    }

    /**
     * Generate a suitable key value for use in a PHP array.
     *
     * @param array $value The value for which a key is required.
     *
     * @return integer|string The key to use.
     */
    protected function generateForArray(array $value)
    {
        $this->typeCheck->generateForArray(func_get_args());

        if (empty($value)) {
            return 'a';
        }

        $keyHashes = '';
        $valueHashes = '';
        $isAssociative = false;
        $nextIndex = 0;

        foreach ($value as $key => $value) {
            $keyHashes .= $this->generate($key) . ',';
            $valueHashes .= $this->generate($value) . ',';

            if (!$isAssociative && $key !== $nextIndex++) {
                $isAssociative = true;
            }
        }

        if ($isAssociative) {
            return 'a' . call_user_func($this->arrayHashFunction, $keyHashes . $valueHashes);
        }

        return 'v' . call_user_func($this->arrayHashFunction, $valueHashes);
    }

    private $typeCheck;
    private $arrayHashFunction;
    private $objectHashFunction;
}
