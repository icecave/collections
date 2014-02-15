<?php
namespace Icecave\Collections\Detail;

use Iterator;
use stdClass;

class LinkedListIterator implements Iterator
{
    /**
     * @param stdClass|null $head
     * @param stdClass|null $tail
     */
    public function __construct(stdClass $head = null, stdClass $tail = null)
    {
        $this->head = $head;
        $this->tail = $tail;

        $this->rewind();
    }

    /**
     * Fetch the current value.
     *
     * @return mixed The current value.
     */
    public function current()
    {
        return $this->currentNode->element;
    }

    /**
     * Fetch the current key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        return $this->currentIndex;
    }

    /**
     * Advance to the next element.
     */
    public function next()
    {
        $this->currentNode = $this->currentNode->next;
        ++$this->currentIndex;
    }

    /**
     * Rewind to the first element.
     */
    public function rewind()
    {
        $this->currentIndex = 0;
        $this->currentNode = $this->head;
    }

    /**
     * Check if the current element is valid.
     *
     * @return boolean True if the iterator points to a valid element; otherwise, false.
     */
    public function valid()
    {
        return null !== $this->currentNode;
    }

    private $head;
    private $tail;
    private $currentIndex;
    private $currentNode;
}
