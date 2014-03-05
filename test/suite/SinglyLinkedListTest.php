<?php
namespace Icecave\Collections;

use Eloquent\Liberator\Liberator;

/**
 * @covers Icecave\Collections\SinglyLinkedList
 * @covers Icecave\Collections\Detail\LinkedListIterator
 */
class SinglyLinkedListTest extends AbstractLinkedListTest
{
    public function className()
    {
        return __NAMESPACE__ . '\\SinglyLinkedList';
    }

    public function verifyLinkIntegrity($collection)
    {
        // Verify size ...
        $this->assertSame(count($collection->elements()), $collection->size());

        // Verify tail node ...
        $head = Liberator::liberate($collection)->head;
        $tail = Liberator::liberate($collection)->tail;

        for ($node = $head; $node && null !== $node->next; $node = $node->next) {
            // no-op ...
        }

        $this->assertSame($tail, $node);
    }
}
