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

    /**
     * @var string[]
     */
    private $queryOperations = array(
        'and',
        'or'
    );

    /**
     * @var string[]
     */
    private $operationMap = array(
        'eq' => 'equals',
        'ne' => 'notEqual',
        'lt' => 'lt',
        'gt' => 'gt',
        'lte' => 'lte',
        'gte' => 'gte'
    );

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
        $name = $operation->name;

        if (in_array($name, $this->queryOperations)) {
            $this->visitQuery(sprintf('add%s', ucfirst($name)), $operation);
        } elseif (in_array($name, array_keys($this->operationMap))) {
            $method = $this->operationMap[$name];
            $this->queryBuilder->field($operation->property)->$method($operation->value);
        } elseif ($name == 'sort') {
            $this->visitSort($operation);
        }
    }

    protected function visitQuery($addMethod, OperationInterface $operation)
    {
        foreach ($operation->queries as $query) {
            $this->queryBuilder->$addMethod($this->getExpr($query));
        }
    }

    protected function visitSort(OperationInterface $operation)
    {
        foreach ($operation->fields as $field) {
            list($name, $order) = $field;
            $this->queryBuilder->sort($name, $order);
        }
    }

    protected function getExpr(OperationInterface $operation)
    {
        $expr = $this
            ->queryBuilder
            ->expr()
            ->field($operation->property);

        if ($operation->name == 'eq') {
            $expr = $expr->equals($operation->value);
        } elseif ($operation->name == 'ne') {
            $expr = $expr->notEqual($operation->value);
        }
        return $expr;
    }
}
