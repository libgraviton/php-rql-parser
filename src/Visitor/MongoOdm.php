<?php

namespace Graviton\Rql\Visitor;

use Graviton\Rql\AST\OperationInterface;
use Graviton\Rql\AST\PropertyOperationInterface;
use Graviton\Rql\AST\QueryOperationInterface;
use Graviton\Rql\AST\ArrayOperationInterface;
use Graviton\Rql\AST\LimitOperationInterface;
use Graviton\Rql\AST\SortOperationInterface;
use Graviton\Rql\AST\LikeOperation;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

class MongoOdm implements VisitorInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * map classnames to querybuilder properties
     *
     * @var string<string>
     */
    private $propertyMap = array(
        'Graviton\Rql\AST\EqOperation' => 'equals',
        'Graviton\Rql\AST\NeOperation' => 'notEqual',
        'Graviton\Rql\AST\LtOperation' => 'lt',
        'Graviton\Rql\AST\GtOperation' => 'gt',
        'Graviton\Rql\AST\LteOperation' => 'lte',
        'Graviton\Rql\AST\GteOperation' => 'gte',
    );

    private $arrayMap = array(
        'Graviton\Rql\AST\InOperation' => 'in',
        'Graviton\Rql\AST\OutOperation' => 'notIn',
    );

    /**
     * map classnames of query style operations
     *
     * @var string<string>
     */
    private $queryMap = array(
        'Graviton\Rql\AST\AndOperation' => 'addAnd',
        'Graviton\Rql\AST\OrOperation' => 'addOr',
    );

    /**
     * @var string<string>
     */
    private $internalMap = array(
        'Graviton\Rql\AST\SortOperation' => 'visitSort',
        'Graviton\Rql\AST\LimitOperation' => 'visitLimit',
        'Graviton\Rql\AST\LikeOperation' => 'visitLike',
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

    public function visit(OperationInterface $operation, $expr = false)
    {
        if (in_array(get_class($operation), array_keys($this->internalMap))) {
            $method = $this->internalMap[get_class($operation)];
            $this->$method($operation);

        } elseif ($operation instanceof PropertyOperationInterface) {
            return $this->visitProperty($operation, $expr);

        } elseif ($operation instanceof ArrayOperationInterface) {
            return $this->visitArray($operation, $expr);

        } elseif ($operation instanceof QueryOperationInterface) {
            $method = $this->queryMap[get_class($operation)];
            $this->visitQuery($method, $operation);
        }
    }

    protected function visitProperty(PropertyOperationInterface $operation, $expr)
    {
        $method = $this->propertyMap[get_class($operation)];
        return $this->getField($operation->getProperty(), $expr)->$method($operation->getValue());
    }

    protected function visitArray(ArrayOperationInterface $operation, $expr)
    {
        $method = $this->arrayMap[get_class($operation)];
        return $this->getField($operation->getProperty(), $expr)->$method($operation->getArray());
    }

    protected function getField($field, $expr)
    {
        if ($expr) {
            return $this->queryBuilder->expr()->field($field);
        }
        return $this->queryBuilder->field($field);
    }

    /**
     * @param string $addMethod
     */
    protected function visitQuery($addMethod, QueryOperationInterface $operation)
    {
        foreach ($operation->getQueries() as $query) {
            $this->queryBuilder->$addMethod($this->visit($query, true));
        }
    }

    protected function visitSort(SortOperationInterface $operation)
    {
        foreach ($operation->getFields() as $field) {
            list($name, $order) = $field;
            $this->queryBuilder->sort($name, $order);
        }
    }

    protected function visitLike(LikeOperation $operation)
    {
        $regex = new \MongoRegex(sprintf('/%s/', str_replace('*', '.*', $operation->getValue())));
        $this->queryBuilder->field($operation->getProperty())->equals($regex);
    }

    protected function visitLimit(LimitOperationInterface $operation)
    {
        $this->queryBuilder->limit($operation->getLimit())->skip($operation->getSkip());
    }
}
