<?php

namespace Graviton\Rql\AST;

abstract class AbstractQueryOperation extends AbstractOperation implements QueryOperationInterface
{
    /**
     * @var OperationInterface[]
     */
    private $queries = array();

    /**
     * @param OperationInterface $query query to add
     *
     * @return void
     */
    public function addQuery($query)
    {
        $this->queries[] = $query;
    }

    /**
     * @return OperationInterface[]
     */
    public function getQueries()
    {
        return $this->queries;
    }
}
