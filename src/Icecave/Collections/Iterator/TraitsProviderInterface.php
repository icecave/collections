<?php
namespace Icecave\Collections\Iterator;

interface TraitsProviderInterface
{
    /**
     * Return traits describing the collection's iteration capabilities.
     *
     * @return Traits
     */
    public function iteratorTraits();
}
