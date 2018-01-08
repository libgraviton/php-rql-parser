<?php
/**
 * ODM MongoDB visitor
 *
 * constrain a mongodb-odm querybuilder based on data from an AST
 */

namespace Graviton\Rql\Visitor;

use Graviton\Rql\Node\SearchNode;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\MongoDB\Query\Builder as MongoBuilder;
use Doctrine\MongoDB\Query\Expr as MongoExpr;
use Doctrine\ODM\MongoDB\Query\Expr;
use Graviton\Rql\QueryBuilderAwareInterface;
use Graviton\Rql\Events;
use Graviton\Rql\Event\VisitNodeEvent;
use Graviton\Rql\Node\ElemMatchNode;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractLogicOperatorNode;
use Xiag\Rql\Parser\Node\Query\AbstractArrayOperatorNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Query;

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
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;
    /**
     * @var \SplStack
     */
    private $context;

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
     * @var array<string>
     */
    private $internalMap = [
        'Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode' => 'visitLike',
        'Graviton\Rql\Node\SearchNode' => 'visitSearch',
        'Graviton\Rql\Node\ElemMatchNode' => 'visitElemMatch',
    ];

    /**
     * inject an optional event dispatcher
     *
     * If injected this is used to dispatch some lifecycle events that you may use
     * to hook into query visitation
     *
     * @param EventDispatcherInterface $dispatcher event dispatcher to dispatch events on
     *
     * @return void
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Builder $builder query builder
     *
     * @return void
     */
    public function setBuilder(Builder $builder)
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
        $this->context = new \SplStack();
        return $this->recurse($query);
    }

    /**
     * build a querybuilder from the AST
     *
     * @param Query|AbstractNode $query or node
     * @param bool               $expr  wrap in expr?
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

        $originalNode = $node;
        list($node, $this->builder) = $this->dispatchNodeEvent($node);

        if ($query instanceof Query) {
            $this->visitQuery($query);
        }

        $this->context->push($originalNode);
        if (is_object($node) && in_array(get_class($node), array_keys($this->internalMap))) {
            $method = $this->internalMap[get_class($node)];
            $builder = $this->$method($node, $expr);
        } elseif ($node instanceof AbstractScalarOperatorNode) {
            $builder = $this->visitScalar($node, $expr);
        } elseif ($node instanceof AbstractArrayOperatorNode) {
            $builder = $this->visitArray($node, $expr);
        } elseif ($node instanceof AbstractLogicOperatorNode) {
            $method = $this->queryMap[get_class($node)];
            $builder = $this->visitLogic($method, $node, $expr);
        } else {
            $builder = $this->builder;
        }
        $this->context->pop();

        return $builder;
    }

    /**
     * @param AbstractNode|null $node node at the center of the event
     *
     * @return array
     */
    private function dispatchNodeEvent(AbstractNode $node = null)
    {
        $builder = $this->builder;
        if (!empty($this->dispatcher)) {
            if ($node instanceof AbstractQueryNode) {
                /** @var VisitNodeEvent $event */
                $event = $this->dispatcher
                    ->dispatch(
                        Events::VISIT_NODE,
                        new VisitNodeEvent($node, $this->builder, $this->context)
                    );
                $node = $event->getNode();
                $builder = $event->getBuilder();
            }
        }
        return [$node, $builder];
    }

    /**
     * @param Query $query top level query that needs visiting
     *
     * @return void
     */
    private function visitQuery(Query $query)
    {
        if ($query->getSort()) {
            $this->visitSort($query->getSort());
        }
        if ($query->getLimit()) {
            $this->visitLimit($query->getLimit());
        }
        if ($query->getSelect()) {
            $this->visitSelect($query->getSelect());
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
    private function visitScalar($node, $expr = false)
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
    private function visitArray(AbstractArrayOperatorNode $node, $expr = false)
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
     * @return MongoBuilder|MongoExpr
     */
    private function getField($field, $expr)
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
    private function visitLogic($addMethod, AbstractLogicOperatorNode $node, $expr = false)
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
    private function visitSort(\Xiag\Rql\Parser\Node\SortNode $node)
    {
        foreach ($node->getFields() as $name => $order) {
            $this->builder->sort($name, $order);
        }
    }

    /**
     * @param LikeNode $node like node
     * @param boolean  $expr should i wrap this in expr
     *
     * @return MongoBuilder||Expr
     */
    private function visitLike(LikeNode $node, $expr = false)
    {
        $query = $node->getValue();
        if ($query instanceof \Xiag\Rql\Parser\DataType\Glob) {
            $query = new \MongoRegex($node->getValue()->toRegex());
        }
        return $this->getField($node->getField(), $expr)->equals($query);
    }

    /**
     * Visit elemMatch() node
     *
     * @param ElemMatchNode $node elemMatch() node
     * @param bool          $expr should i wrap this in expr()
     * @return MongoBuilder|MongoExpr
     */
    private function visitElemMatch(ElemMatchNode $node, $expr = false)
    {
        return $this
            ->getField($node->getField(), $expr)
            ->elemMatch($this->recurse($node->getQuery(), true));
    }

    /**
     * Visit visitSearch() node
     *
     * @param SearchNode $node elemMatch() node
     * @param bool       $expr should i wrap this in expr()
     * @return MongoBuilder|MongoExpr
     */
    private function visitSearch(SearchNode $node, $expr = false)
    {
        if ($node->isVisited()) {
            if ($expr) {
                return $this->builder->expr()->addAnd($this->builder->expr()->field('_id')->exists(true));
            } else {
                return $this->builder;
            }
        }
        $node->setVisited(true);
        $searchArr = [];
        foreach ($node->getSearchTerms() as $string) {
            $searchArr[] = "\"{$string}\"";
        }

        $this->builder->sortMeta('score', 'textScore');
        if ($expr) {
            return $this->builder->expr()->text(implode(' ', $searchArr));
        } else {
            return $this->builder->addAnd($this->builder->expr()->text(implode(' ', $searchArr)));
        }
    }

    /**
     * add limit condition to builder
     *
     * @param LimitNode $node limit node
     *
     * @return void
     */
    private function visitLimit(LimitNode $node)
    {
        $this->builder->limit($node->getLimit())->skip($node->getOffset());
    }

    /**
     * add selects to builder
     *
     * @param SelectNode $node select node
     *
     * @return void
     */
    private function visitSelect(SelectNode $node)
    {
        array_map(
            function ($field) {
                // rename '$ref' to 'ref', else we cannot select them..
                $field = str_replace('$ref', 'ref', $field);
                $this->builder->select($field);
            },
            $node->getFields()
        );
    }
}
