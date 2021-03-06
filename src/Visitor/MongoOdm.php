<?php
/**
 * ODM MongoDB visitor
 *
 * constrain a mongodb-odm querybuilder based on data from an AST
 */

namespace Graviton\Rql\Visitor;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Graviton\Rql\Event\VisitPostEvent;
use Graviton\Rql\Node\CommentNode;
use MongoDB\BSON\Regex;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ODM\MongoDB\Query\Builder;
use Doctrine\MongoDB\Query\Builder as MongoBuilder;
use Doctrine\MongoDB\Query\Expr as MongoExpr;
use Doctrine\ODM\MongoDB\Query\Expr;
use Graviton\Rql\QueryBuilderAwareInterface;
use Graviton\Rql\Events;
use Graviton\Rql\Event\VisitNodeEvent;
use Graviton\Rql\Node\ElemMatchNode;
use Graviton\RqlParser\AbstractNode;
use Graviton\RqlParser\Glob;
use Graviton\RqlParser\Node\AbstractQueryNode;
use Graviton\RqlParser\Node\DeselectNode;
use Graviton\RqlParser\Node\LimitNode;
use Graviton\RqlParser\Node\Query\AbstractScalarOperatorNode;
use Graviton\RqlParser\Node\Query\AbstractLogicalOperatorNode;
use Graviton\RqlParser\Node\Query\AbstractArrayOperatorNode;
use Graviton\RqlParser\Node\Query\ScalarOperator\LikeNode;
use Graviton\RqlParser\Node\SelectNode;
use Graviton\RqlParser\Query;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://swisscom.ch
 */
