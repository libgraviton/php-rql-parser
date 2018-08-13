<?php
/**
 * event after we visited all nodes
 */

namespace Graviton\Rql\Event;

use Symfony\Component\EventDispatcher\Event;
use Doctrine\ODM\MongoDB\Query\Builder;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Query;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://swisscom.ch
 */
final class VisitPostEvent extends Event
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @param AbstractNode $node    any type of node we are visiting
     * @param Builder      $builder doctrine query builder
     */
    public function __construct(Query $query, Builder $builder)
    {
        $this->query = $query;
        $this->builder = $builder;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param Query $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
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
     * @param Builder $builder replacement query builder
     *
     * @return void
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }
}
