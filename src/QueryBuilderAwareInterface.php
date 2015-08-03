<?php
/**
 * Interface QueryBuilderAwareInterface
 */

namespace Graviton\Rql;

use Doctrine\ODM\MongoDB\Query\Builder;

/**
 * @author  List of contributors <https://github.com/libgraviton/php-rql-parser/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
interface QueryBuilderAwareInterface
{
    /**
     * @param Builder $builder query builder
     *
     * @return void
     */
    public function setBuilder(Builder $builder);

    /**
     * Provides the  Doctrine QueryBuilder
     *
     * @return Builder
     */
    public function getBuilder();
}
