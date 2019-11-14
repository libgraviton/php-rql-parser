<?php
/**
 * event after we visited all nodes
 */

namespace Graviton\Rql\Event;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Symfony\Contracts\EventDispatcher\Event;
use Doctrine\ODM\MongoDB\Query\Builder;
use Graviton\RqlParser\Query;

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
     * @var DocumentRepository
     */
    private $repository;

    /**
     * @var \Doctrine\MongoDB\Aggregation\Builder
     */
    private $aggregationOverride = null;

    /**
     * @var string
     */
    private $className;

    /**
     * @param Query              $query      query
     * @param Builder            $builder    doctrine query builder
     * @param DocumentRepository $repository repository
     * @param string             $className  class name
     */
    public function __construct(Query $query, Builder $builder, DocumentRepository $repository, $className = null)
    {
        $this->query = $query;
        $this->builder = $builder;
        $this->repository = $repository;
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
     * @return DocumentRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return \Doctrine\MongoDB\Aggregation\Builder
     */
    public function getAggregationOverride()
    {
        return $this->aggregationOverride;
    }

    /**
     * @param \Doctrine\MongoDB\Aggregation\Builder $aggregationOverride override
     *
     * @return void
     */
    public function setAggregationOverride($aggregationOverride)
    {
        $this->aggregationOverride = $aggregationOverride;
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
