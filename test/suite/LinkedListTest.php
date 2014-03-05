<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;

/**
 * @covers Icecave\Collections\LinkedList
 * @covers Icecave\Collections\Detail\LinkedListIterator
 */
class LinkedListTest extends AbstractLinkedListTest
{
    public function className()
    {
        return __NAMESPACE__ . '\\LinkedList';
    }

    public function verifyLinkIntegrity($collection)
    {
        // Verify size ...
        $this->assertSame(count($collection->elements()), $collection->size());

        // Verify tail node ...
        $head = Liberator::liberate($collection)->head;
        $tail = Liberator::liberate($collection)->tail;

        for ($node = $head; $node && null !== $node->next; $node = $node->next) {
            if ($node->next) {
                $this->assertSame($node, $node->next->prev);
            } elseif ($node->prev) {
                $this->assertSame($node, $node->prev->next);
            }
        }

        $this->assertSame($tail, $node);
    }
}
