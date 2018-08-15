<?php
/**
 * event for visiting single nodes
 */

namespace Graviton\Rql\Event;

use Doctrine\MongoDB\Query\Expr;
use Symfony\Component\EventDispatcher\Event;
use Doctrine\ODM\MongoDB\Query\Builder;
use Xiag\Rql\Parser\AbstractNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://swisscom.ch
 */
final class VisitNodeEvent extends Event
{
    /**
     * @var AbstractNode
     */
    private $node;
    /**
     * @var Builder
     */
    private $builder;
    /**
     * @var \SplStack
     */
    private $context;
    /**
     * @var Expr
     */
    private $exprNode;
    /**
     * @var boolean
     */
    private $expr;
    /**
     * @var string
     */
    private $className;

    /**
     * @param AbstractNode $node      any type of node we are visiting
     * @param Builder      $builder   doctrine query builder
     * @param \SplStack    $context   context
     * @param bool         $expr      if expr is requested or not
     * @param string       $className class name
     */
    public function __construct(
        AbstractNode $node,
        Builder $builder,
        \SplStack $context,
        $expr = false,
        $className = null
    ) {
        $this->node = $node;
        $this->builder = $builder;
        $this->context = $context;
        $this->expr = $expr;
        $this->className = $className;
    }

    /**
     * @return AbstractNode
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * set the current node (or remove it altogether with null)
     *
     * @param AbstractNode|null $node replacement node
     *
     * @return void
     */
    public function setNode(AbstractNode $node = null)
    {
        $this->node = $node;
    }

    /**
     * returns the builder
     *
     * @return Builder|Expr builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * replace builder as needed
     *
     * @param Builder $builder replacement query builder
     *
     * @return void
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * get current context (list of parent nodes)
     *
     * @return \SplStack
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return bool
     */
    public function isExpr()
    {
        return $this->expr;
    }

    /**
     * get ExprNode
     *
     * @return Expr ExprNode
     */
    public function getExprNode()
    {
        return $this->exprNode;
    }

    /**
     * set ExprNode
     *
     * @param Expr $exprNode exprNode
     *
     * @return void
     */
    public function setExprNode($exprNode)
    {
        $this->exprNode = $exprNode;
    }

    /**
     * get ClassName
     *
     * @return string ClassName
     */
    public function getClassName()
    {
        return $this->className;
    }
}
