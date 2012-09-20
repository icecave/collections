<?php
namespace Icecave\Collections\Support;

abstract class Stringify
{
    /**
     * Get a simple, predictable string representation of any type.
     *
     * @param mixed $value The value to stringify.
     *
     * @return string The string representation of $value.
     */
    public static function stringify($value)
    {
        if (null === $value || is_bool($value)) {
            return '<' . var_export($value, true) . '>';
        } elseif (is_scalar($value)) {
            return var_export($value, true);
        } elseif (is_object($value)) {
            return '<' . get_class($value) . ' @ ' . spl_object_hash($value) . '>';
        } elseif (is_resource($value)) {
            return '<' . $value . '>';
        } else {
            return '<' . gettype($value) . '>';
        }
    }
}
