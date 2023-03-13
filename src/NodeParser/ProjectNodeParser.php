<?php
namespace Graviton\Rql\NodeParser;

use Graviton\RqlParser\NodeParser\SortNodeParser;

class ProjectNodeParser extends SortNodeParser
{
    function getNodeName(): string {
        return 'project';
    }

}
