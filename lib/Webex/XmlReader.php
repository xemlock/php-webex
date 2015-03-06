<?php

class Webex_XmlReader extends XMLReader implements Webex_XmlReaderInterface
{
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @return Webex_XmlSubtreeReader
     * @throws Exception
     */
    public function getSubtree()
    {
        return new Webex_XmlSubtreeReader($this);
    }

    /**
     * @param  int $nodeType OPTIONAL
     * @return bool
     */
    public function read($nodeType = null)
    {
        if ($nodeType === null) {
            return parent::read();
        }
        while (parent::read()) {
            if ($this->nodeType === $nodeType) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function readString()
    {
        // readString() function is only available when PHP is compiled
        // against libxml 20620 or later.
        if (is_callable('parent::readString')) {
            return parent::readString();
        }
        return ($node = @$this->expand()) ? $node->nodeValue : '';
    }
}
