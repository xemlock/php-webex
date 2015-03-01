<?php

/**
 * Class for generic query control.
 */
class Webex_Model_Query
{
    const ORDER_ASC  = 'ASC';
    const ORDER_DESC = 'DESC';

    /**
     * Offset from where the retrieval start
     * @var int
     */
    protected $_offset = 0;

    /**
     * Maximum number of results to be retrieved
     * @var int
     */
    protected $_limit = 0;

    /**
     * @var array
     */
    protected $_orderBy;

    /**
     * Constructor.
     *
     * @param  array $data OPTIONAL
     * @return void
     */
    public function __construct(array $data = null)
    {
        if ($data) {
            $this->setFromArray($data);
        }
    }

    /**
     * @param  array $data
     * @return Webex_Model_Query
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . $key;
            if (is_callable(array($this, $method))) {
                $this->{$method}($value);
            }
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->_offset;
    }

    /**
     * @param  int $offset
     * @return Webex_Model_Query
     */
    public function setOffset($offset)
    {
        $this->_offset = (int) $offset;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * @param  int $limit
     * @return Webex_Model_Query
     */
    public function setLimit($limit)
    {
        $this->_limit = (int) $limit;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        return (array) $this->_orderBy;
    }

    /**
     * @param  array $order
     */
    public function setOrderBy(array $order)
    {
        $newOrder = array();
        foreach ($order as $key => $value) {
            $newOrder[$key] = (string) $value;
        }
        $this->_orderBy = $newOrder;
        return $this;
    }
}
