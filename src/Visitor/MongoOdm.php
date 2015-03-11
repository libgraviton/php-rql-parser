<?php
/**
 *
 */
namespace Graviton\Rql\Visitor;

use Graviton\Rql\QueryBuilderAwareInterface;
use Graviton\Rql\AST\OperationInterface;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

/**
 * Class MongoOdm
 *
 * @package Graviton\Rql\Visitor
 */
final class MongoOdm implements VisitorInterface, QueryBuilderAwareInterface
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
        'gte' => 'gte',
        'in' => 'in',
        'out' => 'notIn'
    );

    /**
     * @var string<string>
     */
    private $internalMethods = array(
        'sort' => 'visitSort',
        'like' => 'visitLike',
        'limit' => 'visitLimit',
    );

    /**
     * @param QueryBuilder $queryBuilder Doctrine QueryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param OperationInterface $operation
     */
    public function visit(OperationInterface $operation)
    {
        $name = $operation->getName();

        if (in_array($name, $this->queryOperations)) {
            $this->visitQuery(sprintf('add%s', ucfirst($name)), $operation);
        } elseif (in_array($name, array_keys($this->operationMap))) {
            $method = $this->operationMap[$name];
            $this->queryBuilder->field($operation->getProperty())->$method($operation->getValue());
        } elseif (in_array($name, array_keys($this->internalMethods))) {
            $methodName = $this->internalMethods[$name];
            $this->$methodName($operation);
        }
    }

    /**
     * Provides the  Doctrine QueryBuilder
     *
     * @return QueryBuilder
     */
    public function getBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * Provides the Doctrine Query object to execute.
     *
     * @return \Doctrine\ODM\MongoDB\Query\Query
     */
    public function getQuery()
    {
        return $this->getBuilder()->getQuery();
    }

    /**
     * @param string $addMethod
     */
    protected function visitQuery($addMethod, OperationInterface $operation)
    {
        foreach ($operation->getQueries() as $query) {
            $this->queryBuilder->$addMethod($this->getExpr($query));
        }
    }

    protected function visitSort(OperationInterface $operation)
    {
        foreach ($operation->getFields() as $field) {
            list($name, $order) = $field;
            $this->queryBuilder->sort($name, $order);
        }
    }

    protected function visitLike(OperationInterface $operation)
    {
        $regex = new \MongoRegex(sprintf('/%s/', str_replace('*', '.*', $operation->getValue())));
        $this->queryBuilder->field($operation->getProperty())->equals($regex);
    }

    protected function visitLimit(OperationInterface $operation)
    {
        $fields = $operation->getFields();

        $limit = (int) $fields[0];

        $skip = 0;
        if (!empty($fields[1])) {
            $skip = (int) $fields[1];
        }

        $this->queryBuilder->limit($limit)->skip($skip);
    }

    protected function getExpr(OperationInterface $operation)
    {
        $expr = $this
            ->queryBuilder
            ->expr()
            ->field($operation->getProperty());

        if ($operation->getName() == 'eq') {
            $expr = $expr->equals($operation->getValue());
        } elseif ($operation->getName() == 'ne') {
            $expr = $expr->notEqual($operation->getValue());
        }
        return $expr;
    }
}
