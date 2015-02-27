<?php

class Webex_Model_Collection implements IteratorAggregate, Countable
{
    /**
     * @var string
     */
    protected $_itemClass;

    /**
     * @var array
     */
    protected $_items;

    /**
     * @param  string $itemClass
     * @return void
     */
    public function __construct($itemClass)
    {
        $this->_itemClass = (string) $itemClass;
        $this->_items = array();
    }

    /**
     * @return string
     */
    public function getItemClass()
    {
        return $this->_itemClass;
    }

    /**
     * @param  object $item
     * @return Webex_Model_Collection
     * @throws InvalidArgumentException
     */
    public function add($item)
    {
        if (!$item instanceof $this->_itemClass) {
            throw new InvalidArgumentException(sprintf(
                'Item must be an instance of %s class', $this->_itemClass
            ));
        }
        $this->_items[] = $item;
        return $this;
    }

    /**
     * @param  object $item
     * @return Webex_Model_Collection
     */
    public function delete($item)
    {
        if ($this->_items) {
            foreach ($this->_items as $key => $value) {
                if ($it === $item) {
                    unset($this->_items[$key]);
                }
            }
        }
        return $this;
    }

    /**
     * @return Webex_Model_Collection
     */
    public function clear()
    {
        $this->_items = array();
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_items);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }
}
