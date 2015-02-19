<?php

namespace Graviton\Rql\AST;

interface QueryOperationInterface
{
    /**
     * @return OperationInterface[]
     */
    public function getQueries();
}
