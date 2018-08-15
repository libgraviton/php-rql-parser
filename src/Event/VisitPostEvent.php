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
     * @var string
     */
    private $className;

    /**
     * @param Query   $query     query
     * @param Builder $builder   doctrine query builder
     * @param string  $className class name
     */
    public function __construct(Query $query, Builder $builder, $className = null)
    {
        $this->query = $query;
        $this->builder = $builder;
        $this->className = $className;
    }

    /**
     * @return Query query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param Query $query query
     *
     * @return void
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return Builder builder
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
     * get ClassName
     *
     * @return string ClassName
     */
    public function getClassName()
    {
        return $this->className;
    }
}
