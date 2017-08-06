<?php

abstract class Webex_Type_Abstract
{
    /**
     * @param array $data OPTIONAL
     */
    public function __construct(array $data = null)
    {
        if ($data) {
            $this->setFromArray($data);
        }
    }

    /**
     * @param array $data
     * @return Webex_Type_Abstract
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
        return $this;
    }
}