final class MongoOdm implements VisitorInterface, QueryBuilderAwareInterface
{

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var DocumentRepository
     */
    private $repository;

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
        'Graviton\RqlParser\Node\Query\ScalarOperator\EqNode' => 'equals',
        'Graviton\RqlParser\Node\Query\ScalarOperator\NeNode' => 'notEqual',
        'Graviton\RqlParser\Node\Query\ScalarOperator\LtNode' => 'lt',
        'Graviton\RqlParser\Node\Query\ScalarOperator\GtNode' => 'gt',
        'Graviton\RqlParser\Node\Query\ScalarOperator\LeNode' => 'lte',
        'Graviton\RqlParser\Node\Query\ScalarOperator\GeNode' => 'gte',
    ];

    /**
     * map classes to array style methods of querybuilder
     *
     * @var string<string>
     */
    private $arrayMap = [
        'Graviton\RqlParser\Node\Query\ArrayOperator\InNode' => 'in',
        'Graviton\RqlParser\Node\Query\ArrayOperator\OutNode' => 'notIn',
    ];

    /**
     * map classes of query style operations to builder
     *
     * @var string<string>|bool
     */
    private $queryMap = [
        'Graviton\RqlParser\Node\Query\LogicalOperator\AndNode' => 'addAnd',
        'Graviton\RqlParser\Node\Query\LogicalOperator\OrNode' => 'addOr',
    ];

    /**
     * map classes with an internal implementation to methods
     *
     * @var array<string>
     */
    private $internalMap = [
        'Graviton\RqlParser\Node\Query\ScalarOperator\LikeNode' => 'visitLike',
        'Graviton\Rql\Node\ElemMatchNode' => 'visitElemMatch',
        'Graviton\Rql\Node\CommentNode' => 'visitComment'
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
     * sets repository
     *
     * @param DocumentRepository $repository repository
     *
     * @return void
     */
    public function setRepository(DocumentRepository $repository)
    {
        $this->repository = $repository;
        $this->setBuilder($this->repository->createQueryBuilder());
    }

    /**
     * returns repository
     *
     * @return DocumentRepository repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Query $query query from parser
     *
     * @return Builder|Expr
     */
    public function visit(Query $query)
    {
        $this->context = new \SplStack();

        $this->builder = $this->recurse($query);

        // event after we did all..
        list($query, $this->builder) = $this->dispatchVisitPostEvent($query);

        return $this->builder;
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
        list($node, $this->builder, $exprNode) = $this->dispatchNodeEvent($node, $expr);

        if ($query instanceof Query) {
            $this->visitQuery($query);
        }

        $this->context->push($originalNode);

        if ($exprNode instanceof Expr) {
            // make sure that if the event sets an expr node, that it overrides the builder here
            $builder = $exprNode;
        } elseif (is_object($node) && in_array(get_class($node), array_keys($this->internalMap))) {
            $method = $this->internalMap[get_class($node)];
            $builder = $this->$method($node, $expr);
        } elseif ($node instanceof AbstractScalarOperatorNode) {
            $builder = $this->visitScalar($node, $expr);
        } elseif ($node instanceof AbstractArrayOperatorNode) {
            $builder = $this->visitArray($node, $expr);
        } elseif ($node instanceof AbstractLogicalOperatorNode) {
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
     * @param boolean           $expr if expr is requested or not
     *
     * @return array
     */
    private function dispatchNodeEvent(AbstractNode $node = null, $expr = false)
    {
        $builder = $this->builder;
        $exprNode = null;
        if (!empty($this->dispatcher)) {
            if ($node instanceof AbstractQueryNode) {
                /** @var VisitNodeEvent $event */
                $event = $this->dispatcher
                    ->dispatch(
                        new VisitNodeEvent(
                            $node,
                            $this->builder,
                            $this->context,
                            $expr,
                            $this->repository->getClassName()
                        ),
                        Events::VISIT_NODE
                    );
                $node = $event->getNode();
                $builder = $event->getBuilder();
                $exprNode = $event->getExprNode();
            }
        }
        return [$node, $builder, $exprNode];
    }

    /**
     * @param Query|null $query the query
     *
     * @return array
     */
    private function dispatchVisitPostEvent(Query $query = null)
    {
        $builder = $this->builder;
        if (!empty($this->dispatcher)) {
            /** @var VisitPostEvent $event */
            $event = $this->dispatcher
                ->dispatch(
                    new VisitPostEvent($query, $this->builder, $this->repository),
                    Events::VISIT_POST
                );
            $query = $event->getQuery();
            $builder = $event->getBuilder();

            // override query builder with aggregation?
            $aggregationBuilder = $event->getAggregationOverride();
            if (!is_null($aggregationBuilder)) {
                $builder = $aggregationBuilder;
            }
        }
        return [$query, $builder];
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
        if ($query->getDeselect()) {
            $this->visitDeselect($query->getDeselect());
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
     * @param AbstractLogicalOperatorNode $node      AST representation of query operator
     * @param bool                      $expr      should i wrap this in expr()
     *
     * @return Builder|Expr
     */
    private function visitLogic($addMethod, AbstractLogicalOperatorNode $node, $expr = false)
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
     * @param \Graviton\RqlParser\Node\SortNode $node sort node
     *
     * @return void
     */
    private function visitSort(\Graviton\RqlParser\Node\SortNode $node)
    {
        foreach ($node->getFields() as $name => $order) {
            $this->builder->sort($name, $order);
        }
        return $this->builder;
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
        if ($query instanceof Glob) {
            $query = new Regex($node->getValue()->toRegex());
        }
        return $this->getField($node->getField(), $expr)->equals($query);
    }

    /**
     * @param CommentNode $node node
     * @param boolean     $expr should i wrap this in expr
     *
     * @return MongoBuilder||Expr
     */
    private function visitComment(CommentNode $node, $expr = false)
    {
        $builder = $this->builder->expr();
        $builder->comment($node->getComment());
        return $builder;
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
     * Visit deselect() node
     *
     * @param DeselectNode $node node
     * @param bool         $expr expr
     *
     * @return void
     */
    private function visitDeselect(DeselectNode $node, $expr = false)
    {
        array_map(
            function ($field) {
                $this->builder->exclude($this->cleanupSingleFieldName($field));
            },
            $node->getFields()
        );
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
                $this->builder->select($this->cleanupSingleFieldName($field));
            },
            $node->getFields()
        );
    }

    /**
     * cleans up things we need to change before send to mongo
     *
     * @param string $fieldName fieldname
     *
     * @return string fieldname
     */
    private function cleanupSingleFieldName($fieldName)
    {
        return str_replace('$ref', 'ref', $fieldName);
    }
}
