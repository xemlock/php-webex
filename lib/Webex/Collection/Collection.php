<?php

class Webex_Collection_Collection implements Countable, ArrayAccess, IteratorAggregate
{
    /**
     * @var string|null
     */
    protected $_itemType;

    /**
     * @var array
     */
    protected $_items;

    /**
     * @param string $itemType OPTIONAL
     */
    public function __construct($itemType = null)
    {
        if ($itemType) {
            $this->_setItemType($itemType);
        }
        $this->_items = array();
    }

    /**
     * @param string $itemType
     */
    protected function _setItemType($itemType)
    {
        switch (strtolower($itemType)) {
            case 'bool':
                $itemType = 'boolean';
                break;

            case 'int':
                $itemType = 'integer';
                break;

            case 'float':
                $itemType = 'double';
                break;
        }

        $this->_itemType = (string) $itemType;
    }

    /**
     * @return string
     */
    public function getItemType()
    {
        return $this->_itemType;
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
     * Get array containing all items.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * @param  mixed $item
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function _checkItemType($item)
    {
        if ($this->_itemType &&
            !($item instanceof $this->_itemType || gettype($item) === $this->_itemType)
        ) {
            throw new InvalidArgumentException(sprintf(
                'Item must be of the type %s, %s given',
                $this->_itemType,
                is_object($item) ? get_class($item) : gettype($item)
            ));
        }
        return $item;
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return isset($this->_items[$key]) ? $this->_items[$key] : null;
    }

    /**
     * @param  int $key
     * @param  mixed $value
     * @return void
     * @throws RangeException
     * @throws InvalidArgumentException
     */
    public function offsetSet($key, $value)
    {
        if ($key === null) {
            // [] notation
            return $this->add($value);
        }

        $key = (int) $key;

        // must be within current range
        if ($key < 0 || count($this->_item) <= $key) {
            throw new RangeException('Invalid offset provided');
        }

        $this->_checkItemType($value);
        $this->_items[$key] = $value;
    }

    public function offsetExists($key)
    {
        return isset($this->_items[$key]);
    }

    public function offsetUnset($key)
    {
        $this->delete($key);
    }
}
