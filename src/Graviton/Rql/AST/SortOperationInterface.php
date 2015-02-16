<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

interface SortOperationInterface
{
    /**
     * @return array<string[]>
     */
    public function getFields();
}
