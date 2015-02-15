<?php

namespace Graviton\Rql\Visitor;

use Graviton\Rql\AST\OperationInterface;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

class MongoOdm implements VisitorInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function visit(OperationInterface $operation)
    {
        if ($operation->name == 'eq') {
            $this->queryBuilder->field($operation->property)->equals($operation->value);
        } else if ($operation->name == 'ne') {
            $this->queryBuilder->field($operation->property)->notEqual($operation->value);
        }
    }

    /**
     *
     * @return QueryBuilder
     */
    public function getBuilder()
    {
        return $this->queryBuilder;
    }
}
