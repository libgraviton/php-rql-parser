<?php

namespace Graviton\Rql\AST;

use Graviton\Rql\Visitor\VisitorInterface;

interface QueryOperationInterface
{
    /**
     * @return OperationInterface[]
     */
    public function getQueries();
}
