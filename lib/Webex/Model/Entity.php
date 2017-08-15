<?php

abstract class Webex_Model_Entity
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
     * @param  array $data
     * @return Webex_Model_Entity
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $key = Webex_XmlDataExtractor::tagName($key);

            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->{$method}($value);
                continue;
            }

            $method = 'add' . $key;
            if (method_exists($this, $method)) {
                $this->{$method}($value);
                continue;
            }
        }
        return $this;
    }
}
