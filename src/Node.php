<?php
namespace drmad\semeele;

/**
 * XML node representation
 */
class Node
{
    protected $parent;
    protected $children = [];
    
    protected $nodeName;
    protected $attributes = [];
    protected $content;

    /**
     * Constructor
     */
    public function __construct($nodeName, $content = null, $attributes = [])
    {
        if (is_array($content)) {
            $attributes = $content;
            $content = null;
        }

        $this->nodeName = $nodeName;
        $this->content = $content;
        $this->attributes = $attributes;
    }

    /**
     * Adds a child, returns the newly created node
     *
     * @see __construct()
     * @return self Created node
     */
    public function child(...$params) 
    {
        return $this->children[] = (new self(...$params))->setParent($this);
    }

    /**
     * Adds a child, returns this node.
     *
     * @see __construct()
     * @return self This node
     */
    public function add(...$params)
    {
        $this->child(...$params);
        return $this;
    }

    /**
     * Saves the actual node in a variable. 
     *
     * Useful when you're nested deep, and want to 'return' to some
     * point.
     *
     * @param self $node Variable where this node should be saved.
     * @return self This node
     */
    public function save(&$node)
    {
        $node = $this;
        return $this;
    }

    /**
     * Appends a existent child node and all its descendants. Returns
     * this node.
     *
     * @param self $node Node root to append.
     * @return self This node.
     */
    public function append(Node $node)
    {
        $this->children[] = $node->setParent($this);
        return $this;
    }

    /**
     * Adds attributes to this node
     *
     * @param mixed $name Attribute name, or an associative array.
     * @param string $value Attribute value.
     * @return self This node.
     */
    public function attr($name, $value = null)
    {
        if (is_array($name)) {
            $new_attributes = $name;
        } else {
            $new_attributes = [$name => $value];
        }
        $this->attributes += $new_attributes;

        return $this;
    }

    /**
     * Adds a comment child node. Return this node.
     *
     * @param string $text Comment node content.
     * @return self This node.
     */
    public function comment($text)
    {
        $this->children[] = (new Comment($text))->setParent($this);
        return $this;
    }

    /**
     * Returns the parent node.
     * 
     * @return self Parent node.
     */
    public function parent() 
    {
        return $this->parent;
    }

    /**
     * Sets a parent node. Used for maintain the chain in new 
     * inserted nodes.
     */
    protected function setParent(Node $parent) 
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Properly encode a string to include in the XML
     */
    protected function encode($string)
    {
        if ($string instanceof Cdata) {
            return (string)$string;
        } else {
            return htmlspecialchars($string, ENT_COMPAT | ENT_XML1);
        }
    }

    /**
     * Returns a variable="value" sequence from an associative array.
     *
     * @param $attributes Associative array with XML attributes.
     * @return string Attributes string.
     */
    protected function genAttributes(array $attributes)
    {
        $attr = [];
        foreach ($attributes as $name => $value) {
            $attr[] = $name . '="' . $this->encode($value) .'"';
        }
        return join(' ', $attr);
    }

    /**
     * Returns the XML representation of this Node tree
     *
     * @return string XML representation
     */
    public function getXML()
    {
        // Can't have $children and $content
        if ($this->children && $this->content) {
            throw new \RuntimeException("A node can't have both children and content.");
        }

        $has_content = $this->children || $this->content;
        
        $tagname = $this->nodeName;
        if ($this->attributes) {
            $tagname .= ' ' . $this->genAttributes($this->attributes);
        }

        if ($has_content) {
            $xml = '<' . $tagname . '>';

            if ($this->content) {
                $xml .= $this->encode($this->content);
            }
            if ($this->children) {
                foreach ($this->children as $child) {
                    $xml .= $child->getXML();
                }
            }

            $xml .= '</' . $this->nodeName . '>';
        } else {
            $xml = '<' . $tagname . '/>';
        }

        return $xml;
    }

    /**
     * Magic function when this object is used in string context
     */
    public function __toString()
    {
        return $this->getXML();
    }
}
