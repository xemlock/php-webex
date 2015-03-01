<?php

class Webex_Collection_ResultCollection extends Webex_Collection_Collection
{
    /**
     * @var int
     */
    protected $_total;

    /**
     * @var int
     */
    protected $_offset = 0;

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * @param  int $offset
     * @return Webex_Collection_ResultCollection
     */
    public function setOffset($offset)
    {
        $this->_offset = (int) $offset;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->_total;
    }

    /**
     * @param  int $total
     * @return Webex_Collection_ResultCollection
     */
    public function setTotal($total)
    {
        $this->_total = (int) $total;
        return $this;
    }
}
