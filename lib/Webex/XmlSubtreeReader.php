<?php

class Webex_XmlSubtreeReader implements Webex_XmlReaderInterface
{
    /**
     * @var Webex_XmlReader
     */
    protected $_xmlReader;

    protected $_startDepth;
    protected $_startName;

    /**
     * @var bool
     */
    protected $_done;

    /**
     * @param  Webex_XmlReader $reader
     * @throws Exception
     */
    public function __construct(Webex_XmlReader $reader)
    {
        if ($reader->nodeType !== XMLReader::ELEMENT) {
            throw new Exception('XML Reader is in invalid state');
        }
        $this->_startName = $reader->name;
        $this->_startDepth = $reader->depth;
        $this->_xmlReader = $reader;
    }

    public function __destruct()
    {
        $this->_xmlReader = null;
    }

    /**
     * @param  int $nodeType OPTIONAL
     * @return bool
     */
    public function read($nodeType = null)
    {
        if ($this->_done) {
            return false;
        }

        while ($this->_xmlReader->read()) {
            if ($this->_xmlReader->nodeType === XMLReader::END_ELEMENT
                && $this->_xmlReader->depth === $this->_startDepth
                && $this->_xmlReader->name === $this->_startName
            ) {
                // last valid element, no elements will be read in this subtree
                $this->_done = true;
            }
            if ($nodeType === null || $nodeType === $this->_xmlReader->nodeType) {
                return true;
            }
        }

        // no more elements are left to read in this document
        $this->_done = true;
        return false;
    }

    /**
     * @return string
     */
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

    /**
     * @return Webex_XmlSubtreeReader
     * @throws Exception
     */
    public function getSubtree()
    {
        $class = get_class($this);
        return new $class($this->_xmlReader);
    }
}
