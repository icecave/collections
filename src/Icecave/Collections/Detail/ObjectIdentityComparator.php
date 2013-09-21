<?php
namespace Icecave\Collections\Detail;

use Icecave\Collections\TypeCheck\TypeCheck;
use Icecave\Parity\Comparator\DeepComparator;

/**
 * A deep comparator with relaxed comparisons for objects.
 *
 * Objects are compared by identity, but otherwise behaves as a deep comparator.
 */
class ObjectIdentityComparator extends DeepComparator
{
    /**
     * @param object $lhs
     * @param object $rhs
     *
     * @return integer The result of the comparison.
     */
    protected function compareObject($lhs, $rhs)
    {
        TypeCheck::get(__CLASS__)->compareObject(func_get_args());

        if ($lhs === $rhs) {
            return 0;
        }

        $diff = strcmp(get_class($lhs), get_class($rhs));
        if ($diff !== 0) {
            return $diff;
        }

        return strcmp(
            spl_object_hash($lhs),
            spl_object_hash($rhs)
        );
    }

    /**
     * @param mixed $lhs
     * @param mixed $rhs
     *
     * @return integer The result of the comparison.
     */
    public function __invoke($lhs, $rhs)
    {
        TypeCheck::get(__CLASS__)->validateInvoke(func_get_args());

        return $this->compare($lhs, $rhs);
    }
}
