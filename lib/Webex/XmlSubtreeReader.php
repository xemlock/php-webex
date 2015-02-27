<?php

class Webex_XmlSubtreeReader
{
    protected $_xmlReader;
    protected $_startDepth;
    protected $_startName;
    protected $_done;

    public function __construct(XMLReader $reader)
    {
        if ($reader->nodeType !== XMLReader::ELEMENT) {
            throw new Exception('XML Reader is in invalid state');
        }
        $this->_startName = $reader->name;
        $this->_startDepth = $reader->depth;
        $this->_xmlReader = $reader;
    }

    public function read()
    {
        if ($this->_done) {
            return false;
        }

        if ($this->_xmlReader->read()) {
            if ($this->_xmlReader->nodeType === XMLReader::END_ELEMENT
                && $this->_xmlReader->depth === $this->_startDepth
                && $this->_xmlReader->name === $this->_startName
            ) {
                // last valid element
                $this->_done = true;
            }
            return true;
        }

        // unable to read more from this document
        $this->_done = true;
        return false;
    }

    public function readString()
    {
        if (is_callable(array($this->_xmlReader, 'readString'))) {
            return $this->_xmlReader->readString();
        }
        return ($node = @$this->_xmlReader->expand()) ? $node->nodeValue : '';
    }

    public function expand()
    {
        return $this->_xmlReader->expand();
    }

    public function __get($property)
    {
        return $this->_xmlReader->{$property};
    }
}
