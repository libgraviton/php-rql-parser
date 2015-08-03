<?php
/**
 * event for visiting single nodes
 */

namespace Graviton\Rql\Event;

use Symfony\Component\EventDispatcher\Event;
use Doctrine\ODM\MongoDB\Query\Builder;
use Xiag\Rql\Parser\AbstractNode;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
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
     * @param AbstracNode $node    any type of node we are visiting
     * @param Builder     $builder doctrine query builder
     */
    public function __construct(AbstractNode $node, Builder $builder)
    {
        $this->node = $node;
        $this->builder = $builder;
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
     * @return Builder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * replace builder as needed
     *
     * @param Builder builder buidler
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }
}
