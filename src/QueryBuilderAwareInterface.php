<?php

namespace Graviton\Rql;

/**
 * Interface QueryBuilderAwareInterface
 *
 * @category Graviton
 * @package  Rql
 * @author   Dario Nuevo <dario.nuevo@swisscom.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.com
 */
interface QueryBuilderAwareInterface
{
    /**
     * Provides the  Doctrine QueryBuilder
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getBuilder();

    /**
     * Provides the Doctrine Query object to execute.
     *
     * @return \Doctrine\ODM\MongoDB\Query\Query
     */
    public function getQuery();
}
