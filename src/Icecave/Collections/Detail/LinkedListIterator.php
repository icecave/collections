<?php
namespace Icecave\Collections\Detail;

use Icecave\Collections\TypeCheck\TypeCheck;
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
        $this->typeCheck = TypeCheck::get(__CLASS__, func_get_args());

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
        $this->typeCheck->current(func_get_args());

        return $this->currentNode->element;
    }

    /**
     * Fetch the current key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        $this->typeCheck->key(func_get_args());

        return $this->currentIndex;
    }

    /**
     * Advance to the next element.
     */
    public function next()
    {
        $this->typeCheck->next(func_get_args());

        $this->currentNode = $this->currentNode->next;
        ++$this->currentIndex;
    }

    /**
     * Rewind to the first element.
     */
    public function rewind()
    {
        $this->typeCheck->rewind(func_get_args());

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
        $this->typeCheck->valid(func_get_args());

        return null !== $this->currentNode;
    }

    private $typeCheck;
    private $head;
    private $tail;
    private $currentIndex;
    private $currentNode;
}
