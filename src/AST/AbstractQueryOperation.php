<?php

namespace Graviton\Rql\AST;

abstract class AbstractQueryOperation extends AbstractOperation implements QueryOperationInterface
{
    /**
     * @var OperationInterface[]
     */
    public $queries = array();

    /**
     * @return OperationInterface[]
     */
    public function getQueries()
    {
        return $this->queris;
    }
}
