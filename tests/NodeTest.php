<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Drmad\Semeele\Node;

final class NodeTest extends TestCase
{
    public function testSimpleNode()
    {
        $this->assertEquals(
            (string)(new Node('Node')),
            '<Node/>'
        );
    }

    public function testEmptyNode()
    {
        $this->assertEquals(
            (string)(new Node('EmptyNode', '')),
            '<EmptyNode></EmptyNode>'
        );
    }

    public function testZeroValueNode()
    {
        $this->assertEquals(
            (string)(new Node('ZeroNode', 0)),
            '<ZeroNode>0</ZeroNode>'
        );
    }

    public function testEncoding()
    {
        $this->assertEquals(
            (string)(new Node("Encoded", "ñandú"))->setEncoding('iso-8859-1'),
            mb_convert_encoding('<Encoded>ñandú</Encoded>', 'iso-8859-1')
        );
    }
}
