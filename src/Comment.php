<?php

namespace Drmad\Semeele;

/**
 * An XML comment
 */
class Comment extends ChildlessNode
{
    public function getXML()
    {
        return '<!-- ' . $this->encode($this->nodeName) . ' -->';
    }
}
