<?php
/**
 * ODM MongoDB visitor
 *
 * constrain a mongodb-odm querybuilder based on data from an AST
 */

namespace Graviton\Rql\Visitor;

use Graviton\Rql\QueryBuilderAwareInterface;
use Graviton\Rql\AST;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
final class MongoOdm implements VisitorInterface, QueryBuilderAwareInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * map classes to querybuilder methods
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

    /**
     * map classes to array style methods of querybuilder
     *
     * @var string<string>
     */
    private $arrayMap = array(
        'Graviton\Rql\AST\InOperation' => 'in',
        'Graviton\Rql\AST\OutOperation' => 'notIn',
    );

    /**
     * map classes of query style operations to builder
     *
     * @var string<string>|bool
     */
    private $queryMap = array(
        'Graviton\Rql\AST\AndOperation' => 'addAnd',
        'Graviton\Rql\AST\OrOperation' => 'addOr',
        'Graviton\Rql\AST\QueryOperation' => false,
    );

    /**
     * map classes with an internal implementation to methods
     *
     * @var string<string>
     */
    private $internalMap = array(
        'Graviton\Rql\AST\SortOperation' => 'visitSort',
        'Graviton\Rql\AST\LimitOperation' => 'visitLimit',
        'Graviton\Rql\AST\LikeOperation' => 'visitLike',
    );

    /**
     * create new visitor
     *
     * @param QueryBuilder $queryBuilder MongoDB-ODM querybuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return QueryBuilder
     */
    public function getBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * build a querybuilder from the AST
     *
     * @param AST\OperationInterface $operation AST representation of query
     * @param bool                   $expr      should i wrap this in expr()
     *
     * @return QueryBuilder|Doctrine\ODM\MongoDB\Query\Expr
     */
    public function visit(AST\OperationInterface $operation, $expr = false)
    {
        if (in_array(get_class($operation), array_keys($this->internalMap))) {
            $method = $this->internalMap[get_class($operation)];
            $this->$method($operation);

        } elseif ($operation instanceof AST\PropertyOperationInterface) {
            return $this->visitProperty($operation, $expr);

        } elseif ($operation instanceof AST\ArrayOperationInterface) {
            return $this->visitArray($operation, $expr);

        } elseif ($operation instanceof AST\QueryOperationInterface) {
            $method = $this->queryMap[get_class($operation)];
            return $this->visitQuery($method, $operation, $expr);
        }
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
     * add a property based condition to the querybuilder
     *
     * @param AST\PropertyOperationInterface $operation AST representation of query
     * @param bool                           $expr      should i wrap this in expr()
     *
     * @return mixed
     */
    protected function visitProperty(AST\PropertyOperationInterface $operation, $expr)
    {
        $method = $this->propertyMap[get_class($operation)];
        return $this->getField($operation->getProperty(), $expr)->$method($operation->getValue());
    }

    /**
     * add a array based condition to the querybuilder
     *
     * @param AST\ArrayOperationInterface $operation AST representation of query
     * @param bool                        $expr      should i wrap this in expr()
     *
     * @return QueryBuilder|Doctrine\ODM\MongoDB\Query\Expr
     */
    protected function visitArray(AST\ArrayOperationInterface $operation, $expr)
    {
        $method = $this->arrayMap[get_class($operation)];
        return $this->getField($operation->getProperty(), $expr)->$method($operation->getArray());
    }

    /**
     * get a field condition to add to the querybuilder
     *
     * @param string $field name of field to get
     * @param bool   $expr  should i wrap this in expr()
     *
     * @return QueryBuilder|Doctrine\ODM\MongoDB\Query\Expr
     */
    protected function getField($field, $expr)
    {
        if ($expr) {
            return $this->queryBuilder->expr()->field($field);
        }
        return $this->queryBuilder->field($field);
    }

    /**
     * add query (like and or or) to the querybuilder
     *
     * @param string|boolean              $addMethod name of method we will be calling or false if no method is needed
     * @param AST\QueryOperationInterface $operation AST representation of query operator
     * @param bool                        $expr      should i wrap this in expr()
     *
     * @return QueryBuilder|Doctrine\ODM\MongoDB\Query\Expr
     */
    protected function visitQuery($addMethod, AST\QueryOperationInterface $operation, $expr = false)
    {
        $builder = $this->queryBuilder;
        if ($expr) {
            $builder = $this->queryBuilder->expr();
        }
        foreach ($operation->getQueries() as $query) {
            $expr = $this->visit($query, $addMethod !== false);
            if ($addMethod !== false) {
                $builder->$addMethod($expr);
            }
        }
        return $builder;
    }

    /**
     * add a sort condition to querybuilder
     *
     * @param AST\SortOperationInterface $operation sort operation
     *
     * @return void
     */
    protected function visitSort(AST\SortOperationInterface $operation)
    {
        foreach ($operation->getFields() as $field) {
            $name = $field[0];
            $order = 'asc';
            if (!empty($field[1])) {
                $order = $field[1];
            }
            $this->queryBuilder->sort($name, $order);
        }
    }

    /**
     * add like operation to querybuilder
     *
     * @param AST\Operation $operation like operation
     *
     * @return void
     */
    protected function visitLike(AST\LikeOperation $operation)
    {
        $regex = new \MongoRegex(sprintf('/%s/', str_replace('*', '.*', $operation->getValue())));
        $this->queryBuilder->field($operation->getProperty())->equals($regex);
    }

    /**
     * add limit condition to querybuilder
     *
     * @param AST\LimitOperationInterface $operation limit operation
     *
     * @return void
     */
    protected function visitLimit(AST\LimitOperationInterface $operation)
    {
        $this->queryBuilder->limit($operation->getLimit())->skip($operation->getSkip());
    }
}
