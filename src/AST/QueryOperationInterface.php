<?php

namespace Graviton\Rql\AST;

interface QueryOperationInterface
{
    /**
     * @param OperationInterface $query query to add
     *
     * @return void
     */
    public function addQuery($query);

    /**
     * @return OperationInterface[]
     */
    public function getQueries();
}
