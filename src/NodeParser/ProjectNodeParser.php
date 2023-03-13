<?php
namespace Graviton\Rql\NodeParser;

use Graviton\Rql\Node\ProjectNode;
use Graviton\RqlParser\AbstractNode;
use Graviton\RqlParser\NodeParser\SortNodeParser;

class ProjectNodeParser extends SortNodeParser
{
    function getNodeName(): string {
        return 'project';
    }

    function getNode(array $fields) : AbstractNode {
        return new ProjectNode($fields);
    }

}
