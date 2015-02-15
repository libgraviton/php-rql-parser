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

    /**
     *
     * @return QueryBuilder
     */
    public function getBuilder()
    {
        return $this->queryBuilder;
    }

    public function visit(OperationInterface $operation)
    {
        if ($operation->name == 'eq') {
            $this->queryBuilder->field($operation->property)->equals($operation->value);
        } else if ($operation->name == 'ne') {
            $this->queryBuilder->field($operation->property)->notEqual($operation->value);
        } else if ($operation->name == 'and') {
            $this->visitQuery('addAnd', $operation);
        } else if ($operation->name == 'or') {
            $this->visitQuery('addOr', $operation);
        } else if ($operation->name == 'lt') {
            $this->queryBuilder->field($operation->property)->lt($operation->value);
        } else if ($operation->name == 'lte') {
            $this->queryBuilder->field($operation->property)->lte($operation->value);
        } else if ($operation->name == 'gt') {
            $this->queryBuilder->field($operation->property)->gt($operation->value);
        } else if ($operation->name == 'gte') {
            $this->queryBuilder->field($operation->property)->gte($operation->value);
        }
    }

    protected function visitQuery($addMethod, OperationInterface $operation) {
        foreach ($operation->queries AS $query) {
            $this->queryBuilder->$addMethod($this->getExpr($query));
        }
    }

    protected function getExpr(OperationInterface $operation) {
        $expr = $this->queryBuilder->expr()->field($operation->property);
        if ($operation->name == 'eq') {
            $expr->equals($operation->value);
        } else if ($operation->name == 'ne') {
            $expr->notEqual($operation->value);
        }
        return $expr;
    }

}
