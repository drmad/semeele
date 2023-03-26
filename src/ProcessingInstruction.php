<?php

namespace Drmad\Semeele;

/**
 * XML Processing instruction
 */
class ProcessingInstruction extends ChildlessNode
{
    public function getXML()
    {
        $pi = '<?' . $this->nodeName;
        $attributes = $this->genAttributes($this->attributes);
        if ($attributes) {
            $pi .= ' ' . $attributes;
        }
        $pi .= '?>';

        return $pi;
    }
}