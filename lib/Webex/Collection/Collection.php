<?php

class Webex_Collection_Collection implements Countable, IteratorAggregate
{
    /**
     * @var string|null
     */
    protected $_itemClass;

    /**
     * @var array
     */
    protected $_items;

    /**
     * @param  string $itemClass OPTIONAL
     * @return void
     */
    public function __construct($itemClass = null)
    {
        if ($itemClass) {
            $this->_itemClass = (string) $itemClass;
        }
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
     * @param  mixed $item
     * @return Webex_Collection_Collection
     * @throws InvalidArgumentException
     */
    public function add($item)
    {
        $this->_items[] = $this->_checkItemType($item);
        return $this;
    }

    /**
     * @param  int $key
     * @return object|null
     */
    public function delete($key)
    {
        if (!isset($this->_items[$key]) && !array_key_exists($key, $this->_items)) {
            return null;
        }

        $item = $this->_items[$key];
        unset($this->_items[$key]);

        return $item;
    }

    /**
     * @param  object $item
     * @return bool
     */
    public function deleteItem($item)
    {
        $key = array_search($item, $this->_items, true);

        if ($key === false) {
            return false;
        }

        unset($this->_items[$key]);
        return true;
    }

    /**
     * @return Webex_Collection_Collection
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

    /**
     * @param  string $value
     * @return void
     * @throws InvalidArgumentException
     */
    protected function _checkItemType($item)
    {
        if (!is_object($item)) {
            throw new InvalidArgumentException('Item must be an object');
        }
        if ($this->_itemClass && !$item instanceof $this->_itemClass) {
            throw new InvalidArgumentException(sprintf(
                'Item must be an instance of %s class', $this->_itemClass
            ));
        }
        return $item;
    }
}