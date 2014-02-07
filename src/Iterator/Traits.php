<?php
namespace Icecave\Collections\Iterator;

/**
 * Describes the traits and capabilities of an iterator.
 */
final class Traits
{
    /**
     * @param boolean $isCountable  True if the iterator can be counted without iteration.
     * @param boolean $isRewindable True if the iterator can be rewound and re-iterated.
     */
    public function __construct(
        $isCountable,
        $isRewindable
    ) {
        $this->isCountable = $isCountable;
        $this->isRewindable = $isRewindable;
    }
    /**
     * @var boolean True if the iterator can be counted without iteration.
     */
    public $isCountable;

    /**
     * @var boolean True if the iterator can be rewound and re-iterated.
     */
    public $isRewindable;
}
