<?php
namespace Icecave\Collections\Impl\Native;

use SplFixedArray;

class VectorImpl
{
    public function __construct()
    {
        $this->elements = new SplFixedArray;
        $this->size = 0;
    }

    public function __clone()
    {
        $this->elements = clone $this->elements;
    }

    public function size()
    {
        return $this->size;
    }

    public function clear()
    {
        $this->elements = new SplFixedArray;
        $this->size = 0;
    }

    /**
     * Fetch the element at the given index.
     */
    public function get($index)
    {
        return $this->elements[$index];
    }

    /**
     * Replace the element at a particular position in the sequence.
     */
    public function set($index, $element)
    {
        $this->elements[$index] = $element;
    }

    /**
     * @return array An array containing the elements in the collection.
     */
    public function elements()
    {
        $elements = array();

        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                $elements[] = $element;
            }
        }

        return $elements;
    }

    /**
     * Fetch a new collection with a subset of the elements from this collection.
     */
    public function filter($predicate, VectorImpl $input, VectorImpl $output)
    {
        // Cache size in case input === output ...
        $size = $input->size;

        $output->reserve($input->size);
        $output->size = 0;

        foreach ($input->elements as $index => $element) {
            if ($index >= count($output->elements)) {
                break;
            } elseif ($index >= $size) {
                $output->elements[$index] = null;
            } elseif (call_user_func($predicate, $element)) {
                $output->elements[$output->size++] = $element;
            }
        }
    }

    /**
     * Produce a new collection by applying a transformation to each element.
     */
    public function map($transform, VectorImpl $input, VectorImpl $output)
    {
        $output->resize($input->size);

        foreach ($input->elements as $index => $element) {
            if ($index >= $input->size) {
                break;
            } else {
                $output->elements[$index] = call_user_func($transform, $element);
            }
        }
    }

    /**
     * Partitions this collection into two collections according to a predicate.
     *
     * @param callable   $predicate A predicate function used to determine which partitioned collection to place the elements in.
     * @param VectorImpl $left
     * @param VectorImpl $right
     */
    public function partition($predicate, VectorImpl $left, VectorImpl $right)
    {
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } elseif (call_user_func($predicate, $element)) {
                $left->insert($left->size, $element);
            } else {
                $right->insert($right->size, $element);
            }
        }
    }

    /**
     * Create a new sequence with the elements from this sequence in sorted order.
     */
    public function sort($comparator, VectorImpl $input, VectorImpl $output)
    {
        $elements = $input->elements();

        usort($elements, $comparator);

        $output->elements = SplFixedArray::fromArray($elements);
        $output->size = $input->size;
    }

    /**
     * Returns true if the given predicate returns true for all elements.
     *
     * The loop is short-circuited, exiting after the first element for which the predicate returns false.
     *
     * @param callable $predicate
     *
     * @return boolean True if $predicate($element) returns true for all elements; otherwise, false.
     */
    public function all($predicate)
    {
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } elseif (!call_user_func($predicate, $element)) {
                return false;
            }
        }

        return true;
    }

    public function reverse(VectorImpl $result)
    {
        $result->resize($this->size);

        $target = $this->size - 1;
        foreach ($this->elements as $index => $element) {
            if ($index >= $this->size) {
                break;
            } else {
                $result->elements[$target--] = $element;
            }
        }
    }

    public function reverseInPlace()
    {
        $first = 0;
        $last  = $this->size;

        while (($first !== $last) && ($first !== --$last)) {
            $temp = $this->elements[$first];
            $this->elements[$first] = $this->elements[$last];
            $this->elements[$last] = $temp;
            ++$first;
        }
    }

    public function range($begin, $end, VectorImpl $result)
    {
        if ($begin < $end) {
            $result->resize($end - $begin);

            $index = 0;
            while ($index < $result->size()) {
                $result->elements[$index++] = $this->elements[$begin++];
            }
        }
    }

    /**
     * Find the index of the first instance of an element matching given criteria.
     *
     * @param callable $predicate  A predicate function used to determine which element constitutes a match.
     * @param integer  $startIndex The index to start searching from.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $startIndex is out of range.
     */
    public function find($predicate, $startIndex = 0)
    {
        for ($index = $startIndex; $index < $this->size; ++$index) {
            if (call_user_func($predicate, $this->elements[$index])) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Find the index of the last instance of an element matching given criteria.
     *
     * @param callable     $predicate  A predicate function used to determine which element constitutes a match.
     * @param integer|null $startIndex The index to start searching from, or null to use the last index.
     *
     * @return integer|null             The index of the element, or null if is not present in the sequence.
     * @throws Exception\IndexException if $startIndex is out of range.
     */
    public function findLast($predicate, $startIndex = null)
    {
        for ($index = $startIndex; $index >= 0; --$index) {
            if (call_user_func($predicate, $this->elements[$index])) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Insert an element at a particular index.
     *
     * @param integer $index   The index at which the element is inserted, if index is a negative number the element is inserted that far from the end of the sequence.
     * @param mixed   $element The element to insert.
     *
     * @throws Exception\IndexException if $index is out of range.
     */
    public function insert($index, $element)
    {
        $this->insertMany($index, array($element));
    }

    /**
     * Insert a range of elements at a particular index.
     *
     * @param integer      $index    The index at which the elements are inserted, if index is a negative number the elements are inserted that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     */
    public function insertMany($index, $elements)
    {
        $count = count($elements);

        if (0 === $count) {
            return;
        }

        $this->shiftRight($index, $count);
        $this->size += $count;

        foreach ($elements as $element) {
            $this->elements[$index++] = $element;
        }
    }

    /**
     * Remove a range of elements at a given index.
     */
    public function remove($index, $count)
    {
        $this->shiftLeft($index + $count, $count);
        $this->size -= $count;
    }

    /**
     * Replace a range of elements with a second set of elements.
     *
     * Replaces all elements in the range [$begin, $end), i.e. $begin is inclusive, $end is exclusive.
     *
     * @param integer      $index    The index of the first element to replace, if index is a negative number the replace begins that far from the end of the sequence.
     * @param mixed<mixed> $elements The elements to insert.
     * @param integer|null $count    The number of elements to replace, or null to replace all elements up to the end of the sequence.
     */
    public function replace($index, $elements, $count)
    {
        $diff = count($elements) - $count;

        if ($diff > 0) {
            $this->shiftRight($index + $count, $diff);
        } elseif ($diff < 0) {
            $this->shiftLeft($index + $count, abs($diff));
        }

        $this->size += $diff;

        foreach ($elements as $element) {
            $this->elements[$index++] = $element;
        }
    }

    /**
     * Resize the sequence.
     *
     * @param integer $size    The new size of the collection.
     * @param mixed   $element The value to use for populating new elements when $size > $this->size().
     */
    public function resize($size, $element = null)
    {
        if ($this->size > $size) {
            $this->elements->setSize($size);
            $this->size = $size;
        } elseif (null === $element) {
            $this->reserve($size);
            $this->size = $size;
        } else {
            $this->reserve($size);
            while ($this->size < $size) {
                $this->elements[$this->size++] = $element;
            }
        }
    }

    /**
     * @return integer The current reserved capacity of the vector.
     */
    public function capacity()
    {
        return $this->elements->count();
    }

    /**
     * Reserve enough memory to hold at least $size elements.
     *
     * @param integer $size
     */
    public function reserve($size)
    {
        if ($size > $this->capacity()) {
            $this->elements->setSize($size);
        }
    }

    /**
     * Shrink the reserved memory to match the current vector size.
     */
    public function shrink()
    {
        $this->elements->setSize($this->size);
    }

    /**
     * @param integer $index
     * @param integer $count
     */
    private function shiftLeft($index, $count)
    {
        $target = $index - $count;
        $source = $index;

        while ($source < $this->size) {
            $this->elements[$target++] = $this->elements[$source++];
        }

        while ($target < $this->size) {
            $this->elements[$target++] = null;
        }
    }

    /**
     * @param integer $index
     * @param integer $count
     */
    private function shiftRight($index, $count)
    {
        $this->expand($count);

        $source = $this->size - 1;
        $target = $source + $count;

        while ($source >= $index) {
            $this->elements[$target--] = $this->elements[$source--];
        }
    }

    /**
     * @param integer $count
     */
    private function expand($count)
    {
        if ($this->capacity() >= $this->size + $count) {
            return;
        }

        if (0 === $this->size) {
            $capacity = $this->size + $count;
        } else {
            $capacity = $this->capacity();
            $target = $this->size + $count;
            while ($capacity < $target) {
                $capacity <<= 1;
            }
        }
        $this->reserve($capacity);
    }

    private $elements;
    private $size;
}
