<?php

class Webex_XmlDataExtractor
{
    public static function tagName($tagName)
    {
        if (($pos = strrpos($tagName, "\x00")) !== false) {
            return substr($tagName, 0, $pos);
        }

        return $tagName;
    }

    /**
     * @param DOMNode|SimpleXMLElement $node
     * @return array|string
     */
    public function xmlToArray($node)
    {
        if ($node instanceof SimpleXMLElement) {
            $node = dom_import_simplexml($node);
        }

        if ($node->nodeType === XML_TEXT_NODE) {
            return $node->nodeValue;
        }

        if ($node->nodeType === XML_ELEMENT_NODE) {
            $array = array();
            $typeCounters = array();
            $textContent = '';

            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE) {
                    $textContent .= $child->nodeValue;
                    continue;
                }

                $nodeName = $child->nodeName;
                $nodeNameWithoutNS = preg_replace('/^.*?:/', '', $nodeName);

                $childArray = $this->xmlToArray($child);
                $key = $nodeNameWithoutNS;

                if (!isset($array[$nodeNameWithoutNS])) {
                    $typeCounters[$nodeNameWithoutNS] = 0;
                } else {
                    $key .= "\x00" . ++$typeCounters[$nodeNameWithoutNS];
                }

                $array[$key] = $childArray;
            }

            if (strlen($textContent)) {
                if (strtoupper($textContent) === 'TRUE') {
                    return true;
                }
                if (strtoupper($textContent) === 'FALSE') {
                    return false;
                }

                return $textContent;
            }

            return $array;
        }

        return null;
    }
}
