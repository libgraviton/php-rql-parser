<?php
/**
 * Interface QueryBuilderAwareInterface
 */

namespace Graviton\Rql;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
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
