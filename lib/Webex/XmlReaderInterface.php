<?php

interface Webex_XmlReaderInterface
{
    public function read();

    public function readString();

    public function getSubtree();
}
