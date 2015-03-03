<?php

class Webex_XmlReader extends XMLReader implements Webex_XmlReaderInterface
{
    /**
     * @return Webex_XmlSubtreeReader
     * @throws Exception
     */
    public function getSubtree()
    {
        return new Webex_XmlSubtreeReader($this);
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
