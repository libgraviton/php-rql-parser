<?php
/**
 * ODM MongoDB visitor
 *
 * constrain a mongodb-odm querybuilder based on data from an AST
 */

namespace Graviton\Rql\Visitor;

use Xiag\Rql\Parser\Query;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\ODM\MongoDB\Query\Expr;
use Graviton\Rql\QueryBuilderAwareInterface;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractLogicOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
final class MongoOdm implements VisitorInterface, QueryBuilderAwareInterface
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * map classes to querybuilder methods
     *
     * @var string<string>
     */
    private $scalarMap = [
        'Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode' => 'equals',
        'Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode' => 'notEqual',
        'Xiag\Rql\Parser\Node\Query\ScalarOperator\LtNode' => 'lt',
        'Xiag\Rql\Parser\Node\Query\ScalarOperator\GtNode' => 'gt',
        'Xiag\Rql\Parser\Node\Query\ScalarOperator\LeNode' => 'lte',
        'Xiag\Rql\Parser\Node\Query\ScalarOperator\GeNode' => 'gte',
    ];

    /**
     * map classes to array style methods of querybuilder
     *
     * @var string<string>
     */
    private $arrayMap = [
        'Xiag\Rql\Parser\Node\Query\ArrayOperator\InNode' => 'in',
        'Xiag\Rql\Parser\Node\Query\ArrayOperator\OutNode' => 'notIn',
    ];

    /**
     * map classes of query style operations to builder
     *
     * @var string<string>|bool
     */
    private $queryMap = [
        'Xiag\Rql\Parser\Node\Query\LogicOperator\AndNode' => 'addAnd',
        'Xiag\Rql\Parser\Node\Query\LogicOperator\OrNode' => 'addOr',
    ];

    /**
     * map classes with an internal implementation to methods
     *
     * @var string<string>
     */
    private $internalMap = [
        'Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode' => 'visitLike',
    ];

    /**
     * create new visitor
     *
     * @param Builder $builder MongoDB-ODM querybuilder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param Query $query query from parser
     *
     * @return Builder|Expr
     */
    public function visit(Query $query)
    {
        return $this->recurse($query);
    }

    /**
     * build a querybuilder from the AST
     *
     * @param Query|Node $query or node
     * @param bool       $expr  wrap in expr?
     *
     * @return Builder|Expr
     */
    private function recurse($query, $expr = false)
    {
        if ($expr) {
            $node = $query;
        } else {
            $node = $query->getQuery();
        }

        if ($query instanceof Query) {
            $this->visitQuery($query);
        }

        if (in_array(get_class($node), array_keys($this->internalMap))) {
            $method = $this->internalMap[get_class($node)];
            return $this->$method($node, $expr);

        } elseif ($node instanceof AbstractScalarOperatorNode) {
            return $this->visitScalar($node, $expr);

        } elseif ($node instanceof AbstractArrayOperatorNode) {
            return $this->visitArray($node, $expr);

        } elseif ($node instanceof AbstractLogicOperatorNode) {
            $method = $this->queryMap[get_class($node)];
            return $this->visitLogic($method, $node, $expr);
        }

        return $this->builder;
    }

    /**
     * @param Query $query top level query that needs visiting
     *
     * @return void
     */
    public function visitQuery(Query $query)
    {
        if ($query->getSort()) {
            $this->visitSort($query->getSort());
        }
        if ($query->getLimit()) {
            $this->visitLimit($query->getLimit());
        }
    }

    /**
     * add a property based condition to the querybuilder
     *
     * @param AbstractScalarOperatorNode $node scalar node
     * @param bool                       $expr should i wrap this in expr()
     *
     * @return Builder|Expr
     */
    protected function visitScalar($node, $expr = false)
    {
        $method = $this->scalarMap[get_class($node)];
        return $this->getField($node->getField(), $expr)->$method($node->getValue());
    }

    /**
     * add a array based condition to the querybuilder
     *
     * @param AbstractArrayOperatorNode $node array node
     * @param bool                      $expr should i wrap this in expr()
     *
     * @return Builder|Expr
     */
    protected function visitArray(AbstractArrayOperatorNode $node, $expr = false)
    {
        $method = $this->arrayMap[get_class($node)];
        return $this->getField($node->getField(), $expr)->$method($node->getValues());
    }

    /**
     * get a field condition to add to the querybuilder
     *
     * @param string $field name of field to get
     * @param bool   $expr  should i wrap this in expr()
     *
     * @return Builder|Expr
     */
    protected function getField($field, $expr)
    {
        if ($expr) {
            return $this->builder->expr()->field($field);
        }
        return $this->builder->field($field);
    }

    /**
     * add query (like and or or) to the querybuilder
     *
     * @param string|boolean            $addMethod name of method we will be calling or false if no method is needed
     * @param AbstractLogicOperatorNode $node      AST representation of query operator
     * @param bool                      $expr      should i wrap this in expr()
     *
     * @return Builder|Expr
     */
    protected function visitLogic($addMethod, AbstractLogicOperatorNode $node, $expr = false)
    {
        $builder = $this->builder;
        if ($expr) {
            $builder = $this->builder->expr();
        }
        foreach ($node->getQueries() as $query) {
            $expr = $this->recurse($query, $addMethod !== false);
            if ($addMethod !== false) {
                $builder->$addMethod($expr);
            }
        }
        return $builder;
    }

    /**
     * add a sort condition to querybuilder
     *
     * @param \Xiag\Rql\Parser\Node\SortNode $node sort node
     *
     * @return void
     */
    protected function visitSort(\Xiag\Rql\Parser\Node\SortNode $node)
    {
        foreach ($node->getFields() as $name => $order) {
            $this->builder->sort($name, $order);
        }
    }

    /**
     * @param \Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode $node like node
     * @param boolean                                             $expr should i wrap this in expr
     *
     * @return void
     */
    protected function visitLike(\Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode $node, $expr = false)
    {
        $query = $node->getValue();
        if ($query instanceof \Xiag\Rql\Parser\DataType\Glob) {
            $query = new \MongoRegex($node->getValue()->toRegex());
        }
        return $this->getField($node->getField(), $expr)->equals($query);
    }

    /**
     * add limit condition to builder
     *
     * @param \Xiag\Rql\Parser\Node\LimitNode $node limit node
     *
     * @return void
     */
    protected function visitLimit(\Xiag\Rql\Parser\Node\LimitNode $node)
    {
        $this->builder->limit($node->getLimit())->skip($node->getOffset());
    }
}
